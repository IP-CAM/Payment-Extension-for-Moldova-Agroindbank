<?php 
class ModelExtensionPaymentMaib extends Model {
	public function getMethod($address, $total) {
		$this->load->language('extension/payment/maib');

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone
			WHERE geo_zone_id = '" . (int)$this->config->get('payment_maib_geo_zone_id') . "'
				AND country_id = '" . (int)$address['country_id'] . "'
				AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");

		if ($this->config->get('payment_maib_total') > $total) {
			$status = false;
		} elseif (!$this->config->get('payment_maib_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}

		$method_data = array();

		if ($status) {
			$method_data = array(
				'code'       => 'maib',
				'title'      => $this->language->get('text_title'),
				'terms'      => '',
				'sort_order' => $this->config->get('payment_maib_sort_order')
			);
		}
		return $method_data;
	}
	
	public function setClosedDay($closed_day, $store_id) {
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
?>
