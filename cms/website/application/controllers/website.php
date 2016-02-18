<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Typology CMS
 *
 * @author      VSA Partners / Louis D Walch (lwalch@vsapartners.com)
 * @link        http://www.vsapartners.com
 *
 */
 
 
require_once(APPPATH . 'controllers/application.php');

class Website extends Application {

	var $PAGE_CONF			= array();
	
	var $zone				= 'website';
	var $module				= 'page';
	
	var $request_type		= 'www';
	var $request_format		= 'html';
	var $request_path		= '';
	
	var $page_title;
	var $page_cache_period	= false;
	var $page_cache_id 		= false;
	var $error_count = 0;

	// ------------------------------------------------------------------------

	function __construct() {
			parent::__construct();

			// Is site active?
			$this->_checkSiteStatus();

			$this->load->library('layout', $this->loadConfig('website_layout', FALSE));
            $this->load->helper('inflector');

			// Check request format, store url, etc.
			$this->_prepareForOutput();

			$this->load->model('page_model', 'page_model');
			$this->load->model('page_attributejoin_model');
			$this->load->model('cache_model', 'cache');
			
 			$this->PAGE_CONF 	= $this->loadConfig('page');

			// Set up initial layout properties
			$this->layout->setTitle($this->SITE_CONF['site_title']);

	}


	// ------------------------------------------------------------------------

	function _remap() {

			if ($this->_passwordProtectSite() === TRUE) {	
			
				$show 	= $this->load->view('login_site', NULL, TRUE);
				$output = $this->_formatOutput($show, TRUE);
				$this->output->set_output($output);

			} else if ($this->request_format == 'file') {	
				$this->_loadFile(array('path' => $this->request_path));
			} else if ($this->request_type == 'service') {	
				$this->_performService($this->request_path);
			} else {
				$this->loadPage(array('path' => $this->request_path));
			}

	}


	public function loadPage($item=array(), $format=NULL, $primary=TRUE, $check_cache=TRUE, $get_children=TRUE) {

// log_message('debug', '--- loadPage : '.$format. ': '.print_r($item, true));

			$output			= '';
			$uri = rtrim($this->uri->uri_string(), '/');					
			if (strpos($uri,'JSON/')){		
				$format	= 'json';		
			}else{		
				$format	= (!is_null($format)) ? $format : $this->request_format;		
			}
			$cache_params	= $item;
			$prev_cache		= $this->page_cache_period;
			$prev_cache_id	= $this->page_cache_id;
			$page_data		= '';
						
			if (!$primary) $cache_params['sub_request'] = 1;

			// Check if request is already cached
			if ($check_cache && $this->cache->isCached($this->module, $cache_params)) {

				$output 	= $this->cache->getCacheData() . '<!-- Showing cached content -->';

			} else {

				// Is this a request for the sitemap?
				if ($this->SITE_CONF['sitemap']['allow'] && ($this->SITE_CONF['sitemap']['uri_trigger'] == $this->request_path)) {
				
					$page_data		= $this->_loadSitemapXML();
				
				// Load page
				} else {

					switch (strtolower($format)) {
						case 'json':
						case 'xml':
							$page_data 	.= $this->_loadPageXML($item, $primary, $get_children);
							break;
						case 'html':
						default:
							$page_data 	.= $this->_loadPageHTML($item, $primary, $get_children);
							break;
					}
					
				}					

				// Make sure valid data returned			
				if (empty($page_data)) {
					return ($primary) ? $this->_show404('Empty page data.') : FALSE;
				}

				$output = ($primary) ? $this->_formatOutput($page_data, TRUE) : $page_data;				
				
				if (strpos($uri,'JSON/')){		
					$xml = simplexml_load_string($output,'SimpleXMLElement', LIBXML_NOCDATA);		
					$output = json_encode($xml);		
					header('Content-Type: application/json');		
					echo $output;		
					exit;
				}
				// Should this be cached ?
				if ($check_cache && $this->page_cache_period) {

					// Make sure we are using the correct cache id
					if (!empty($item['page_id'])) {	
// log_message('debug', '*** setting page_cache_id based on item/page_id');
						$this->page_cache_id = $item['page_id'];
					}
					if (!empty($item['parent_id'])) {
// log_message('debug', '*** setting page_cache_id based on item/parent_id');
						$this->page_cache_id = $item['parent_id'];
					}

					if ($this->page_cache_id) {
// log_message('debug', '!!! save page_cache_id: '.$this->page_cache_id);
// log_message('debug', '!!! save cache_params: '.print_r($cache_params, true));
						// Store cache		
						$this->cache->save($output, $this->module, $cache_params, $this->page_cache_id, $this->page_cache_period);
					}

				}
				
			}
			
			// Restore cache value
			$this->page_cache_period 	= $prev_cache;
			$this->page_cache_id 		= $prev_cache_id;

			if ($primary) {
				// This will inturn call _output and _wrap
				$this->output->set_output($output);
			} else {
				return $output;
			}

	}


