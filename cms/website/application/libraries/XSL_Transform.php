<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Typology CMS
 *
 * @author      VSA Partners / Louis D Walch (lwalch@vsapartners.com)
 * @link        http://www.vsapartners.com
 *
 */
 
 
 
class Xsl_transform {

	var $_allowed = array('str_replace');
	
	public function __construct() {
			CI()->load->helper('string');
	}
	
	public function transform($xsl_file=null, $xml_data=null) {

			CI()->benchmark->mark('xsl_transform ('.$xsl_file.')_start');
	
			set_error_handler(array('XSL_Transform', 'handleError'));
	
			$xsl 		= new DOMDocument('1.0','UTF-8');
			$xsl->load($this->_getXSL($xsl_file));
		
			$inputdom 	= new DomDocument('1.0','UTF-8');
			$inputdom->loadXML($this->_getXML($xml_data));
				
			$proc 		= new XsltProcessor();
			$proc->importStylesheet($xsl);
			$proc->registerPhpFunctions($this->_allowedFunctions());
			// $result 	= $proc->transformToXML($inputdom);

			$proc->setParameter('', 'ISAJAX', CI()->is_ajax);

			$result_doc = $proc->transformToDoc($inputdom);
			$result 	= $result_doc->saveXML();
	
			restore_error_handler();
			
			// Strip out any <?xml stuff at top out output
			$result = preg_replace('/\<\?xml.+\?\>/', '', $result, 1);
	
			CI()->benchmark->mark('xsl_transform ('.$xsl_file.')_end');
	
			return $result;
	
	}

	public function _getXSL($xsl_file=null) {

			// Error checking
			if (is_null($xsl_file) || empty($xsl_file)) show_error('Could not create page. XSL template missing from transformation request.');

			$server_path = DOCROOT . SITEPATH . $xsl_file;
			$server_path = preg_replace('/(\/+)/','/',$server_path);

			if (!file_exists($server_path)) {
				show_error('Unable to display page, template not found ('.$xsl_file.').');
			}
			
			return $server_path;
	
	}

	public function _getXML($xml_data=null) {
	
			if (is_null($xml_data) || empty($xml_data)) show_error('Could not create page. XML missing from transformation request.');

			// Transform input array to xml?
			if (is_array($xml_data)) $xml_data = array2XML($xml_data);
	
			// Strip out any <? in the xml passed in, it will throw and error
			$xml_data = preg_replace('/\<\?xml.+\?\>/', '', $xml_data, 1);
			
			return $this->_fixEncoding($xml_data);
	
	}

	protected function _fixEncoding($str) {
			if ((mb_detect_encoding($str) == "UTF-8") && mb_check_encoding($str,"UTF-8")) {
				return $str;
			} else {
				return utf8_encode($str);
			}
	}

	private function _allowedFunctions() {

			$allowed	= $this->_allowed;
			$xsl_func 	= get_class_methods('XSL_Functions');

			foreach ($xsl_func as $f) {	
				$allowed[] = 'XSL_Functions::'.$f;
			}

			$allowed[] = 'urlencode';
			$allowed[] = 'date';			
			$allowed[] = 'strip_tags';

			return $allowed;
	
	}

	static public function handleError($errno, $errstr, $errfile, $errline) {
			show_error($errstr);
	}

}


class XSL_Base {

	var $returnStart 	= '';
	var $returnEnd 		= '';
	
	static function getServerAddress() {
			
			$return = $_SERVER['SERVER_NAME'];

			return $return;
	
	}
	

	static function getPageByAttribute($params) {

	    $params = self::_extractParams($params);
	    $return = array();	    
	    $fields	= array();

		if (isset($params['attr_key']) && isset($params['attr_value'])) {
			$fields[$params['attr_key']][] = $params['attr_value'];
			unset($params['attr_key'], $params['attr_value']);
		} else if (isset($params['key'])) {
      		$fields = CI()->input->get_post($params['key']) ? CI()->input->get_post($params['key']) : array();
			unset($params['key']);
   		} else {
   			$fields = $params;
   		}
      	
	    if (!empty($params['content']) && ($params['content'] == 'TRUE')) {
	    	$select_set = '_default';
	    	unset($params['content']);
	    } else {
	    	$select_set = 'navigation';
	    }	    

	    $return['params']   = array_merge($params, $fields);
        $return['results']  = CI()->page_model->getByAttribute($params, $select_set);
	    
		return self::_outputArray($return);
	
	}


