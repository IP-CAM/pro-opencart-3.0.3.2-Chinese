<?php
class ModelExtensionModuleOrderExpress extends Model {
    public function getLastOrderExpress($order_id){
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_express WHERE order_id = '" . (int)$order_id . "' ORDER BY date_added DESC LIMIT 1");

        if (empty($query->rows)) {
            return array();
        }else{
            return $query->rows[0];
        }

    }
}