	// ------------------------------------------------------------------------	
	// LOAD HTML

	private function _loadPageHTML($item=array(), $primary=TRUE, $get_children=TRUE) {	

			$page			= $this->_loadPageData($item, TRUE);
			$return			= array();

			if (!count($page)) {
				$message = 'Page not found in database.';
				return $this->_show404($message);
			} else {
				$page = $page[0];
			}
			
			if ($page['template_options']['html_action'] == 'deny') {

				// This page is not enabled for HTML view

				if (!empty($page['template_options']['html_redirect'])) {
					//log_message('debug', '>> Page not allowed to be viewed as HTML. Redirecting to: '.$page['template_options']['html_redirect']);
					redirect($page['template_options']['html_redirect']);
				} else {
					//log_message('debug', '>> Page not allowed to be viewed as HTML. Showing 404');
					$this->_show404('Page not allowed to be viewed as HTML.');
					return false;						
				}			

			} else if (($page['type'] == 'redirect') && !empty($page['options']['redirect_path'])) {

				// Redirect page request. Get outta here.
				redirect($page['options']['redirect_path'], 'location', 301);
				exit();

			//} else if (($page['type'] == 'secure_section') || ($page['type'] == 'secure_page')) {
			} else if ($page['type'] == 'secure_page') {

				$this->page_cache_period 	= false;
				$this->_loginSecureUser($page);					

		//	} else if (($page['type'] == 'section') && ($page['template_options']['html_action'] == 'first')) {
			} else if (in_array($page['type'], array('section', 'mirror_section', 'mirror_section_source')) && ($page['template_options']['html_action'] == 'first')) {

				// Return the first page
				return $this->_loadPageHTML(array('parent_id' => $page['page_id']));

			} else if (($page['type'] == 'section') && ($page['template_options']['html_action'] == 'specified') && !empty($page['options']['section_pages']['default_page'])) {

				// Return user specified page
				return $this->_loadPageHTML(array('page_id' => $page['options']['section_pages']['default_page']), $primary);
			
			}

			// Stuff to do if this is the primary request only
			if ($primary) {

				$this->current_id 		= $page['page_id'];
				$this->current_title	= $page['title'];
				$this->layout->appendTitle($page['title']);

				if (!empty($page['meta_description'])) {
					$this->layout->addMeta('og:desc', $page['meta_description'], TRUE, 'property');
					$this->layout->addMeta('description', $page['meta_description'], TRUE);
				}
				if (!empty($page['meta_title'])) {
					$this->layout->addMeta('og:title', $page['meta_title'], TRUE, 'property');
					$this->layout->setTitle($page['meta_title']);
				}
				if (!empty($page['meta_image'])) {
					$this->layout->addMeta('og:image', $page['meta_image'], TRUE, 'property');
				}
				if (!empty($page['tracking_js']['misc'])) {
					$this->layout->addHeadMisc($page['tracking_js']['misc']);
				}

				if ($this->SITE_CONF['cache_enabled'] && ($page['template_options']['cache_time'] != 'none')) {
					$this->page_cache_period = $page['template_options']['cache_time'];
				}

			}

			// Static page request. This needs to happen after the primary information above is set.
			if ($page['type'] == 'static') return $page['content'];

			// Make sure we have an XSL template
			if (empty($page['template_html_xsl_path'])) {
				show_error('Could not load the page at /templates/'.$this->request_path.' for page '.$page['page_id'].'. Missing or invalid template.');
			}


			if (in_array($page['type'], array('section', 'page_calendar', 'mirror_section', 'mirror_section_source', 'mirror_calendar', 'mirror_calendar_source')) && (strpos($page['template_options']['html_action'], 'show_w_') === 0)) {
			
				// Return children
				
				$html_action	= $page['template_options']['html_action'];
				$get_children	= (is_bool($get_children)) ? $get_children : TRUE;

				// Is this a section, get it's children
				if ($get_children) {

					$next_children 		= ($html_action == 'show_w_all' || $html_action == 'show_w_immediate') ? TRUE : FALSE;	
					$l_item 			= array('parent_id' => $page['page_id']);
					$page['children']	= $this->_loadPageXML($l_item, FALSE, $next_children);

				}	
			
			}

			// We need this for caching
			if (!empty($page['page_id'])) {
				$this->page_cache_id = $page['page_id'];
			}
			
			return $this->xsl_transform->transform($page['template_html_xsl_path'], $page);
		
	}