	static function getSectionAttributeOptions($template_id) {
	
	    if (empty($template_id)) return;
	    
	    CI()->load->model('page_attribute_model');
	    $data = CI()->page_attribute_model->getSectionAttributeOptions('template_id', $template_id);

		return self::_outputArray($data);
	
	}


	static function loadRelatedPages($params) {
	
			$params		= self::_extractParams($params);
			
			if (empty($params['search']) || empty($params['term'])) return;

			$filter		= array();
			$select_set 	= (!empty($params['content']) && ($params['content'] == 'TRUE')) ? NULL : 'navigation';

			$search 	= '<'.$params['search'].'><![CDATA['.$params['term'].']';
			CI()->db->like('content', $search); 
			
			if (!empty($params['search2'])) {
				$search2 	= '<'.$params['search2'].'><![CDATA['.$params['term2'].']';
				CI()->db->like('content', $search2); 
			}
			
			if (!empty($params['parent_id'])) {
				$filter['parent_id'] = $params['parent_id'];
			}

			if (!empty($params['template_id'])) {
				$filter['template_id'] = $params['template_id'];
			}

			$page 		= CI()->page_model->selectSet($select_set)->get($filter);

			return self::_outputArray($page);	
	
	}
	
	
	static function searchChildren($params) {
	
			$params		= self::_extractParams($params);

			if (empty($params['parent_id'])) return;
			
			$cookie		= 'search'.$params['parent_id'];
			$filters	= CI()->session->userdata($cookie) ? CI()->session->userdata($cookie) : array();
			
			if (!empty($params['uniq'])) {
				$cookie = $params['uniq'].$cookie;
			}

			if (!empty($params['search']) && !empty($params['term'])) {
				$filters[$params['search']]	= $params['term'];
			}
			
			if (CI()->input->get_post('search') && (CI()->input->get_post('term') == '*')) {
				$filters[CI()->input->get_post('search')]	= '';
			} else if (CI()->input->get_post('search')) {
				$filters[CI()->input->get_post('search')]	= CI()->input->get_post('term');
			}

			foreach ($filters as $search => $term) {
				if (strlen($term)) {
					CI()->db->like('content', '<'.$search.'><![CDATA['.$term.']'); 
				}
			}	
			
			$limit		= (!empty($params['limit'])) ? $params['limit'] : false;

			$pages 		= CI()->page_model->limit($limit)->get(array('parent_id'=>$params['parent_id']));
			$pages2		= array();
			
			foreach ($pages as $row) {
			
				if (!empty($row['content_date']) && ($row['content_date'] > 1)) {
					$row['content_date_detail'] = array(
						'weekday'		=> date('l', strtotime($row['content_date']))
						, 'month'		=> date('F', strtotime($row['content_date']))
						, 'month_abv'	=> date('M', strtotime($row['content_date']))
						, 'year'		=> date('Y', strtotime($row['content_date']))
						, 'day'			=> date('j', strtotime($row['content_date']))						
						, 'time'		=> date('g:i A', strtotime($row['content_date']))					
						, 'unix'		=> strtotime($row['content_date'])					
						);
				}
				
				$pages2[] = $row;
			
			}

			
			$return 	= array('filters'=>$filters,'pages'=>$pages2);

			// Save filters for next session
			CI()->session->set_userdata($cookie, $filters);
			
			if (!empty($params['return'])) {
				return $return;
			} else {
				return self::_outputArray($return);	
			}
	
	}

	static function loadRecentPages($params) {
	
			$params		= self::_extractParams($params);

			if (empty($params['template_id']) && empty($params['parent_id'])) return;
			
			$filter		= array();

			if (!empty($params['parent_id'])) {
				$filter['parent_id'] = $params['parent_id'];
			}

			if (!empty($params['template_id'])) {
				$filter['template_id'] = $params['template_id'];
			}

			$limit 		= (!empty($params['limit'])) ? $params['limit'] : '100';
			$select_set = (!empty($params['content']) && ($params['content'] == 'TRUE')) ? NULL : 'navigation';
			$sort 		= (!empty($params['sort'])) ? $params['sort'] : 'publish_date';

			CI()->db->order_by($sort, 'desc');
			CI()->db->limit($limit);

			$page 		= CI()->page_model->selectSet($select_set)->get($filter);

			return self::_outputArray($page);	
	
	}
	
	
	static function randomNumber($params) {

			$params		= self::_extractParams($params);
			
			$min		= !empty($params['min']) ? $params['min'] : 0;
			$max		= !empty($params['max']) ? $params['max'] : null;
			
			return rand($min, $max);
		
	}

