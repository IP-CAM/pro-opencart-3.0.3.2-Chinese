<?php
class ControllerExtensionModuleWeiBoLogin extends Controller {
	private $error = array();

	public function index() {
		if (!$this->customer->isLogged()) {
			$appkey = $this->config->get('module_weibo_login_appkey');
			$appsecret = $this->config->get('module_weibo_login_appsecret');
			$callback_url = $this->url->link('extension/module/weibo_login/callback', '', true);
			
			$this->load->language('extension/module/weibo_login');
			
			$data['text_weibo_login'] = $this->language->get('text_weibo_login');

			include_once(DIR_SYSTEM.'library/weibo/saetv2.ex.class.php');
			
			$o = new SaeTOAuthV2($appkey , $appsecret);

			$data['code_url'] = $o->getAuthorizeURL($callback_url);

			if ($this->customer->isLogged()) {
				$data['logged'] = 1;
			} else {
				$data['logged'] = 0;
			}
			
			if(isset($this->session->data['weibo_login_access_token']) && isset($this->session->data['weibo_login_uid'])) {
				$data['weibo_login_authorized'] = 1;
			} else {
				$data['weibo_login_authorized'] = 0;
				unset($this->session->data['weibo_login_access_token']);
				unset($this->session->data['weibo_login_uid']);
			}

			return $this->load->view('extension/module/weibo_login', $data);

		}
	}
	
	public function callback() {

		$appkey = $this->config->get('module_weibo_login_appkey');
		$appsecret = $this->config->get('module_weibo_login_appsecret');
		$callback_url = $this->url->link('extension/module/weibo_login/callback', '', true);
		
		$this->load->language('extension/module/weibo_login');
		
		$data['text_weibo_login'] = $this->language->get('text_weibo_login');

		include_once(DIR_SYSTEM.'library/weibo/saetv2.ex.class.php');

		$o = new SaeTOAuthV2($appkey, $appsecret);
		
		if (isset($_REQUEST['code'])) {
			$keys = array();
			$keys['code'] = $_REQUEST['code'];
			$keys['redirect_uri'] = $callback_url;
			try {
				$token = $o->getAccessToken( 'code', $keys ) ;
			} catch (OAuthException $e) {
			}
		}
		
		if ($token) {
			
			//setcookie( 'weibojs_'.$o->client_id, http_build_query($token) );
			
			$c = new SaeTClientV2($appkey, $appsecret, $token['access_token']);
			$ms  = $c->home_timeline();
			$uid_get = $c->get_uid();
			$uid = $uid_get['uid'];
			$user_message = $c->show_user_by_id($uid);
			
			$this->load->model('account/customer');
			
			$this->session->data['weibo_login_access_token'] = $token['access_token'];
			
			$this->session->data['weibo_login_uid'] = $uid;
			
			if ($this->customer->login_weibo($this->session->data['weibo_login_access_token'],  $this->session->data['weibo_login_uid'])) {
				
				unset($this->session->data['guest']);
	
				// Default Shipping Address
				$this->load->model('account/address');
	
				if ($this->config->get('config_tax_customer') == 'payment') {
					$this->session->data['payment_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
				}
	
				if ($this->config->get('config_tax_customer') == 'shipping') {
					$this->session->data['shipping_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
				}
	
				$this->response->redirect($this->url->link('account/account', '', 'SSL'));
			}else{

				$customer_data = array(
					'registertype'	=> 'email',
					'firstname'	    => $uid,
					'lastname'	    => '',
					'email'		    => $uid,
					'telephone'	    => $uid,
					'password'	    => $uid,
				);
				
				$customer_id = $this->model_account_customer->addCustomer($customer_data);
			
				if($uid) {
					$this->model_account_customer->updateCustomerWeiBoInfo($customer_id, $this->session->data['weibo_login_access_token'], $uid);
				}
				
				$this->customer->login($uid, $uid);
				
				//Unset Third party login session
				unset($this->session->data['qq_login_warning']);
				unset($this->session->data['weibo_login_warning']);
				unset($this->session->data['weixin_login_warning']);
				unset($this->session->data['qq_nickname']);
	
				unset($this->session->data['guest']);
				
				$this->response->redirect($this->url->link('account/account'));
			}
			
		}else{
			echo $this->language->get('text_weibo_fail');	
		}
	
	}

	public function login(){
        $appkey = $this->config->get('module_weibo_login_appkey');
        $appsecret = $this->config->get('module_weibo_login_appsecret');
        $callback_url = $this->url->link('extension/module/weibo_login/callback', '', true);

        include_once(DIR_SYSTEM.'library/weibo/saetv2.ex.class.php');

        $o = new SaeTOAuthV2($appkey , $appsecret);

        $data['code_url'] = $o->getAuthorizeURL($callback_url);

        $this->response->redirect($this->url->link('extension/module/qq_login/login'));
    }

}