	// ------------------------------------------------------------------------	
	// LOAD XML

	private function _loadPageXML($item=array(), $primary=TRUE, $get_children=TRUE) {	
			if (array_key_exists('path',$item)){
					if (strpos($item[path],'JSON/')){
						$item[path] = str_replace('JSON/','',$item[path]);		
					}
			}
			
			$pages	= $this->_loadPageData($item, $primary);
			
			$return			= '';
		
			if ($primary && !count($pages)) return $this->_show404('Page not found in database.');

			foreach ($pages as $page) {
				
				if ($page['template_options']['xml_action'] == 'deny') {
	
					if ($primary) {
						// Only show friendly error is this is a top level request.
						show_error('The page you requested has not been enabled for XML viewing.');
					} else {
						// TODO: Make it more obvious when a page is being omitted. It can get confusing when listing children.
						//$return .= '<!-- Skipping page '.$page['page_id'].': Has not been enabled for XML viewing.	 -->';
						//continue;
					}
	
				} else if ($page['type'] == 'redirect') {
	
					$this->_show404('Redirect pages can not be viewed as XML.');
					return false;						
	
				}
	
				// Stuff to do if this is the primary request only
				if ($primary) {
	
					$this->current_id 		= $page['page_id'];
					$this->current_title	= $page['title'];
					$this->layout->appendTitle($page['title']);
	
					if ($this->SITE_CONF['cache_enabled'] && ($page['template_options']['cache_time'] != 'none')) {
						$this->page_cache_period = $page['template_options']['cache_time'];
					}
	
					// React to different page types

					if (($page['type'] == 'redirect') && !empty($page['options_json']['redirect_path'])) {
						// REDIRECT PAGE TYPE
						redirect($page['options_json']['redirect_path'], 'location', 301);	
					} else if (($page['type'] == 'secure_section') || ($page['type'] == 'secure_page')) {	
						$this->page_cache_period 	= false;
						$this->_loginSecureUser($page);	
					}

				}
	
	
				// Static page request. This needs to happen after the primary information above is set.
				if ($page['type'] == 'static') {

					$return .= $page['content'];
				
				} else {
	
					$transform_xsl = false;	
	
					if (in_array($page['type'], array('section', 'mirror_section', 'mirror_section_source', 'mirror_calendar', 'mirror_calendar_source')) && (strpos($page['template_options']['xml_action'], 'show_w_') === 0)) {

						// Return children

						$xml_action		= $page['template_options']['xml_action'];
						$get_children	= (is_bool($get_children)) ? $get_children : TRUE;
		
						// Is this a section, get it's children
						if ($get_children) {

							$next_children 		= ($xml_action == 'show_w_all') ? TRUE : FALSE;	
							$l_item 			= array('parent_id' => $page['page_id']);
							$page['children']	= $this->_loadPageXML($l_item, FALSE, $next_children);
		
						}	
					
					}

					if ($primary && !empty($page['template_xml_xsl_path'])) {
						$transform_xsl = $page['template_xml_xsl_path'];
					}
		
					// Assign attributes
					if (!empty($this->PAGE_CONF['xml_attribute_nodes'])) {
						foreach ($this->PAGE_CONF['xml_attribute_nodes'] as $node){
							if (isset($page[$node])) $page['@'.$node] = $page[$node];
						}
					}
					
					// Strip out unwanted values
					if (!empty($this->PAGE_CONF['xml_remove_nodes'])) {
						foreach ($this->PAGE_CONF['xml_remove_nodes'] as $node){
							if (isset($page[$node])) unset($page[$node]);
						}
					}
					
				//	pr($page);
					
					if ($transform_xsl) {
						$return .= $this->xsl_transform->transform($transform_xsl, array('page' => $page));
					} else {
						// Plain item xml
						$return .= array2XML($page);
					}

				}
			
			}

			// We need this for caching
			if (!empty($page['page_id'])) {
				$this->page_cache_id = $page['page_id'];
			}
			
			return $return;				
		
	}