	static function addHeadMisc($params) {

			if (!empty($params)) {
				CI()->layout->addHeadMisc($params);
			}
		
	}

	static function setLayout($params) {

			if (!empty($params)) {
				CI()->layout->setLayout($params);
			}
		
	}

	static function setMetaTag($params) {
	
			$params		= self::_extractParams($params);
			$replace 	= (!empty($params['replace']) && ($params['replace'] == 'TRUE')) ? TRUE : FALSE;
			$group 		= (!empty($params['group'])) ? $params['group'] : 'name';

			if (!empty($params['name']) && !empty($params['content'])) {
				CI()->layout->addMeta($params['name'], $params['content'], $replace, $group);
			}
		
	}

	static function setBodyClass($params) {

			$params		= self::_extractParams($params);
			$replace 	= (!empty($params['replace']) && ($params['replace'] == 'TRUE')) ? TRUE : FALSE;

			if (!empty($params['class'])) {
				CI()->layout->setBodyClass($params['class'], $replace);
			}
		
	}

	static function setBodyScript($params) {

			$params		= self::_extractParams($params);
			$replace 	= (!empty($params['replace']) && ($params['replace'] == 'TRUE')) ? TRUE : FALSE;

			if (!empty($params['script'])) {
				CI()->layout->setBodyScript($params['script'], $replace);
			}
		
	}


	
	static function addJS($path){
			if (!empty($path)) {
				CI()->layout->assets->add('js', $path, 'page');
			}
	}

	static function addCSS($path){
			if (!empty($path)) {
				CI()->layout->assets->add('css', $path, 'page');
			}
	}

	static function addJavaScriptVar($params) {

			$params		= self::_extractParams($params);

			if (!empty($params['name'])) {
				CI()->layout->addJavaScriptVar($params['name'], $params['content']);
			}
		
	}

	static function appendTitle($title) {
			CI()->layout->appendTitle($title);
	}
	static function setTitle($title) {
			CI()->layout->setTitle($title);
	}

	static function getSitePath($params) {
			
			$return = SITEPATH;
				
			if (!empty($params) && ($params == 'noslash')) $return = rtrim($return, '/');

			return $return;
	
	}

	static function loadPageHTML($params) {
	
			$params			= self::_extractParams($params);
	
			$return 		= '';
			$return_first 	= (!empty($params['first']) && ($params['first'] == 'TRUE')) ? TRUE : FALSE;
			$page			= false;
				
			if (!empty($params['path']) && (CI()->zone == 'website')) {
				$load_params = array('path' => $params['path']);
			} else if (!empty($params['id']) && (CI()->zone == 'website')) {
				$load_params = array('page_id' => $params['id']);
			}

			$check_cache 	= (!empty($params['cache']) && ($params['cache'] == 'FALSE')) ? FALSE : TRUE;
			$get_children	= (!empty($params['children']) && $params['children'] == 'FALSE') ? FALSE : TRUE;

			$page = CI()->loadPage($load_params, 'html', FALSE, $check_cache, $get_children);
			
			if ($page) {

				// Convert to XML
				$output_dom = new DomDocument('1.0', 'UTF-8');
				$output 	= array2XML($page);
				$node 		= $output_dom->createCDATASection($output);
				$newnode 	= $output_dom->appendChild($node);
		
				return $output_dom;

			}

	}

