<?php

namespace Opencart\Admin\Controller\Extension\Maib\Cron;

require __DIR__ . '/../../../catalog/controller/payment/maib_trait.php';
use Opencart\Catalog\Controller\Extension\Maib\Payment\MaibTrait;

class Maib extends \Opencart\System\Engine\Controller {
	use MaibTrait;

	public function index(int $cron_id, string $code, string $cycle, string $date_added, string $date_modified): void {
		$this->closeDay();
	}

	public function closeday() {
		if (!$this->config->get('payment_maib_status')) {
// 			echo 'disabled';
			return;
		}
		$day = date('Ymd');
		if ($this->config->get('payment_maib_last_closed_day') == $day) {
// 			echo 'closed';
			return;
		}
		try {
			$client = $this->getMaibClient();

			$tres = $client->closeDay();

			if (isset($tres['RESULT']) && $tres['RESULT'] == 'OK') {
				$this->log('Business day closed');
				$this->setClosedDay($day, $this->config->get('config_store_id'));
// 				echo 'closed';
			}
			else {
				throw new \Exception('close day failed - ' . print_r($tres, true));
			}
		}
		catch (\Exception $e) {
			$this->log('close day failed - ' . $e->getMessage(), 'Error');
// 			echo 'Close day error: ' . $e->getMessage();
		}
	}

	public function setClosedDay($closed_day, $store_id): void {
		$key = 'payment_maib_last_closed_day';
		$query = $this->db->query("SELECT value FROM " . DB_PREFIX . "setting
			WHERE store_id = '" . (int)$store_id . "' AND `key` = '" . $this->db->escape($key) . "'");

		if ($query->num_rows) {
			$this->db->query("UPDATE " . DB_PREFIX . "setting
				SET `value` = '" . $this->db->escape($closed_day) . "'
				WHERE `code` = 'payment_maib' AND `key` = '" . $this->db->escape($key) . "' AND store_id = '" . (int)$store_id . "'");
		} else {
			$this->db->query("INSERT INTO " . DB_PREFIX . "setting
				SET store_id = '" . (int)$store_id . "', `code` = 'payment_maib',
				`key` = '" . $this->db->escape($key) . "', `value` = '" . $this->db->escape($closed_day) . "'");
		}
	}

}