	private function _loadPageData($item=array(), $first=FALSE, $select_set='_default') {	

			$relations		= ($this->PAGE_CONF['load_page_relations']) ? TRUE : FALSE;
			$pages			= $this->page_model->related($relations)->get($item, $select_set);

			return $pages;
			
	}
	

	// ------------------------------------------------------------------------	
	// LOAD CHILDREN (Shared)

	private function _loadPageChildren($parent, $get_children=NULL) {	
	
	
	}

	// ------------------------------------------------------------------------	
	// LOAD FILE

	private function _loadSitemapXML() {
			
			$this->layout->setLayout('xml');

			// Make sure it's off, will screw up rendering
			$this->output->enable_profiler(FALSE);			

			$pages 		= $this->page_model->getForSitemap();
			$page_data 	= $this->load->view($this->SITE_CONF['sitemap']['view'], array('pages'=>$pages), TRUE);

			return $page_data;	
	}


	// ------------------------------------------------------------------------	
	// SERVICE
	/*
	private function _performService($path) {
	
			if (!strlen($path)) {
				$this->output->showData(array2XML(array('status'=>'ERROR', 'message'=> 'Invalid service.')));
				return FALSE;
			}
			
			switch (trim($path, '/')) {

				case 'subscribe':
				
					// Ajax requests only please
					if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || ($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest')) {
						//redirect('/');
					}

					if (!($email = $this->input->get_post('email', TRUE))) {
						$this->output->set_output(array2XML(array('status'=>'ERROR', 'message'=> 'Please supply your email address.')));
						return FALSE;
					}

					$fp = fopen(DOCROOT . 'cms/files/subscribers.txt', 'a');
					fwrite($fp, $email.NL);
					fclose($fp);					

					$this->output->set_output(array2XML(array('status'=>'OK')));
				
					break;

				case 'subscribeCM':

					// Ajax requests only please
					if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || ($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest')) {
						redirect('/');
					}
				
					if (!($fields = $this->input->get_post('subscribe'))) {
						$this->output->set_output(array2XML(array('status'=>'ERROR', 'message'=> 'Invalid subscription. Please supply your email address.')));
						return FALSE;
					}
										
					if (empty($fields['email'])) {
						$this->output->set_output(array2XML(array('status'=>'ERROR', 'message'=> 'Invalid subscription. Please supply your email address.')));
						return FALSE;					
					}
					
					// Need to declare it traditional way instead of using loader or it conflicts

					include_once(APPPATH.'libraries/CampaignMonitor.php');

					$cm_config 			= array(
						'api_key'		=> $this->SITE_CONF['campaign_monitor']['api_key']
						,'client_id'	=> $this->SITE_CONF['campaign_monitor']['client_id']
						);
					
					$CampaignMonitor 	= new CampaignMonitor($cm_config);

					$customfields		= array(
						'mobile'		=> ((!empty($fields['getmobile']) && ($fields['getmobile'] == 'on')) ? $fields['mobile'] : '')
						);
					
					// Master list					
					$CampaignMonitor->subscriberAddWithCustomFields($fields['email'], '', $customfields, $this->SITE_CONF['campaign_monitor']['master_list_id']);
				
					if (!empty($fields['designer']) && count($fields['designer'])) {
						
						foreach ($fields['designer'] as $list_id) {
							$list_subscribe = $CampaignMonitor->subscriberAddWithCustomFields($fields['email'], '', $customfields, $list_id);					
						}				
					
					}				
					
					$this->output->set_output(array2XML(array('status'=>'OK')));

					break;
				
				default;
					$this->output->set_output(array2XML(array('status'=>'ERROR', 'message'=> 'Unkown service.')));
					break;
			}

	}
	
	*/


