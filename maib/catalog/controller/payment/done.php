<?php

namespace Opencart\Catalog\Controller\Extension\Maib\Payment;

class Done extends Maib {
	use MaibTrait;

	public function index(): void {
		$this->language->load('extension/maib/payment/maib');
		$this->load->model('checkout/order');

		$tries = 2;
		$post_transaction_id = null;
		$order_id = empty($this->session->data['order_id']) ? null :
			'_SESSION_' . $this->session->data['order_id'];
		try {
			if (empty($this->request->request['trans_id'])) {
				throw new \Exception('Missing TRANSACTION_ID');
			}
			$post_transaction_id = $this->request->request['trans_id'];
			$order_query = $this->db->query("SELECT order_id FROM " . DB_PREFIX . "maib_transaction
				WHERE transaction_id = '" . $this->db->escape($post_transaction_id) . "'");
			if (empty($order_query->row['order_id'])) {
				throw new \Exception('Order ID not found in transactions table');
			}
			$order_id = $order_query->row['order_id'];
			$user_ip = $this->getUserIp();
			$client = $this->getMaibClient();

			while ($tries--) {
				$tres = $client->getTransactionResult($post_transaction_id, $user_ip);
				if ($tres['RESULT'] == 'OK') {
					$this->model_checkout_order
						->addHistory($order_id, $this->config->get('payment_maib_order_status_id'), "MAIB-OK/TRANS_ID:$post_transaction_id/RRN:" . $tres['RRN'], true);
					$this->log(strtr('Confirmed transaction @transid RRN @rrn for order @orderid', [
						'@transid' => $post_transaction_id,
						'@rrn' => $tres['RRN'],
						'@orderid' => $order_id,
					]));
					$this->response
						->redirect($this->url->link('checkout/success', '', 'SSL'));
					break;
				}
				elseif ($tres['RESULT'] == 'PENDING') {
					sleep(5);
					continue;
				}
				else {
					throw new \Exception('Invalid transaction response, ' . print_r($tres, true));
				}
			}
			if ($tres['RESULT'] == 'PENDING') {
				$log_message = 'Failed to confirm payment transaction status'
					. ', still in pending for order ' . $order_id
					. ', trans_id ' . $post_transaction_id
					. ', ' . print_r($tres, true);
				$this->log($log_message, 'Error');
				$this->session->data['error'] = $this->language->get('text_maib_pending');
				$this->model_checkout_order
					->addHistory($order_id, $this->config->get('payment_maib_order_pending_status_id'), "MAIB-PENDING/TRANS_ID:$post_transaction_id/RRN:-", true);
				$this->response
					->redirect($this->url->link('checkout/success', '', 'SSL'));
			}
			else {
				throw new \Exception('Failed to verify transaction status, ' . print_r($tres, true));
			}
		}
		catch (\Exception $e) {
			$log_message = 'Failed payment with exception for order ' . $order_id . ': ' . $e->getMessage();
			$this->log($log_message, 'Error');
			$this->session->data['error'] = $this->language->get('error_no_payment');
			$this->response->redirect($this->url->link('checkout/checkout', '', 'SSL'));
		}
	}

}
