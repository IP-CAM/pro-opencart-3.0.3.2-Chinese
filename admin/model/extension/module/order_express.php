<?php
class ModelExtensionModuleOrderExpress extends Model {
    public function getOrderExpress($order_id){
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_express WHERE order_id = '" . (int)$order_id . "'");

        return $query->rows;
    }

    public function addOrderExpress($data){

        $this->db->query("INSERT INTO " . DB_PREFIX . "order_express SET order_id = '" . (int)$data['order_id'] . "', date_added = '" . date('Y-m-d H:i:s') . "', express_name = '" . $this->db->escape($data['express_name']) . "', express_code = '" . $this->db->escape($data['express_code']) . "', tracking_number = '" . $this->db->escape($data['tracking_number']) . "' , comment = '" . $this->db->escape($data['comment']) ."'" );

        $id = $this->db->getLastId();

        return $id;
    }

    public function delOrderExpress($id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "order_express WHERE order_express_id = " . (int)$id . " LIMIT 1" );
    }

}