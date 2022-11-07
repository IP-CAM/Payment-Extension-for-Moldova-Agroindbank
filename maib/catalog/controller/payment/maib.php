<?php

namespace Opencart\Catalog\Controller\Extension\Maib\Payment;

class Maib extends \Opencart\System\Engine\Controller {
	use MaibTrait;

	public function index() {
		$data = array();
		$this->load->language('extension/maib/payment/maib');
		$this->load->model('localisation/currency');
		$this->load->model('checkout/order');

		$currency_codes = array(
			'USD' => 840,
			'EUR' => 978,
			'MDL' => 498
		);

		if (empty($this->session->data['order_id'])) {
			throw new \Exception('Order id missing');
		}

		$order_info = $this->model_checkout_order
			->getOrder($this->session->data['order_id']);
		$language = $this->model_localisation_language
			->getLanguage($order_info['language_id']);

		if (!isset($currency_codes[$order_info['currency_code']])) {
			return '<div class="alert alert-warning warning">' .
				$this->language->get('error_no_currency') . '</div>';
		}

		try {
			$client = $this->getMaibClient();

			$user_ip = $this->getUserIp();
			$amount = round($order_info['total'] * $order_info['currency_value'], 2);
			$currency_code = $currency_codes[$order_info['currency_code']];
			$language_code = isset($language['code']) ? substr($language['code'], 0, 2) : 'en';
			$description = 'Order #' . $order_info['order_id'];

			$transaction = $client
				->registerSmsTransaction($amount, $currency_code, $user_ip, $description, $language_code);

			if (isset($transaction['TRANSACTION_ID'])) {
				$data['transaction_id'] = $transaction['TRANSACTION_ID'];
				$this->session->data['transaction_id'] = $transaction['TRANSACTION_ID'];
				$data['action_url'] = $this->getRedirectUrl();
				$this->db->query("INSERT INTO " . DB_PREFIX . "maib_transaction SET
					transaction_id = '" . $this->db->escape($transaction['TRANSACTION_ID']) . "',
					order_id = '" . (int)$order_info['order_id'] . "',
					date_added = '" . date('Y-m-d H:i:s') . "'");
				$this->log(strtr('New transaction @transid for order @orderid', [
					'@transid' => $transaction['TRANSACTION_ID'],
					'@orderid' => $order_info['order_id'],
				]));
			}
			else {
				throw new \Exception('No transaction_id ' . print_r($transaction, true));
			}
		}
		catch (\Exception $e) {
			$this->log('Register new transaction - ' . $e->getMessage(), 'Error');
			return '<div class="alert alert-warning warning">' . $this->language->get('error_no_transaction_id') . '</div>';
		}

		$data['button_confirm'] = $this->language->get('button_confirm');
		$template = 'extension/maib/payment/maib';
		return $this->load->view($template, $data);
	}

public function addOrderHistoryBefore($route, &$data) {
        $order_id = $data[0];
        $order_status_id = $data[1];
        $comment = ($data[2] ?? '');
        $notify = ($data[2] ?? false);
        $override = ($data[2] ?? false);

        if ($order_status_id == 12) {
            $this->load->model('checkout/order');

            $order_info = $this->model_checkout_order->getOrder($order_id);

            if ($order_info) {
                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_history WHERE order_id = '{$order_id}' AND comment LIKE '%MAIB-OK/TRANS_ID%'");

                if ($query->num_rows) {
                    $str = $query->row['comment'];
                    $explode = explode(":", $str);
                    if (isset($explode[1])) {
                        list($key, $trans_id) = $explode;
					
					$amount = round($order_info['total'] * $order_info['currency_value'], 2);

					try {
					$client = $this->getMaibClient();	
					$reverse = $client->revertTransaction($trans_id, $amount);
			
					if (isset($reverse['RESULT']) && $reverse['RESULT'] == 'OK') {
					$this->log('Transaction full reversed!');
                    
                    $order_status_id = 12;
					$comment = "MAIB-REVERSAL-OK/TRANS_ID:" . $trans_id;
					$notify = 1;
					$this->log(strtr('Full reversal transaction: @transid for order @orderid', [
						'@transid' => $trans_id,
						'@orderid' => $order_id,
					]));
					
					$query = $this->db->query("UPDATE " . DB_PREFIX . "order set total = '0' WHERE order_id = '{$order_id}'");
					$query = $this->db->query("UPDATE " . DB_PREFIX . "order_total set value = '0', title = 'Total (reversed)' WHERE order_id = '{$order_id}' AND code = 'total'");
				
			}
			else {
			throw new \Exception('Reversal failed - ' . print_r($reverse, true));
			}
		}
	    catch (\Exception $e) {
	    $order_status_id = 9;
		$comment = "MAIB-REVERSAL/FAILLED";
		$notify = 0;
		$this->log('' . $e->getMessage(), 'Error');
		//echo 'Reversal failed error: ' . $e->getMessage();
		}		
                    }
                }
            }
        
		$data[0] = $order_id;
        $data[1] = $order_status_id;
        $data[2] = $comment;
        $data[3] = $notify;
        $data[4] = $override;
		}
		
       
    }
}
