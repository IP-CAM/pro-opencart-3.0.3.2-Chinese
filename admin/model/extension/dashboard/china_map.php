<?php
class ModelExtensionDashboardChinaMap extends Model {
	public function getTotalOrdersByCountry() {
		$implode = array();
		
		if (is_array($this->config->get('config_complete_status'))) {
			foreach ($this->config->get('config_complete_status') as $order_status_id) {
				$implode[] = (int)$order_status_id;
			}
		}

		if ($implode) {
			$query = $this->db->query("SELECT COUNT(*) AS total, SUM(o.total) AS amount, c.iso_code_2 FROM `" . DB_PREFIX . "order` o LEFT JOIN `" . DB_PREFIX . "country` c ON (o.payment_country_id = c.country_id) WHERE o.order_status_id IN('" . (int)implode(',', $implode) . "') GROUP BY o.payment_country_id");

			return $query->rows;
		} else {
			return array();
		}
	}

    public function getTotalOrdersByZone() {
        $implode = array();

        if (is_array($this->config->get('config_complete_status'))) {
            foreach ($this->config->get('config_complete_status') as $order_status_id) {
                $implode[] = (int)$order_status_id;
            }
        }
        if ($implode) {
            $query = $this->db->query("SELECT COUNT(*) AS total, SUM(o.total) AS amount, z.code FROM `" . DB_PREFIX . "order` o LEFT JOIN `" . DB_PREFIX . "zone` z ON (o.payment_zone_id = z.zone_id) WHERE o.order_status_id IN('" . (int)implode(',', $implode) . "') GROUP BY o.payment_zone_id");
            return $query->rows;
        } else {
            return array();
        }
    }
   
    public function getTotalOrdersZoneByDay() {
        $implode = array();

        if (is_array($this->config->get('config_complete_status'))) {
            foreach ($this->config->get('config_complete_status') as $order_status_id) {
                $implode[] = (int)$order_status_id;
            }
        }

        if ($implode) {    
            $query = $this->db->query("SELECT COUNT(*) AS total, SUM(o.total) AS amount, z.code FROM `" . DB_PREFIX . "order` o LEFT JOIN `" . DB_PREFIX . "zone` z ON (o.payment_zone_id = z.zone_id) WHERE o.order_status_id IN('" . (int)implode(',', $implode) . "') AND date_added >= 'DATE(NOW())' GROUP BY o.payment_zone_id");
            return $query->rows;
        } else {
            return array();
        }
    }   
    
    public function getTotalOrdersZoneByWeek() {
        $implode = array();

        if (is_array($this->config->get('config_complete_status'))) {
            foreach ($this->config->get('config_complete_status') as $order_status_id) {
                $implode[] = (int)$order_status_id;
            }
        }

        if ($implode) {    
            $date_start = strtotime('-' . date('w') . ' days');

            $query = $this->db->query("SELECT COUNT(*) AS total, SUM(o.total) AS amount, z.code FROM `" . DB_PREFIX . "order` o LEFT JOIN `" . DB_PREFIX . "zone` z ON (o.payment_zone_id = z.zone_id) WHERE o.order_status_id IN('" . (int)implode(',', $implode) . "') AND date_added >= DATE('" . $this->db->escape(date('Y-m-d', $date_start)) . "')  GROUP BY o.payment_zone_id");
            return $query->rows;
        } else {
            return array();
        }
    }  

    public function getTotalOrdersZoneByMonth() {
        $implode = array();

        if (is_array($this->config->get('config_complete_status'))) {
            foreach ($this->config->get('config_complete_status') as $order_status_id) {
                $implode[] = (int)$order_status_id;
            }
        }

        if ($implode) {    
            $query = $this->db->query("SELECT COUNT(*) AS total, SUM(o.total) AS amount, z.code FROM `" . DB_PREFIX . "order` o LEFT JOIN `" . DB_PREFIX . "zone` z ON (o.payment_zone_id = z.zone_id) WHERE o.order_status_id IN('" . (int)implode(',', $implode) . "') AND date_added >= DATE('" . $this->db->escape(date('Y') . '-' . date('m') . '-1')  . "') GROUP BY o.payment_zone_id");
            return $query->rows;
        } else {
            return array();
        }
    } 

    public function getTotalOrdersZoneByYear() {
        $implode = array();

        if (is_array($this->config->get('config_complete_status'))) {
            foreach ($this->config->get('config_complete_status') as $order_status_id) {
                $implode[] = (int)$order_status_id;
            }
        }

        if ($implode) {    
            $query = $this->db->query("SELECT COUNT(*) AS total, SUM(o.total) AS amount, z.code FROM `" . DB_PREFIX . "order` o LEFT JOIN `" . DB_PREFIX . "zone` z ON (o.payment_zone_id = z.zone_id) WHERE o.order_status_id IN('" . (int)implode(',', $implode) . "') AND date_added >= '".$this->db->escape(date('Y') . '-1-1')."' GROUP BY o.payment_zone_id");
            return $query->rows;
        } else {
            return array();
        }
    }     
}