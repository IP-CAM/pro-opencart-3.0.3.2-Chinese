<?php

class ControllerExtensionModuleExpressTracking extends Controller
{
    private $error = array();

    private $store_id = 0;
    private $code = 'module_express_tracking';

    public function __construct($registry)
    {
        parent::__construct($registry);

        $this->store_id = isset($this->request->get['store_id']) ? (int)$this->request->get['store_id'] : 0;
    }

    public function index()
    {
        $this->load->language('extension/module/express_tracking');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting($this->code, $this->request->post, $this->store_id);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
        );

        if (!isset($this->request->get['module_id'])) {
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('extension/module/express_tracking', 'user_token=' . $this->session->data['user_token'], true)
            );
        } else {
            $data['breadcrumbs'][] = array(
                'text' => $this->language->get('heading_title'),
                'href' => $this->url->link('extension/module/express_tracking', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)
            );
        }

        if (!isset($this->request->get['module_id'])) {
            $data['action'] = $this->url->link('extension/module/express_tracking', 'user_token=' . $this->session->data['user_token'], true);
        } else {
            $data['action'] = $this->url->link('extension/module/express_tracking', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
        }

        $data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

        if ($this->request->server['REQUEST_METHOD'] != 'POST') {
            $module_info = $this->model_setting_setting->getSetting($this->code, $this->store_id);
        }

        $data['user_token'] = $this->session->data['user_token'];

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        if (isset($this->error['module_express_tracking_app_name'])) {
            $data['error_app_name'] = $this->error['app_name'];
        } else {
            $data['error_app_name'] = '';
        }

        if (isset($this->error['module_express_tracking_app_id'])) {
            $data['error_app_id'] = $this->error['app_id'];
        } else {
            $data['error_app_id'] = '';
        }

        if (isset($this->error['module_express_tracking_app_key'])) {
            $data['error_app_key'] = $this->error['app_key'];
        } else {
            $data['error_app_key'] = '';
        }

        if (isset($this->request->post['module_express_tracking_app_name'])) {
            $data['module_express_tracking_app_name'] = $this->request->post['module_express_tracking_app_name'];
        } elseif (!empty($module_info['module_express_tracking_app_name'])) {
            $data['module_express_tracking_app_name'] = $module_info['module_express_tracking_app_name'];
        } else {
            $data['module_express_tracking_app_name'] = '';
        }

        if (isset($this->request->post['module_express_tracking_app_id'])) {
            $data['module_express_tracking_app_id'] = $this->request->post['module_express_tracking_app_id'];
        } elseif (!empty($module_info['module_express_tracking_app_id'])) {
            $data['module_express_tracking_app_id'] = $module_info['module_express_tracking_app_id'];
        } else {
            $data['module_express_tracking_app_id'] = '';
        }

        if (isset($this->request->post['module_express_tracking_app_key'])) {
            $data['module_express_tracking_app_key'] = $this->request->post['module_express_tracking_app_key'];
        } elseif (!empty($module_info['module_express_tracking_app_key'])) {
            $data['module_express_tracking_app_key'] = $module_info['module_express_tracking_app_key'];
        } else {
            $data['module_express_tracking_app_key'] = '';
        }

        if (isset($this->request->post['module_express_tracking_status'])) {
            $data['module_express_tracking_status'] = $this->request->post['module_express_tracking_status'];
        } elseif (!empty($module_info['module_express_tracking_status'])) {
            $data['module_express_tracking_status'] = $module_info['module_express_tracking_status'];
        } else {
            $data['module_express_tracking_status'] = '';
        }

        if (isset($this->request->post['module_express_tracking_com'])) {
            $data['module_express_tracking_com'] = $this->request->post['module_express_tracking_com'];
        } elseif (!empty($module_info['module_express_tracking_com'])) {
            $data['module_express_tracking_com'] = $module_info['module_express_tracking_com'];
        } else {
            $data['module_express_tracking_com'] = array();
        }

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/express_tracking', $data));
    }

    public function getlist()
    {
        $this->load->model('setting/setting');

        $module_info = $this->model_setting_setting->getSetting($this->code, $this->store_id);

        if (isset($this->request->get['express_code'])) {
            $express_code = $this->request->get['express_code'];
        } else {
            $express_code = '';
        }

        if (isset($this->request->get['express_num'])) {
            $express_num = $this->request->get['express_num'];
        } else {
            $express_num = '';
        }

        if (!empty($module_info) && $module_info['module_express_tracking_status'] == 1 && !empty($express_code) && !empty($express_num)) {
            $express_name = $module_info['module_express_tracking_app_name'];

            require_once(DIR_SYSTEM . 'library/express/' . $express_name . '.php');

            $express = new $express_name();

            $params = array(
                'app_id' => $module_info['module_express_tracking_app_id'],
                'app_key' => $module_info['module_express_tracking_app_key'],
                'express_code' => $express_code,
                'express_num' => $express_num,
            );

            $tracking_info = $express->getTracking($params);

            $html = '';
            foreach ($tracking_info as $row) {
                $html .= $row['accept_time'] . ' ' . $row['accept_station'];
                if (!empty($row['remark'])) {
                    $html .= '（' . $row['remark'] . '）';
                }
                $html .= '</br>';
            }

            $this->response->setOutput($html);
        }

    }

    protected function validate()
    {
        if (!$this->user->hasPermission('modify', 'extension/module/express_tracking')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->request->post['module_express_tracking_app_name']) {
            $this->error['app_name'] = $this->language->get('error_app_name');
        }

        if (!$this->request->post['module_express_tracking_app_id']) {
            $this->error['app_id'] = $this->language->get('error_app_id');
        }

        if (!$this->request->post['module_express_tracking_app_key']) {
            $this->error['app_key'] = $this->language->get('error_app_key');
        }

        return !$this->error;
    }

    public function orderForm()
    {
        $data = array();

        if (isset($this->request->get['order_id']) && !empty($this->request->get['order_id'])) {
            $data['order_id'] = (int)$this->request->get['order_id'];
        } else {
            $data['order_id'] = '';
        }

        $this->load->language('extension/module/express_tracking');

        $this->load->model('setting/setting');

        $express_info = $this->model_setting_setting->getSetting($this->code, $this->store_id);

        $data['companies'] = array();
        foreach ($express_info['module_express_tracking_com'] AS $row) {
            if ($row['status']) {
                $data['companies'][] = $row;
            }
        }

        $data['user_token'] = $this->session->data['user_token'];

        return $this->load->view('extension/module/express_tracking_orderform', $data);
    }


    public function history()
    {

        $this->load->language('extension/module/express_tracking');

        $this->load->model('extension/module/order_express');

        if (isset($this->request->get['order_id'])) {
            $order_id = $this->request->get['order_id'];
        } else {
            $order_id = '';
        }

        $data['catalog'] = $this->request->server['HTTPS'] ? HTTPS_CATALOG : HTTP_CATALOG;
        $data['user_token'] = $this->session->data['user_token'];

        if (!empty($order_id)) {
            $data['order_express'] = $this->model_extension_module_order_express->getOrderExpress($order_id);

            $this->response->setOutput($this->load->view('extension/module/express_tracking_history', $data));
        }
    }

    public function addOrderExpress()
    {
        $this->load->language('extension/module/express_tracking');

        $this->load->model('extension/module/order_express');
        $this->load->model('setting/setting');

        $json = array();
        $data = array();

        if (isset($this->request->get['order_id']) && !empty($this->request->get['order_id'])) {
            $data['order_id'] = (int)$this->request->get['order_id'];
        } else {
            $json['error'] = $this->language->get('error_order_id');
        }

        if (isset($this->request->post['express_code']) && !empty($this->request->post['express_code'])) {
            $data['express_code'] = $this->request->post['express_code'];
        } else {
            $json['error'] = $this->language->get('error_express_code');
        }

        if (isset($this->request->post['tracking_number']) && !empty($this->request->post['tracking_number'])) {
            $data['tracking_number'] = $this->request->post['tracking_number'];
        } else {
            $json['error'] = $this->language->get('error_tracking_number');
        }

        if (isset($this->request->post['comment']) && !empty($this->request->post['comment'])) {
            $data['comment'] = $this->request->post['comment'];
        } else {
            $data['comment'] = '';
        }

        $express_info = $this->model_setting_setting->getSetting($this->code, $this->store_id);
        foreach ($express_info['module_express_tracking_com'] AS $row) {
            if (strtoupper(trim($row['code'])) == strtoupper(trim($data['express_code']))) {
                $data['express_name'] = $row['name'];
                break;
            }
        }

        if (!isset($data['express_name'])) {
            $json['error'] = $this->language->get('error_express_name');
        }

        if (!isset($json['error'])) {
            $this->model_extension_module_order_express->addOrderExpress($data);
            $json['success'] = $this->language->get('text_success');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));

    }

    public function delOrderExpress()
    {
        $this->load->language('extension/module/express_tracking');

        $this->load->model('extension/module/order_express');

        $json = array();

        if (isset($this->request->get['order_express_id']) && !empty($this->request->get['order_express_id'])) {
            $order_express_id = (int)$this->request->get['order_express_id'];
        } else {
            $json['error'] = $this->language->get('error_order_express_id');
        }

        if (!isset($json['error'])) {
            $this->model_extension_module_order_express->delOrderExpress($order_express_id);
            $json['success'] = 'ok';
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));

    }

}