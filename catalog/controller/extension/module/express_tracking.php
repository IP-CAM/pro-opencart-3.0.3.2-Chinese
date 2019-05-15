<?php
class ControllerExtensionModuleExpressTracking extends Controller
{
    private $store_id = 0;

    private $code = 'module_express_tracking';

    public function getlist()
    {
        $this->load->language('extension/module/express_tracking');

        if (isset($this->request->get['order_id'])) {
            $order_id = $this->request->get['order_id'];
        } else {
            $order_id = 0;
        }

        if (!$this->customer->isLogged()) {
            $this->session->data['redirect'] = $this->url->link('account/order/info', 'order_id=' . $order_id, true);

            $this->response->redirect($this->url->link('account/login', '', true));
        }

        if (!empty($order_id)) {
            $this->load->model('setting/setting');

            $this->load->model('extension/module/order_express');

            $module_info = $this->model_setting_setting->getSetting($this->code, $this->store_id);

            $tracking_info = $this->model_extension_module_order_express->getLastOrderExpress($order_id);


            if (!empty($module_info) && $module_info['module_express_tracking_status'] == 1 && !empty($tracking_info['express_code']) && !empty($tracking_info['tracking_number'])) {

                $express_name = $module_info['module_express_tracking_app_name'];

                require_once(DIR_SYSTEM . 'library/express/' . $express_name . '.php');

                $express = new $express_name();

                $params = array(
                    'app_id'        => $module_info['module_express_tracking_app_id'],
                    'app_key'       => $module_info['module_express_tracking_app_key'],
                    'express_code'  => $tracking_info['express_code'],
                    'express_num'   => $tracking_info['tracking_number'],
                );

                $tracking_info = $express->getTracking($params);

                $html = '';
                if (empty($tracking_info) || count($tracking_info) == 0) {
                    $html = '<td colspan="3" class="text-center">' . $this->language->get('text_no_results') . '</td>';
                } else {
                    foreach ($tracking_info as $row) {
                        $html.= '<tr><td class="text-left">' . $row['accept_time'] . '</td>
                                     <td class="text-left">' . $row['accept_station'] . '</td>
                                     <td class="text-left">' . $row['remark'] . '</td>
                                 </tr>';
                    }
                }

                $this->response->setOutput($html);
            }

        }
    }

}