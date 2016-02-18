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
			$result 	= $proc->transformToXML($inputdom);

			// http://www.php.net/manual/en/xsltprocessor.transformtoxml.php#62081
	  		//$result = $proc->transformToDoc($inputdom);
			//$result = $result->saveHTML();
	
			restore_error_handler();
			
			// Strip out any <?xml stuff at top out output
			$result = preg_replace('/\<\?xml.+\?\>/', '', $result, 1);
	
			CI()->benchmark->mark('xsl_transform ('.$xsl_file.')_end');
	
			return $result;
	
	}

	public function _getXSL($xsl_file=null) {
	
			// Error checking
			if (is_null($xsl_file) || empty($xsl_file)) show_error('Could not create page. XSL template missing from transformation request.');

			$server_path =  DOCROOT . zonepath($xsl_file);
			
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

			$xml_data = mb_convert_encoding($xml_data,'UTF-8','UTF-8');	
			
			return $xml_data;
	
	}

	private function _allowedFunctions() {

			$allowed	= $this->_allowed;
			$xsl_func 	= get_class_methods('XSL_Functions');

			foreach ($xsl_func as $f) {	
				$allowed[] = 'XSL_Functions::'.$f;
			}
			
			return $allowed;
	
	}

	static public function handleError($errno, $errstr, $errfile, $errline) {
			show_error($errstr);
	}

}

class XSL_Functions {

	var $returnStart 	= '';
	var $returnEnd 		= '';
	
	static function setBodyClass($params) {

			$params		= self::_extractParams($params);
			$replace 	= (!empty($params['replace']) && ($params['replace'] == 'TRUE')) ? TRUE : FALSE;

			if (!empty($params['class'])) {
				CI()->layout->setBodyClass($params['class'], $replace);
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

				// Special call, will check for cached items.
				$page = CI()->loadPage(array('path' => $params['path']), 'html', FALSE);
		
			} else if (!empty($params['id']) && (CI()->zone == 'website')) {

				// Special call, will check for cached items.
				$page = CI()->loadPage(array('page_id' => $params['id']), 'html', FALSE);

			}

			if ($page) {
				return self::_outputArray($page);
			}

	}

	static function loadPageXML($params) {

			$params			= self::_extractParams($params);
			$return 		= '';
			$return_first 	= (!empty($params['first']) && ($params['first'] == 'TRUE')) ? TRUE : FALSE;
			$page			= false;
			$select_set 	= (!empty($params['content']) && ($params['content'] == 'TRUE')) ? NULL : 'navigation';
			$sort_field 	= (!empty($params['sort'])) ? $params['sort'] : null;
			$do_auto_join 	= (!empty($params['auto_join']) && ($params['auto_join'] == 'FALSE')) ? FALSE : TRUE;
			$cache_key		= FALSE;




			if (!empty($params['cache']) && ($params['cache'] == 'TRUE')) {
				unset($params['cache']);

				$cache_key	= preg_replace("/[^a-z\-_\d]/i", "", json_encode($params));
				$cached		= CI()->page_model->getCache($cache_key);

				if ($cached) {
					return self::_outputArray($cached);
				}

			}
								
			if (!empty($params['path']) && (CI()->zone == 'website')) {
								
				// Special call, will check for cached items.
				// $page 	= CI()->loadPage(array('page_id' => $id ), 'xml', array('return_first'=>$return_first, 'is_child'=>TRUE), FALSE);

			} else if (!empty($params['template_id'])) {
				$page 	= CI()->page_model->sort($sort_field)->doAutoJoin($do_auto_join)->get(array('template_id'=>$params['template_id']), $select_set);
			} else if (!empty($params['parent_id'])) {
				$page 	= CI()->page_model->sort($sort_field)->doAutoJoin($do_auto_join)->getByParentId($params['parent_id'], $select_set);
			} else if (!empty($params['id'])) {
				$page 	= CI()->page_model->sort($sort_field)->doAutoJoin($do_auto_join)->getById($params['id'], $params, NULL, $return_first);			
			}
			
			if ($cache_key) {
				CI()->page_model->setCache($cache_key, $page);
			}

			if ($page) {
				return self::_outputArray($page);
			}
	
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

			$params			= self::_extractParams($params);
			$config_items 	= CI()->loadConfig($params['config_name']);

			return self::_outputArray($config_items);
	
	}
	
	static function getPostParams($params) {

			$req = $_POST;
			return self::_outputArray($req);
	
	}

	static function getUrlParams($params) {

			$req = $_GET;
			return self::_outputArray($req);
	
	}

	static function json_decode($params) {

			$result = json_decode($params, true);

			if (is_array($result)) {
				return self::_outputArray($result);
			}
		
	}

	static function rand($params) {

			$params = self::_extractParams($params);

			$p_min	= (!empty($params['min'])) ? $params['min'] : null;
			$p_max	= (!empty($params['max'])) ? $params['max'] : null;

			return rand($p_min, $p_max);

	}		

	static function stringMaxLength($params) {

			$params = self::_extractParams($params);

			$string = $params['string'];

			if (!empty($params['strip_tags'])) $string = strip_tags($string);

			$length = (!empty($params['length'])) ? $params['length'] : 200;
			
			if (strlen($string) > $length) {
				$string = trim((preg_match('/^(.*)\W.*$/', substr($string, 0, $length+1), $matches) ? $matches[1] : substr($string, 0, $length)), '&nbsp;');
				if (substr($string, -1, 1) != '.') $string .= '...';					
			}  

			return $string;
	
	}


	static private function _outputString($string='') {
			return $this->returnStart . $string . $this->returnEnd;
	}

	static private function _outputArray($array=array()) {
	
			// Convert to XML
			$output_dom = new DomDocument('1.0', 'UTF-8');
	
			$output = array2XML($array);
	
			// Strip out any <?xml stuff
			$output = preg_replace('/\<\?xml.+\?\>/', '', $output, 1);
	
			$output_dom->loadXML($output);
	
			return $output_dom;
	
	}

    static public function _extractParams($params) {

    		if (strpos($params, '{') === 0) {
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