	static function loadPageXML($params) {
	
			$params			= self::_extractParams($params);

			$return 		= '';
			$return_first 	= (!empty($params['first']) && ($params['first'] == 'TRUE')) ? TRUE : FALSE;
			$page			= false;
			
			//$select_set 	= (!empty($params['content']) && ($params['content'] == 'TRUE')) ? NULL : 'navigation';
			
			if (!empty($params['id'])) {
				$params['page_id'] = $params['id'];
				unset($params['id']);
			}
			
			if (!empty($params['content']) && $params['content'] == 'FALSE') {
			
				// Don't want content, so hit database directly. This request will not be cached.
				
				$select_set = 'navigation';
				
				if (!empty($params['path'])) {
					$page 	= CI()->page_model->selectSet($select_set)->getByPath($params['path']);
				} else if (!empty($params['parent_id'])) {
					$page 	= CI()->page_model->selectSet($select_set)->getByParentId($params['parent_id']);
				} else if (!empty($params['template_id'])) {			
					$page 	= CI()->page_model->selectSet($select_set)->first($return_first)->getByTemplateId($params['template_id']);
				} else if (!empty($params['id'])) {
					$page 	= CI()->page_model->selectSet($select_set)->first($return_first)->getById($params['id']);
				}
				
			} else if (CI()->zone == 'website') {
				
				// Need content, so route through website controller. This will also check for cached items. 
				// Preferred method.

				$check_cache 	= (!empty($params['cache']) && ($params['cache'] == 'FALSE')) ? FALSE : TRUE;
				$get_children	= (!empty($params['children']) && $params['children'] == 'FALSE') ? FALSE : TRUE;

				if (!empty($params['content'])) unset($params['content']);
				if (!empty($params['first'])) unset($params['first']);
				if (!empty($params['nocache'])) unset($params['nocache']);
				if (!empty($params['get_children'])) unset($params['get_children']);

				$page = CI()->loadPage($params, 'xml', FALSE, $check_cache, $get_children);
				$page = '<results>'.$page.'</results>';
				
			}

			if (!empty($params['debug'])) {
				echo CI()->db->last_query();
			}
			
			if ($page) {
				return self::_outputArray($page);
			}
	
	}


	static function sendEmail($params) {

			$params			= self::_extractParams($params);			
					
			
			if (empty($params['to'])) return false;

			CI()->load->helper('inflector');

			$MANAGE_CONF 	= CI()->loadConfig('manage');	

			$fields			= array();

			$to				= $params['to'];
			$message		= (!empty($params['message'])) ? $params['message'] : '';
			$subject		= (!empty($params['subject'])) ? $params['subject'] : '';
			$from_email		= (!empty($params['from_email'])) ? $params['from_email'] : $MANAGE_CONF['default_from_email'];
			$from_name		= (!empty($params['from_name'])) ? $params['from_name'] : CI()->SITE_CONF['site_title'];

			if (!empty($params['data'])) {
				// Passing fields directly in, from another static function
				$fields		= $params['data'];
			} else if (!empty($params['post_key']) && !empty($_POST[$params['post_key']])) {
				// Need to pull fields from the posted data
				$fields = $_POST[$params['post_key']];
			}
			
			if (count($fields)) {
				
				$message	.= '<br/><br/><table width="650" cellpadding="6" cellspacing="4" border="0" bgcolor="#dddddd">';
				$message	.= self::sendEmail_arrayRows($fields);
				$message	.= '</table>';

			}

			$message .= '<br/>';
			$message .= '<br/>User Agent: '.CI()->input->user_agent();
			$message .= '<br/>IP: '.CI()->input->ip_address();
			
			CI()->load->library('email', array('mailtype'=>'html'));
			CI()->email->from($from_email, $from_name);
			CI()->email->to($to);
			CI()->email->subject($subject);
			CI()->email->message($message);
			CI()->email->send();
	
			// CI()->load->library('email', array('mailtype'=>'text', 'protocol' => 'sendmail', 'mailpath' => '/usr/sbin/sendmail -t -i'));
			// echo CI()->email->print_debugger();		
			
			return '';
	
	}

	static function sendEmail_arrayRows($data, $prefix='') {

		$return = '';

		foreach ($data as $key => $val) {

			$key = $prefix.$key;

			if (is_array($val)) {
				$return .= self::sendEmail_arrayRows($val, $key.': ');
			} else {
				$return .= '<tr><th width="200" align="left" valign="top">'.humanize($key).'</th><td bgcolor="#ffffff">'.$val.'</td></tr>';
			}
		}

		return $return;

	}
	