	// ------------------------------------------------------------------------	

	private function _passwordProtectSite() {
		
		$cookie_name 	= 'secure_website';
		$cookie_value 	= '1';

		
		// Is password protection turned off?
		if ($this->SITE_CONF['password']['protect_site'] === FALSE) {
			return FALSE;
		}
		
		// Make sure this is a domain that should be limited	
		if ((strlen($this->SITE_CONF['password']['domain']) && ($this->SITE_CONF['password']['domain'] != $_SERVER['SERVER_NAME']))) {
			return FALSE;
		}	

		// Is the user already authenticated?
		if ($this->session->userdata($cookie_name) == $cookie_value) {
			return FALSE;
		}
		
		// Is user submitted a successful login?
		if ($this->input->post('password') == $this->SITE_CONF['password']['password']) {
			$this->session->set_userdata($cookie_name, $cookie_value);
			return FALSE;
		}

		if ($this->input->post('password')) {
			$this->layout->setMessage('Invalid login.', 'page', 'error');
		}
		
		// By default let's ask for password
		return TRUE;	
	
	}

	private function _loginSecureUser($page) {
	
			$path			= trim($page['path'], '/');
			$path_parts		= explode('/', $path);
			
			$secure_section	= $path_parts[0];
			
			$session_info	= $this->session->userdata('secure_section');

			$this->load->model('user_model');

			// NEW LOGIN ATTEMPT

			if ($this->input->get_post('secure_username') || $this->input->get_post('secure_password')) {
				
				// Clear any previous sessions
				$this->session->unset_userdata('secure_section');
				$session_info = array();

				$username 	= $this->input->get_post('secure_username');
				$password 	= $this->input->get_post('secure_password');

				if ($page['type'] == 'secure_section') {
					// This is a section, need to get page so we have auth info
					$secure_page_path 	= $page['path'].'/'.$username;
					$secure_page 		= $this->page_model->getByPath($secure_page_path);
					$secure_page		= (count($secure_page)) ? $secure_page[0] : FALSE;
				} else {
					$secure_page 		= $page;
					$secure_page_path	= $page['path'];
				}


				if (!empty($secure_page['options']) && !is_array($secure_page['options'])) {
					$secure_page['options'] = json_decode($secure_page['options'], TRUE);
				}

				if ($secure_page && !empty($secure_page['options']['secure_password']) && ($secure_page['options']['secure_password'] == $password)) {

					// Sweet, authenticated!
					$session_info[$secure_section] = array(
						'username'	=> $username
						, 'path'	=> $secure_page_path
						, 'key'		=> $this->user_model->encryptString($secure_page_path)
						);
					$this->session->set_userdata('secure_section', $session_info);
					if ($page['type'] == 'secure_section') {
					
						if ($this->request_format == 'xml') {
							$secure_page_path = $this->SITE_CONF['xml_uri_trigger'] . $secure_page_path;
						}
					
						redirect($secure_page_path);
					} else {
						return TRUE;
					}
					
					// Failed login attempt
					if ($this->input->post('redirect')) {				
						redirect($this->input->post('redirect').'?error=login');
					}

				} 
			
			// USER ALREADY LOGGED IN

			} else if (!empty($session_info[$secure_section])) {

				// User user is logged into this section, must confirm
				$session_section = $session_info[$secure_section];
				
				if ($page['type'] == 'secure_section') {

					if ($this->request_format == 'xml') {
						$secure_page_path = $this->SITE_CONF['xml_uri_trigger'] . $session_section['path'];
					}

					// Just viewing the section, redirect to user's page
					redirect($secure_page_path);

				} else {

					// Viewing page already, make sure they are auth'd thou
					if ($this->user_model->encryptString($page['path']) == $session_section['key']) {
						// Everything is ok, allow them to continue
						return TRUE;
					}

				}
			}			


 			// If we got this far it was a failed login attempt. 
 
			if ($this->request_format == 'xml') {
				show_error('Unauthorized user.');			
			} else if ($page['type'] == 'secure_page') {
				// Secure page, redirect to parent
				redirect($page['parent_path'].'?error=auth&page='.$secure_page['page_id']);
			} else {
				// Secure section, show page content (it shoud have a login form)
				return TRUE;
			}
			
			show_error('You are not authorized to view this page.');
			
	}
	
	
	private function _show404($message=null) {
			
			// First let's confirm there isn't a redirect set up for this url
			$this->load->model('page_redirect_model');
			$new = $this->page_redirect_model->getByPath($this->request_path);

			if (count($new) && strlen($new[0]['new_path'])) {
				redirect($new[0]['new_path'], 'location', 301);
				exit();
			}

			// It's a normal 404, please continue....

			// Clear out request path. Can be used to inject code into output.
			$this->layout->addJavaScriptVar('CURRENT_URI', '');

			$this->layout->addMeta('reason_404', $message);

			$this->request_path_404 = $this->request_path;
						
			// Notify
			if (!empty($this->SITE_CONF['custom_404']['notification']) && strlen($this->SITE_CONF['custom_404']['notification'])) {
				$this->_notify404($this->SITE_CONF['custom_404']['notification']);
			}

			if (($this->SITE_CONF['custom_404']['enabled'] === TRUE)
				&& ($this->request_path != $this->SITE_CONF['custom_404']['path'])
				&& ($this->request_format != 'xml')) {

				$this->request_path = $this->SITE_CONF['custom_404']['path'];

				// Make sure we send 404 headers
				$this->output->set_status_header('404');
				
				return $this->loadPage(array('path' => $this->SITE_CONF['custom_404']['path']), NULL, FALSE, FALSE);

			} else {

				// Show traditional 404 error (APPPATH/errors/error_404)
				show_404();

			}

			exit('Page not found.');
			
	}

