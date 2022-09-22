<?php

require_once(DIR_SYSTEM . 'library/maib/vendor/autoload.php');

use Maib\MaibApi\MaibClient;
use Maib\MaibApi\MaibDescription;
use GuzzleHttp\Client;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class ControllerExtensionPaymentMaib extends Controller {
	private $requestsLogFile = DIR_LOGS . 'maib_requests.log';
	
	private $logFile = DIR_LOGS . 'maib.log';
	
	public function getMaibClient($recreate = false) {
		if ($recreate) {
			unset($this->maibClient);
		}
		if ($this->maibClient) {
			return $this->maibClient;
		}
		$certificate = $this->config->get('payment_maib_public_key_file');
		if (!preg_match('/^\//', $certificate)) {
			$certificate = rtrim(DIR_SYSTEM, '/') . '/' . $certificate;
		}
		$private_key = $this->config->get('payment_maib_private_key_file');
		if (!preg_match('/^\//', $private_key)) {
			$private_key = rtrim(DIR_SYSTEM, '/') . '/' . $private_key;
		}
		
		if ($this->config->get('payment_maib_mode') == 'test') {
			$base_url = MaibClient::MAIB_TEST_BASE_URI;
		}
		elseif ($this->config->get('payment_maib_mode') == 'live') {
			$base_url = MaibClient::MAIB_LIVE_BASE_URI;
		}
		elseif (defined('PAYMENT_MAIB_MERCHANT_URL') && preg_match('#^(https://[^/]+)(.*)#', PAYMENT_MAIB_MERCHANT_URL, $m)) {
			$base_url = $m[1];
			$custom_url_path = $m[2];
		}
		else {
			throw new Exception('Client cannot be initiated, invalid or missing merchant url');
		}
		$options = [
			'base_url' => $base_url,
			'debug'  => false,
			'verify' => true,
			'defaults' => [
				'cert' => $certificate,
				'ssl_key' => [
					$private_key,
					$this->config->get('payment_maib_private_key_password')
				],
				'config'  => [
					'curl'  =>  [
						CURLOPT_SSL_VERIFYHOST => 2,
						CURLOPT_SSL_VERIFYPEER => true,
					]
				]
			],
		];

		$guzzle_6 = preg_match('/^6/', Client::VERSION);
		if ($guzzle_6) {
			$options['base_uri'] = $base_url;
			$options += $options['defaults']; 
		}

		if ($this->config->get('payment_maib_debug')) {
			$log = new Logger('maib_guzzle_request');
			$log->pushHandler(new StreamHandler($this->requestsLogFile, Logger::DEBUG));
		}

		if ($guzzle_6 && $this->config->get('payment_maib_debug')) {
			$stack = GuzzleHttp\HandlerStack::create();
			$stack->push(GuzzleHttp\Middleware::log($log, new GuzzleHttp\MessageFormatter(GuzzleHttp\MessageFormatter::DEBUG)));
			$options['handler'] = $stack;
		}

		$guzzleClient = new Client($options);
		$description_options = [];
		if (!empty($custom_url_path)) {
			$description_options['basePath'] = $custom_url_path;
		}
		$description = new MaibDescription($description_options);
		$this->maibClient = new MaibClient($guzzleClient, $description);

		if (!$guzzle_6 && $this->config->get('payment_maib_debug')) {
			$subscriber = new GuzzleHttp\Subscriber\Log\LogSubscriber($log, GuzzleHttp\Subscriber\Log\Formatter::SHORT);
			$this->maibClient->getHttpClient()->getEmitter()->attach($subscriber);
		}
		
		return $this->maibClient;
	}

	public function getUserIp() {
		return $this->request->server['REMOTE_ADDR'];
	}
	
	public function index() {
		$data = array();
		$this->load->language('extension/payment/maib');
		$this->load->model('localisation/currency');
		$this->load->model('checkout/order');

		$currency_codes = array(
			'EUR' => 978,
			'MDL' => 498
		);
		
		$order_info = $this->model_checkout_order
			->getOrder($this->session->data['order_id']);
		$language = $this->model_localisation_language
			->getLanguage($order_info['language_id']);
		
		if (!isset($currency_codes[$order_info['currency_code']])) {
			echo '<div class="warning">' .
				$this->language->get('error_no_currency') . '</div>';
			return;
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
				throw new Exception('No transaction_id ' . print_r($transaction, true));
			}
		}
		catch (Exception $e) {
			$this->log('Register new transaction - ' . $e->getMessage(), 'Error');
			echo '<div class="warning">' . $this->language->get('error_no_transaction_id') . '</div>';
			return;
		}
		
		$data['button_confirm'] = $this->language->get('button_confirm');
		$template = 'extension/payment/maib';
		return $this->load->view($template, $data);
	}

	public function getRedirectUrl() {
		if ($this->config->get('payment_maib_mode') == 'test') {
			return MaibClient::MAIB_TEST_REDIRECT_URL;
		}
		elseif ($this->config->get('payment_maib_mode') == 'live') {
			return MaibClient::MAIB_LIVE_REDIRECT_URL;
		}
		elseif (defined('PAYMENT_MAIB_REDIRECT_URL')) {
			return PAYMENT_MAIB_REDIRECT_URL;
		}
		throw new Exception('Invalid or missing redirect URL');
	}

	public function error() {
		$this->language->load('extension/payment/maib');
		$this->session->data['error'] = $this->language->get('text_maib_error');
		$this->response->redirect($this->url->link('checkout/checkout', '', 'SSL'));
	}
	
	public function return() {
		$this->language->load('extension/payment/maib');
		$this->load->model('checkout/order');
		
		$tries = 2;
		try {
			if (empty($this->request->post['trans_id'])) {
				throw new Exception('Missing TRANSACTION_ID');
			}
			$post_transaction_id = $this->request->post['trans_id'];
			if (empty($this->session->data['order_id'])) {
				throw new Exception('Missing Order ID');
			}
			$order_id = $this->session->data['order_id'];
			$user_ip = $this->getUserIp();
			$client = $this->getMaibClient();

			while ($tries--) {
				$tres = $client->getTransactionResult($post_transaction_id, $user_ip);
				if ($tres['RESULT'] == 'OK') {
					$this->model_checkout_order
						->addOrderHistory($order_id, $this->config->get('payment_maib_order_status_id'), "MAIB-OK/TRANS_ID:$post_transaction_id/RRN:" . $tres['RRN'], true);
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
					throw new Exception('Invalid transaction response, ' . print_r($tres, true));
				}
			}
			if ($tres['RESULT'] == 'PENDING') {
				$log_message = 'Failed to confirm payment transaction status'
					. ', still in pending for order ' . $this->session->data['order_id']
					. ', trans_id ' . $post_transaction_id
					. ', ' . print_r($tres, true);
				$this->log($log_message, 'Error');
				$this->session->data['error'] = $this->language->get('text_maib_pending');
				$this->model_checkout_order
					->addOrderHistory($order_id, $this->config->get('payment_maib_order_pending_status_id'), "MAIB-PENDING/TRANS_ID:$post_transaction_id/RRN:-", true);
				$this->response
					->redirect($this->url->link('checkout/success', '', 'SSL'));
			}
			else {
				throw new Exception('Failed to verify transaction status, ' . print_r($tres, true));
			}
		}
		catch (Exception $e) {
			$log_message = 'Failed payment with exception for order '
				. $this->session->data['order_id'] . ': ' . $e->getMessage();
			$this->log($log_message, 'Error');
			$this->session->data['error'] = $this->language->get('error_no_payment');
			$this->response
				->redirect($this->url->link('checkout/checkout', '', 'SSL'));
		}
	}
	
	/**
	 * Close bussiness day, should be invoked daily by cron.
	 * wget https://site.com/index.php?route=extension/payment/maib/closeday
	 */
	public function closeday() {
		$day = date('Ymd');
		if ($this->config->get('payment_maib_last_closed_day') == $day) {
			echo 'closed';
			return;
		}
		try {
			$client = $this->getMaibClient();
			
			$tres = $client->closeDay();
			
			if (isset($tres['RESULT']) && $tres['RESULT'] == 'OK') {
				$this->log('Business day closed');
				$this->load->model('extension/payment/maib');
				$this->model_extension_payment_maib
					->setClosedDay($day, $this->config->get('config_store_id'));
				echo 'closed';
			}
			else {
				throw new Exception('close day failed - ' . print_r($tres, true));
			}
		}
		catch (Exception $e) {
			$this->log('close day failed - ' . $e->getMessage(), 'Error');
			echo 'Close day error: ' . $e->getMessage();
		}
	}
	
	private function logError($transaction_id, $context) {
		$log_message = 'Order ' . $this->session->data['order_id']
			. ', trans_id ' . $transaction_id
			. ', ' . print_r($context, true);
		
		return $this->log($log_message, 'Error');
	}
	
	private function log($message, $type = 'Notice') {
		$log_message = date('d.m.Y H:i:s') . ' ' . $type . ': ' . $message . "\n";
		file_put_contents($this->logFile, $log_message, FILE_APPEND);
	}
}