	static function loadFileItem($params) {
	
			$params			= self::_extractParams($params);

			if (!empty($params['id'])) {

				CI()->load->model('file_model');
	
				$file = CI()->file_model->getById($params['id']);
				
				if ($file) {
					return self::_outputArray($file[0]);
				}
			
			}
				
	}				

	static function getApplicationVars($params) {

			$return = '';
		
			$vars = array(
				'APPPATH'		=> APPPATH
				, 'SITEPATH'	=> SITEPATH
				, 'asset_path'	=> CI()->asset_path
				, 'module'		=> CI()->module
				, 'admin_path'	=> (!empty(CI()->admin_path) ? CI()->admin_path : '')
				);
			
			return self::_outputArray($vars);
	
	}

	static function getConfig($params) {

			$params 		= self::_extractParams($params);
			$config_name	= (is_array($params)) ? $params['config_name'] : $params;
			$config_items 	= CI()->loadConfig($config_name);

			return self::_outputArray($config_items);
	
	}

	static function getPostOrUrlParams() {

			if (!empty($_POST)) {
				$data = self::_outputArray($_POST);
			} else if (!empty($_GET)) {
				$data = self::_outputArray($_GET);
			} else {
				$data = array();				
			}  

			$data = CI()->input->xss_clean($data);

			return self::_outputArray($data);

	}
	
	static function getPostParams() {

			$data = !empty($_POST) ? $_POST : array();
			$data = CI()->input->xss_clean($data);

			return self::_outputArray($data);

	}


	static function getUrlParams($params) {

			$data = !empty($_GET) ? $_GET : array();
			$data = CI()->input->xss_clean($data);

			return self::_outputArray($data);

	}

	static function string_length($params) {

			$params = stripslashes($params);
			$params = str_replace("\'", "", $params);
			$params = str_replace(NL, "", $params);

			$params = self::_extractParams($params);
			
			if (!is_array($params)) return '';

			$string = $params['string'];
			
			$append = (!empty($params['append'])) ? $params['append'] : '...';

			if (!empty($params['strip_tags']) && ($params['strip_tags'] == 'TRUE')) $string = strip_tags($string);

			$length = (!empty($params['length'])) ? $params['length'] : 200;
			
			if (strlen($string) > $length) {
				$string = trim((preg_match('/^(.*)\W.*$/', substr($string, 0, $length+1), $matches) ? $matches[1] : substr($string, 0, $length)), '&nbsp;');
				if (substr($string, -1, 1) != '.') $string .= $append;					
			}  

			return $string;
	
	}

	static function date($params) {
			$params			= self::_extractParams($params);
			$string			= date($params['format'], strtotime($params['string']));
			return $string;
	}
	

	static function loadRemoteXML($url) {
	
			if (!empty($url)) {
			
				if (substr($url, 0, 1) == '/') $url = 'http://' . $_SERVER['SERVER_NAME'] . $url;

				$ch = curl_init($url);
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
				$result = curl_exec($ch);

				//pr($url, '$url');
				//pr($result, '$result');

				if (!$result) {
			       // echo curl_error($ch);
			    }
			    
			    curl_close($ch);
			    
			    if ($result) {

					$xml 		= simplexml_load_string($result);
                    $inputdom   = new DomDocument();
                    $inputdom->loadXML($xml->asXML());

					return $inputdom;
			    
			    }

			} else {				
				return false;
			}
	
	}


	static private function _outputString($string='') {
			return $this->returnStart . $string . $this->returnEnd;
	}

	static protected function _outputArray($array=array()) {
	
			// Convert to XML
			$output_dom = new DomDocument('1.0', 'UTF-8');
	
			$output = array2XML($array);

			
			// Strip out any <?xml stuff
			$output = preg_replace('/\<\?xml.+\?\>/', '', $output, 1);
	
			$output_dom->loadXML($output);
	
			return $output_dom;
	
	}

    static public function _extractParams($params) {

    		if (strpos($params, '{') === 0 || strpos($params, '[') === 0) {
    			return json_decode($params, TRUE);
    		} else {
    			return $params;
    		}
    		
    }

    public function __call($name, $arguments) {
		show_error('Error calling unknown function: '.$name);
    }

    public static function __callStatic($name, $arguments) {
		show_error('Error calling unknown function: '.$name);
    }

}


$_include = DOCROOT . 'cms/local/xsl_functions.php';
include($_include);
