<?php
class ControllerDesignMobileLayout extends Controller {
	private $error = array();
    private $layout_id = 0;

	public function index() {
		$this->load->language('design/mobile_layout');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('design/layout');

		$this->getList();
	}

	public function setting(){

        $this->load->language('design/mobile_layout');

        $this->document->setTitle($this->language->get('heading_title'));

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

            $this->load->model('design/layout');

            $post_data = $this->request->post;
            foreach($post_data as $position => $modules) {
                $data = array();
                foreach($modules as $sort => $code) {
                    $data['layout_module'][] = array(
                        'layout_id' => $this->layout_id,
                        'code'      => $code,
                        'position'  => $position,
                        'sort_order'=> $sort,
                    );
                }
            }
            $this->model_design_layout->editMobileLayout(0, $data);

            $this->session->data['success'] = $this->language->get('text_success');
        }

        $this->getList();

    }


	protected function getList() {

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('design/mobile_layout', 'user_token=' . $this->session->data['user_token'] , true)
		);

		$data['layouts'] = array();

		$results = $this->model_design_layout->getLayouts();

		foreach ($results as $result) {
			$data['layouts'][] = array(
				'layout_id' => $result['layout_id'],
				'name'      => $result['name'],
				'edit'      => $this->url->link('design/layout/edit', 'user_token=' . $this->session->data['user_token'] . '&layout_id=' . $result['layout_id'] , true)
			);
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

        $this->load->model('setting/extension');
        $this->load->model('setting/module');

        $data['extensions'] = array();

        $extensions = $this->model_setting_extension->getInstalled('module');

        foreach ($extensions as $code) {

            if (strpos($code,'login') !== false) {
                continue;
            }

            $this->load->language('extension/module/' . $code, 'extension');

            $module_data = array();

            $modules = $this->model_setting_module->getModulesByCode($code);

            foreach ($modules as $module) {
                if (isset($module['setting'])) {
                    $setting = json_decode($module['setting'], true);
                    if (isset($setting['status']) && $setting['status'] == 1) {
                        $module_data[] = array(
                            'name' => strip_tags($module['name']),
                            'code' => $code . '.' . $module['module_id'],
                            'edit' => $this->url->link('extension/module/' . $code, 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $module['module_id'], true),
                        );
                    }
                }
            }

            if (!empty($module_data)) {
                $data['extensions'][] = array(
                    'name'   => strip_tags($this->language->get('extension')->get('heading_title')),
                    'code'   => $code,
                    'module' => $module_data
                );
            }
        }

        $this->load->model('design/layout');

        $layout_modules = $this->model_design_layout->getLayoutModules($this->layout_id);

        $data['layout_modules'] = array();

        foreach ($layout_modules as $layout_module) {
            $part = explode('.', $layout_module['code']);

            $this->load->language('extension/module/' . $part[0]);

            if (!isset($part[1])) {
                $data['layout_modules'][] = array(
                    'name'       => strip_tags($this->language->get('heading_title')),
                    'code'       => $layout_module['code'],
                    'edit'       => $this->url->link('extension/module/' . $part[0], 'user_token=' . $this->session->data['user_token'], true),
                    'sort_order' => $layout_module['sort_order']
                );
            } else {
                $module_info = $this->model_setting_module->getModule($part[1]);

                if ($module_info) {
                    $data['layout_modules'][] = array(
                        'name'       => strip_tags($module_info['name']),
                        'type'       => strip_tags($this->language->get('heading_title')),
                        'code'       => $layout_module['code'],
                        'edit'       => $this->url->link('extension/module/' . $part[0], 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $part[1], true),
                        'sort_order' => $layout_module['sort_order']
                    );
                }
            }
        }

        $data['action'] = $this->url->link('design/mobile_layout/setting', 'user_token=' . $this->session->data['user_token'], true);
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

        $this->load->language('design/mobile_layout');

		$this->response->setOutput($this->load->view('design/mobile_layout', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'design/layout')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

}
