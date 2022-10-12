<?php

namespace Opencart\Catalog\Controller\Extension\Maib\Payment;

require_once(DIR_EXTENSION . 'maib/system/library/maib/vendor/autoload.php');

use Maib\MaibApi\MaibClient;
use Maib\MaibApi\MaibDescription;
use GuzzleHttp\Client;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

trait MaibTrait {
	private $requestsLogFile = DIR_LOGS . 'maib_requests.log';

	private $logFile = DIR_LOGS . 'maib.log';

	private $logger;

	private $maibClient;

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
		elseif (defined('\PAYMENT_MAIB_MERCHANT_URL')) {
			$base_url = \PAYMENT_MAIB_MERCHANT_URL;
		}
		else {
			throw new \Exception('Client cannot be initiated, invalid or missing merchant url');
		}
		$options = [
			'base_uri' => $base_url,
			'debug'  => false,
			'verify' => true,
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
			],
		];

		if ($this->config->get('payment_maib_debug')) {
			$log = new Logger('maib_guzzle_request');
			$log->pushHandler(new StreamHandler($this->requestsLogFile, Logger::DEBUG));
			$stack = HandlerStack::create();
			$stack->push(Middleware::log($log, new MessageFormatter(MessageFormatter::DEBUG)));
			$options['handler'] = $stack;
		}

		$guzzleClient = new Client($options);
		$this->maibClient = new MaibClient($guzzleClient);

		return $this->maibClient;
	}

	public function getUserIp() {
		return $this->request->server['REMOTE_ADDR'];
	}

	public function getRedirectUrl() {
		if ($this->config->get('payment_maib_mode') == 'test') {
			return MaibClient::MAIB_TEST_REDIRECT_URL;
		}
		elseif ($this->config->get('payment_maib_mode') == 'live') {
			return MaibClient::MAIB_LIVE_REDIRECT_URL;
		}
		elseif (defined('\PAYMENT_MAIB_REDIRECT_URL')) {
			return \PAYMENT_MAIB_REDIRECT_URL;
		}
		throw new \Exception('Invalid or missing redirect URL');
	}

	public function error() {
		$this->language->load('extension/maib/payment/maib');
		$this->session->data['error'] = $this->language->get('text_maib_error');
		$this->response->redirect($this->url->link('checkout/checkout', '', 'SSL'));
	}

	private function logError($transaction_id, $context): void {
		$log_message = 'Order ' . $this->session->data['order_id']
			. ', trans_id ' . $transaction_id
			. ', ' . print_r($context, true);

		$this->log($log_message, 'Error');
	}

	private function log($message, $type = 'Info'): void {
		if (!$this->logger) {
			$this->logger = new Logger('maib');
			$this->logger->pushHandler(new StreamHandler($this->logFile, Logger::DEBUG));
		}
		$method = strtolower($type);
		$this->logger->{$method}($message);
	}
}
