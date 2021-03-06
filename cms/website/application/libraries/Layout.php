<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Typology CMS - Layout Libraruy
 *
 * @author      VSA Partners / Louis D Walch (lwalch@vsapartners.com)
 * @link        http://www.vsapartners.com
 *
 */
 
 
class Layout {

	var $config;

	var $_wrap_output		= FALSE;

	var $_layout			= 'default';
	var $_layouts			= array();
	var $_format			= 'html';
	
	var $_is_ajax			= FALSE;
			
//	var $_wrapper			= 'default';
//	var $_wrapper_regions	= array();

	var $_region_data		= array();
	

	var $_body_id			= null;
	var $_body_class		= array();
	var $_body_script		= array();
	var $_page_title		= array();

//	var $_data;
	var	$_head_misc			= '';
	var $_meta				= array(
		'name'				=> array()
		, 'property'		=> array()
		);
	var $_js_vars			= array();
	var $_messages			= array(
		'page'				=> array()
		, 'modal'			=> array()
		);
	var $_message_tag		= '<div class="message %s">%s</div>';
	var $_messages_tag		= '<div class="page_messages">%s</div>';

	// What is this?
//	var $preview			= FALSE;
	
	var $assets				= null;
	
	/* HTML Tag Templates */
	var $_meta_tag			= '<meta %s="%s" content="%s" />';

	
	public function __construct($config=array()) {
	
			$this->_layouts = $config['layouts'];
			
			CI()->load->library('layoutAssets', $config);
			$this->assets = CI()->layoutassets;
			
			$this->addJavaScriptVar('SITEPATH', SITEPATH);
			$this->addJavaScriptVar('CURRENT_URI', CI()->current_uri);
//			$this->addJavaScriptVar('MODAL_MESSAGES', '');

			// Check for messages set in flash data
			if (CI()->session->flashdata('error')) $this->setMessage(CI()->session->flashdata('error'), 'modal', 'error');
			if (CI()->session->flashdata('message')) $this->setMessage(CI()->session->flashdata('message'), 'modal', 'message');

			if (CI()->is_ajax) {
				CI()->output->enable_profiler(FALSE);
			}

			if (count($this->_layouts)) $this->_wrap_output = TRUE;
	
	
	}

	// ------------------------------------------------------------------------
	// OUTPUT
	
	public function show($data='') {
			CI()->output->append_output($data);
	}


	public function wrap($data=null) {
	
			if ($this->_wrap_output === TRUE) {

				// TODO: Check config if we should be auto switching this				
				if (CI()->is_ajax === TRUE) {
					$this->setLayout('ajax');
				}
				
				$layout = $this->getLayout();
				
//				if (!empty($layout['format'])) { $this->setFormat($layout['format']); }

				if (!empty($layout['wrapper'])) {
				
					if (!empty($layout['assets'])) {
						$this->assets->addSets($layout['assets']);
					}
	
					$this->_region_data['content'] = $data;
					return CI()->load->view($layout['wrapper'], null, TRUE);		
				
				}
			} else {
				$data .= '<!-- Layout wrap disabled -->';	
			}
			
			return $data;
			
	}
	

	// ------------------------------------------------------------------------
	// HEAD
	
	public function getHead() {
	
			return 
				$this->getMetaTags()
				. $this->getJavaScriptVars()
				. $this->assets->getTags('css')
				. $this->assets->getTags('cssiphone')
				. $this->assets->getTags('js','top')
				. $this->assets->getTags('js','page')
				. (!empty($this->_head_misc) ? (chr(10).$this->_head_misc.chr(10)) : '');
	
	}



	public function addMeta($name='', $content='', $replace=true, $group='name') {
			
			if (is_array($name)) {

				foreach($path as $add) $this->addMetaTag($name, $content, $replace, $group);

			} else if (strlen($name)) {
				
				if ($replace || empty($this->_meta[$group][$name])) {
					$this->_meta[$group][$name] = $content;
				} else {
					$this->_meta[$group][$name] .= ' ' . $content;
				}

			}	

	}

	public function addHeadMisc($string) {
			$this->_head_misc .= NL.$string;
	}

	public function getMetaTags() {

			if (!count($this->_meta)) return false;
			
			$return 	= '';

			foreach($this->_meta as $group => $tags) {	
				foreach($tags as $name => $content) {
					$return .= NL.TAB.sprintf($this->_meta_tag, $group, $name, $content);
				}
			}
			
			return $return;

	}


    public function addJavaScriptVar($name='', $content='') {
            
            if (strlen($name)) {
                $content = is_array($content) ? json_encode($content) : '"'.$content.'"';
                $this->_js_vars[$name] = $content;
            }    

    }

    public function getJavaScriptVars() {
    
            $return =  NL .TAB. '<script language="javascript">';

            foreach ($this->_js_vars as $name => $content){
                $return .= NL .TAB.TAB. 'var '.$name.' = '.$content.';';        
            }
            
            $return .= NL .TAB. '</script>';
            
            return $return;

    }


	// ------------------------------------------------------------------------
	// MESSAGES	
	
	public function setMessage($msg=null, $location='modal', $type='message') {
	
			if (!is_null($msg)) $this->_messages[$location][$type][] = $msg;
			
			return TRUE;	
	
	}