	private function _notify404($to=null) {

			$message		= '404 Notification';
			$subject		= '404 Notification';
			$from_email		= 'website@'.$_SERVER['HTTP_HOST'];
			$from_name		= 'Website';

			$data = array(
				'Referred from page'		=> $_SERVER['HTTP_REFERER'],
				'This request came from'	=> $_SERVER['REMOTE_ADDR'],
				'Forwarded for'				=> $_SERVER['HTTP_X_FORWARDED_FOR']
				);

			$message = '';

			$message	.= '<br/><br/><table width="650" cellpadding="6" cellspacing="4" border="0" bgcolor="#dddddd">';
			foreach ($data as $key => $val) {
				$return .= '<tr><th width="200" align="left" valign="top">'.$key.'</th><td bgcolor="#ffffff">'.$val.'</td></tr>';
			}
			$message	.= '</table>';

			$message	.= '<br/><br/><table width="650" cellpadding="6" cellspacing="4" border="0" bgcolor="#dddddd">';
			foreach ($_SERVER as $key => $val) {
				$return .= '<tr><th width="200" align="left" valign="top">'.$key.'</th><td bgcolor="#ffffff">'.$val.'</td></tr>';
			}
			$message	.= '</table>';
			
			CI()->load->library('email', array('mailtype'=>'html'));
			CI()->email->from($from_email, $from_name);
			CI()->email->to($to);
			CI()->email->subject($subject);
			CI()->email->message($message);
			CI()->email->send();

	}


	// ------------------------------------------------------------------------	

	private function _checkSiteStatus() {

			if (!$this->SITE_CONF['site_active']) {
				show_error('This site currently not active.');
			}
	
	}


	// ------------------------------------------------------------------------	

