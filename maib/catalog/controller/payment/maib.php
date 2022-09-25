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

}
