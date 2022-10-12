<?php

namespace Opencart\Admin\Controller\Extension\Maib\Payment;

require_once(DIR_EXTENSION . 'maib/system/library/maib/vendor/autoload.php');

use Maib\MaibApi\MaibClient;

/**
 * MAIB payment extension.
 */
class Maib extends \Opencart\System\Engine\Controller {
	private $error = array();

	public function index(): void {
		$this->load->language('extension/maib/payment/maib');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');
		if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate()) {
			$this->model_setting_setting->editSetting('payment_maib', $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->checkCron();
			$this->response->redirect($this->url->link('marketplace/extension', 'user_token='
				. $this->session->data['user_token'] . '&type=payment', true));
		}

		$data['breadcrumbs'] = $this->getBreadCrumbs();

		$data['_form'] = $this->getPostData();

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		$data['_error'] = $this->error;

		$data['payment_maib_redirect_url']['Default test'] = MaibClient::MAIB_TEST_REDIRECT_URL;
		$data['payment_maib_redirect_url']['Default live'] = MaibClient::MAIB_LIVE_REDIRECT_URL;
		if (defined('\PAYMENT_MAIB_REDIRECT_URL')) {
			$data['payment_maib_redirect_url']['From config.php'] = \PAYMENT_MAIB_REDIRECT_URL;
		}
		else {
			$data['payment_maib_redirect_url']['From config.php'] = '-<br>To override add your url to catalog and admin config.php, ex:'
				 . '<br>define(\'PAYMENT_MAIB_REDIRECT_URL\', "https://maib.ecommerce.md:123/ecomm456/ClientHandler");';
		}

		$data['payment_maib_merchant_url']['Default test'] = MaibClient::MAIB_TEST_BASE_URI;
		$data['payment_maib_merchant_url']['Default live'] = MaibClient::MAIB_LIVE_BASE_URI;
		if (defined('\PAYMENT_MAIB_MERCHANT_URL')) {
			$data['payment_maib_merchant_url']['From config.php'] = \PAYMENT_MAIB_MERCHANT_URL;
		}
		else {
			$data['payment_maib_merchant_url']['From config.php'] = '-<br>To override add your url to catalog and admin config.php, ex:'
				 . '<br>define(\'PAYMENT_MAIB_MERCHANT_URL\', "https://maib.ecommerce.md:123/ecomm456/MerchantHandler");';
		}

    	$data['action'] = $this->url->link('extension/maib/payment/maib', 'user_token='
    		. $this->session->data['user_token'], 'SSL');

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token='
			. $this->session->data['user_token'] . '&type=payment', true);
		$data['payment_maib_shop_return_url'] = (defined('\HTTPS_CATALOG') ? \HTTPS_CATALOG : \HTTP_CATALOG)
			. 'index.php?route=extension/maib/payment/done';

		$this->load->model('localisation/geo_zone');
		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		$this->load->model('localisation/order_status');
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		$template = 'extension/maib/payment/maib';
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view($template, $data));
	}

	private function validate(): bool {
		$post_data = $this->request->post;

		if (!$this->user->hasPermission('modify', 'extension/maib/payment/maib')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		$required = array('payment_maib_private_key_file', 'payment_maib_public_key_file');

		foreach ($required as $field) {
			if (empty($post_data[$field])) {
				$this->error[$field] = $this->language->get('error_empty_field');
			}
		}

		if (count($this->error)) {
			return false;
		}

		$pkey = (preg_match('/^\//', $post_data['payment_maib_private_key_file']) ?
			'' : rtrim(DIR_SYSTEM, '/') . '/') . $post_data['payment_maib_private_key_file'];
		if (empty($this->error['payment_maib_private_key_file']) && !file_exists($pkey)) {
			$this->error['payment_maib_private_key_file'] = $this->language->get('error_key_file_not_found');
		}

		$cert = (preg_match('/^\//', $post_data['payment_maib_public_key_file']) ?
			'' : rtrim(DIR_SYSTEM, '/') . '/') . $post_data['payment_maib_public_key_file'];
		if (empty($this->error['payment_maib_public_key_file']) && !file_exists($cert)) {
			$this->error['payment_maib_public_key_file'] = $this->language->get('error_key_file_not_found');
		}

		if (function_exists('openssl_x509_check_private_key')
			&& empty($this->error['payment_maib_public_key_file'])
			&& empty($this->error['payment_maib_private_key_file'])) {
			$cert_data = file_get_contents($cert);
			$key_data = array(
				file_get_contents($pkey),
				empty($post_data['payment_maib_private_key_password']) ? null : $post_data['payment_maib_private_key_password'],
			);
			if (false === openssl_x509_check_private_key($cert_data, $key_data)) {
				$this->error['payment_maib_private_key_file'] = $this->language->get('error_key_file_not_match');
			}
		}

		return empty($this->error);
	}

	private function getBreadCrumbs(): array {
		$breadcrumbs = [];

		$breadcrumbs[] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token='
				. $this->session->data['user_token'])
		];

		$breadcrumbs[] = [
			'text' => $this->language->get('text_extensions'),
			'href' => $this->url->link('marketplace/extension', 'user_token='
				. $this->session->data['user_token'] . '&type=payment')
		];

		$breadcrumbs[] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/maib/payment/maib', 'user_token='
				. $this->session->data['user_token'])
		);

		return $breadcrumbs;
	}

	private function getPostData(): array {
		$defautls = $this->getDefaults();
		foreach ($defautls as $key => $value) {
			$config = $this->config->get($key);
			if (is_numeric($value) && $config === '') {
				$config = null;
			}
			if ($config !== null) {
				$defautls[$key] = $config;
			}
		}
		return array_merge($defautls, $this->request->post);
	}

	private function getDefaults(): array {
		return array(
			'payment_maib_private_key_file' => '',
			'payment_maib_private_key_password' => '',
			'payment_maib_public_key_file' => '',
			'payment_maib_mode' => 'test',
			'payment_maib_method' => 'sms',
			'payment_maib_total' => 0,
			'payment_maib_order_status_id' => 1,
			'payment_maib_order_pending_status_id' => 1,
			'payment_maib_geo_zone_id' => '',
			'payment_maib_status' => 1,
			'payment_maib_sort_order' => 1,
			'payment_maib_debug' => 0,
			'payment_maib_last_closed_day' => '',
		);
	}

	public function checkCron(): void {
		$this->load->model('setting/cron');
		if (!$this->model_setting_cron->getCronByCode('maib')) {
			$this->model_setting_cron
				->addCron('maib', 'Close day', 'day', 'extension/maib/cron/maib', true);
		}
	}

	public function install() {
		$this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "maib_transaction (
			transaction_id char(32) NOT NULL,
			order_id int NOT NULL,
			date_added char(20) NOT NULL,
			PRIMARY KEY (transaction_id))");
	}

	public function uninstall() {
		$this->db->query("DROP TABLE IF EXISTS " . DB_PREFIX . "maib_transaction");
	}
}