	public function getMessages($location='page', $type=null) {

			$output	= '';

			if (is_null($type)) {
			
				// Getting all messages for location
				if (count($this->_messages[$location])) {
					foreach ($this->_messages[$location] as $type => $values) {
						$output .= $this->getMessages($location, $type);					
					}				
				}
			
			} else if (array_key_exists($type, $this->_messages[$location]) && count($this->_messages[$location][$type])) {
				
				if ($location == 'modal') {
				
					// Do somehting special
				
					$this->addJavaScriptVar('MODAL_MESSAGES', $this->_messages[$location][$type]);
				
				} else {
				
					// Page
				
					$messages = '';
				
					foreach ($this->_messages[$location][$type] as $message) {
						$messages .= sprintf($this->_message_tag, $type, $message);
					}
					
					$output .= sprintf($this->_messages_tag, $messages);
				
				}
				
			}
			
			return $output;
			
	}


	// ------------------------------------------------------------------------
	// FOOT

	public function getFoot() {
			
			return $this->_getGoogleTracking() 
				 . $this->_checkHighlight()
				 . $this->assets->getTags('js','bottom');
	
	}
	
	public function _getGoogleTracking() {
	
			if (!CI()->SITE_CONF['google_analytics_id']) return;
			/*			
			return '<script type="text/javascript">'
				 . NL . TAB . 'var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");'
				 . NL . TAB .'document.write(unescape("%3Cscript src=\'" + gaJsHost + "google-analytics.com/ga.js\' type=\'text/javascript\'%3E%3C/script%3E"));'
 				 . NL . TAB .'</script>'
 				 . NL . TAB .'<script type="text/javascript">try {var pageTracker = _gat._getTracker("'.CI()->SITE_CONF['google_analytics_id'].'");pageTracker._trackPageview();} catch(err) {}</script>'
 				 .'';
 			*/

			return NL . TAB . "<script>"
				. NL . TAB . "(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){"
				. NL . TAB . "(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),"
				. NL . TAB . "m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)"
				. NL . TAB . "})(window,document,'script','//www.google-analytics.com/analytics.js','ga');"
				. NL . TAB . "ga('create', '".CI()->SITE_CONF['google_analytics_id']."', 'auto');"
				. NL . TAB . "ga('send', 'pageview');"
				. NL . TAB . "</script>";


	
	}
	private function _checkHighlight($name='hilight') {

			$output = '';
		
			if(CI()->session->flashdata($name)) {
			
				$output = '<script language="javascript">'
						. NL . 'highlight("'.CI()->session->flashdata($name).'")'
						. NL . '</script>';
					
			}
	
			return $output;
	}

	
	// ------------------------------------------------------------------------
	// SIMPLE GETTERS AND SETTERS


	public function setRegionData($name='', $data=null, $replace=true) {
			
			if (is_null($data)) return FALSE;

			if ($replace) {
				$this->_region_data[$name] = $data;
			} else {
				$this->_region_data[$name] .= $data;
			}		
			
			return $this;
			
	}

	public function getRegionData($name=null) {
	
			if (is_null($name) || !array_key_exists($name, $this->_region_data)) return FALSE;

			return $this->_region_data[$name];	
	
	}


	public function setLayout($layout='') {
	
			if (array_key_exists($layout, $this->_layouts)) {
				$this->_layout = $this->_layouts[$layout];
				
				if (!empty($this->_layout['format'])) {
					$this->setFormat($this->_layout['format']);
				}
				
			}
			return $this;
	}

	public function getLayout() {
			return $this->_layout;
	}

	public function setFormat($format=null) {
			if (!is_null($format)) $this->_format = strtolower($format);

			switch($format) {
				case 'xml':
					CI()->output->set_header('Content-Type: text/xml');
					break;
				default:
					break;
			}
			return $this;

	}

	public function getFormat() {
			return strtolower($this->_format);
	}

	public function setTitle($data='') {
			$this->_page_title = strlen($data) ? array($data) : array();
			return $this;
	}

	public function appendTitle($data='') {
			if (strlen($data)) $this->_page_title[] = $data;
			return $this;
	}

	public function getTitle() {
			return implode(CI()->SITE_CONF['title_seperator'], $this->_page_title);
	}

	public function setBodyScript($value=false, $replace=false) {
			if ($value) {
				$value = trim($value);
				if ($replace) {
					$this->_body_script = array($value);
				} else {
					$this->_body_script[] = $value;
				}
			} else {
				$this->_body_script[] = array();
			}
			return $this;
	}
	
	public function getBodyScript($default=false) {
			if ($default) $this->setBodyScript($default);
			return (count($this->_body_script)) ? 'data-script="'.implode(' ', $this->_body_script).'"' : '';
	}

	public function setBodyClass($value=false, $replace=false) {
			if ($value) {
				if ($replace) {
					$this->_body_class = array($value);
				} else {
					$this->_body_class[] = $value;
				}
			} else {
				$this->_body_class[] = array();
			}
			return $this;
	}
	
	public function getBodyClass($default=false) {
			if ($default) $this->setBodyClass($default);
			return (count($this->_body_class)) ? 'class="'.implode(' ', $this->_body_class).'"' : '';
	}
	
	public function setBodyId($value) {
			if ($value) {
				$this->_body_id = $value;
			} else {
				$this->_body_id = '';
			}
			return $this;
	}
	
	public function getBodyId($default=false) {
			if ($default) $this->setBodyId($default);
			return ($this->_body_id) ? 'id="'.$this->_body_id.'"' : '';
	}


}