	private function _prepareForOutput() {

			$uri = rtrim($this->uri->uri_string(), '/');

			if (strpos($uri, $this->SITE_CONF['xml_uri_trigger']) === 0) {

				// XML REQUEST

				$this->request_format = 'xml';
				$this->layout->setLayout('xml');

				// Remove trigger from uri before db look up.
				$uri = substr($uri, strlen($this->SITE_CONF['xml_uri_trigger']));		

				// Make sure it's off, will screw up rendering
				$this->output->enable_profiler(FALSE);

			} else if (strpos($uri, $this->SITE_CONF['pdf_uri_trigger']) === 0) {

				// POPUP REQUEST

				$this->request_type 	= 'pdf';
				$this->request_format 	= 'html';
				$this->layout->setLayout('pdf');

				// Remove trigger from uri before db look up.
				$uri = substr($uri, strlen($this->SITE_CONF['pdf_uri_trigger']));		

			} else if (strpos($uri, $this->SITE_CONF['popup_uri_trigger']) === 0) {

				// POPUP REQUEST

				$this->request_format = 'html';
				$this->layout->setLayout('popup');

				// Remove trigger from uri before db look up.
				$uri = substr($uri, strlen($this->SITE_CONF['popup_uri_trigger']));		
			
			/*
			} else if (strpos($uri, $this->SITE_CONF['service_uri_trigger']) === 0) {

				// SERVICE REQUEST
				$this->request_type 	= 'service';
				$this->request_format 	= 'xml';
				$this->layout->setLayout('xml');

				// Remove trigger from uri before db look up.
				$uri = substr($uri, strlen($this->SITE_CONF['service_uri_trigger']));		

				// Make sure it's off, will screw up rendering
				$this->output->enable_profiler(FALSE);
		
			*/

			} else  {
			
				// HTML REQUEST (DEFAULT)

				// Header meta stuff. This needs to be done before we process the page

				if (!empty($this->SITE_CONF['meta']['head_extra'])) {
					$this->layout->addHeadMisc($this->SITE_CONF['meta']['head_extra']);
				}
				if (!empty($this->SITE_CONF['meta']['meta_description'])) {
					$this->layout->addMeta('og:desc', $this->SITE_CONF['meta']['meta_description'], TRUE, 'property');
					$this->layout->addMeta('description', $this->SITE_CONF['meta']['meta_description'], TRUE);
				}
				if (!empty($this->SITE_CONF['meta']['meta_image'])) {
					$this->layout->addMeta('og:image', $this->SITE_CONF['meta']['meta_image'], TRUE, 'property');
				}

				$this->request_format = 'html';
				$this->layout->setLayout('default');
			
			}

			$uri = strlen($uri) ? $uri  : $this->SITE_CONF['default_path'];

			$this->request_path = $uri;
	
	}

	// ------------------------------------------------------------------------	

	function _formatOutput($content='', $return=FALSE) {
	
			if ($this->request_type == 'pdf') {
				
				$this->output->set_header('Content-Type: text/html');

				require_once(DOCROOT .'/'. APPPATH .'libraries/dompdf/dompdf_config.inc.php');

				$output = $this->layout->wrap($content, $return);	

				$title	= $this->layout->getTitle().'.pdf';

				if ($this->input->get('output')) {
					
					echo $output;
				
				} else {

					$dompdf = new DOMPDF();
					$dompdf->set_base_path(DOCROOT);
					$dompdf->load_html($output);
					$dompdf->render();
					$dompdf->stream($title);

				}

			} else {
	
				$this->layout->addMeta('author', $this->SITE_CONF['author']);
				$this->layout->addMeta('generator', $this->SITE_CONF['application_name'].' '.$this->SITE_CONF['application_version']);
				$this->layout->addMeta('cms', $this->SITE_CONF['application']);
				$this->layout->addMeta('cms-version', $this->SITE_CONF['application_version']);
				$this->layout->addMeta('cms-pagegenerated', date(DATE_DB_FORMAT));
				$this->layout->addMeta('cms-sitepath', SITEPATH);
				$this->layout->addMeta('cms-requestpath', $this->request_path);
	
				$output = $this->layout->wrap($content, $return);	
	
				if ($return) {
					return $output;
				} else {
					echo $output;
				}
			
			}
	
	}

}