<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Typology CMS
 *
 * @author      VSA Partners / Louis D Walch (lwalch@vsapartners.com)
 * @link        http://www.vsapartners.com
 *
 */
 
 
// ------------------------------------------------------------------------

class XML_Builder {
	
	var $PAGE_CONF;

	var $base_node;
	var $edit_page_item;
	var $output_item;


	// ------------------------------------------------------------------------


	function buildEditXML($template_xml, $page_xml) {
			$this->_set_config();


			if (empty($page_xml)) { 
				// Error??
				$page_xml = '<'.$this->base_node.' />';
			}
			if (empty($template_xml)) {
				// Error??
				$template_xml = '<'.$this->base_node.' />';
			}

			$template_item 			= simplexml_load_string($template_xml, 'SimpleXMLElement', LIBXML_NOCDATA);
			$this->edit_page_item 	= simplexml_load_string('<?xml version="1.0" encoding="utf-8"?>' . $page_xml, 'SimpleXMLElement', LIBXML_NOCDATA);
			$this->output_item		= simplexml_load_string('<?xml version="1.0" encoding="utf-8"?><'.$this->base_node.' />', 'SimpleXMLElement', LIBXML_NOCDATA);
 
			$this->_buildEditXML_node($template_item, '/'.$this->base_node, $this->output_item);
		
			$output	= $this->output_item->asXML();

			$output2	= $this->edit_page_item->asXML();

//			$output = $this->fixCdata($output);
//			$output = $this->unencodeAmp($output);
//			$output = $this->replaceHtmlTags($output);
			$output =  html_entity_decode($output);

			// Strip out any <?xml stuff
			$output = preg_replace('/\<\?xml.+\?\>/', '', $output, 1);

			return $output;
	
	}

	private function _buildEditXML_node($xml, $path="", $parent_node, $num=null) {
	
			foreach ($xml->children() as $key => $element) {

				// Build Path (Need to add position for nested multi items)
				$new_path 		= $path;
				if (is_int($num)) $new_path 
								.= '[position() = '.($num + 1).']';
				$new_path		.= '/' . $key;
				$node_name		= $key;

				// Does this exist in the page item?
				$page_elements 	= $this->edit_page_item->xpath($new_path);

//				pr($new_path, 'Path ('.count($page_elements).')');
//				pr($page_elements, 'page_elements');

				if ($page_elements && count($page_elements) >= 1) { 

					// Use item from page
		
					$i = 0;
					foreach ($page_elements as $p_key => $p_val) {

						// For multi items which have children
						// Make sure we only display THEIR children under THEM
						//if (is_null($num) || ($num === $i) || ($element->attributes()->multi)) {
						if (true) {

							// Add node to XML 
							//$node_value	= strlen($p_val[0]) ? $p_val[0] : null;

							$node_value		= (strlen($p_val) && !is_array($p_val)) ? $p_val : null;
							
							if ($p_val->attributes()->lang) {
							
								// MULTI LANGUAGE ELEMENT

								$new_current 	= $parent_node->addChild($key, '');
								
								foreach ($p_val->children() as $lang_key => $lang_element) {
									$new_current->addChild($lang_key, $this->makeCdata($lang_element, 'edit'));
								}
								
							} else {
							
								// NORMAL ELEMENT
								$new_current 	= $parent_node->addChild($key, $this->makeCdata($node_value, 'edit'));
							
							}
							
							// Insert attributes from the template node
							foreach ($element->attributes() as $a_key => $a_val) {
								if (in_array($a_key, $this->PAGE_CONF['xml_template_attribs'])) $new_current->addAttribute($a_key, $a_val);
							}	

							// Make sure we have a title specified
							if (!$element->attributes()->title && !$element->attributes()->border) {
								$new_current->addAttribute('title', humanize($key));
							}

								// Insert attributes from page
							foreach ($p_val->attributes() as $a_key => $a_val) {
								// addAttribute('name', utf8_encode('G√ºnther'));
								if (!in_array($a_key, $this->PAGE_CONF['xml_template_attribs'])) {
									$new_current->addAttribute($a_key, $this->sanitizeAttrubute($a_val));
								} 
							}
	
							//if (count($page_elements) > 1) 
							$new_current->addAttribute('sortkey', $i);
		
							// Children
							$this->_buildEditXML_node($element, $new_path, $new_current, $i);
						
						}

						$i++;						
						
					}
		
				} else {
		
					// Use item from template
					
					$node_value		= strlen($element[0]) ? $element[0] : '';
					$new_current 	= $parent_node->addChild($key, $node_value);

					foreach ($element->attributes() as $a_key => $a_val) {
						$new_current->addAttribute($a_key, $a_val);
					}
					$new_current->addAttribute('sortkey', 0);
					
					// Make sure we have a title specified
					if (!$element->attributes()->title && !$element->attributes()->border) {
						$new_current->addAttribute('title', humanize($key));
					}
					
		
					$this->_buildEditXML_node($element, $new_path, $new_current);
		
				}
		
			}	
		
	} // End: _buildEditXML_node



	// ------------------------------------------------------------------------


	function buildDupeXML($template_xml, $path) {
			$this->_set_config();
			
			$template_item 	= simplexml_load_string($template_xml, 'SimpleXMLElement', LIBXML_NOCDATA);
			$dupe_item 		= $template_item->xpath('//'.$path);

			$this->buildDupeXML_node($dupe_item[0]);	
			
			return $dupe_item[0]->asXML();

	}

	function buildDupeXML_node($element) {

			// Make sure we have a title specified
			if (!$element->attributes()->title && !$element->attributes()->border) {
				$element->addAttribute('title', humanize($element->getName()));
			}
			
			foreach ($element->children() as $child) {
				$this->buildDupeXML_node($child);
			}
	
	}
	

	// ------------------------------------------------------------------------

	function buildContentXML($array) {
			$this->_set_config();

			$this->output_item = new DOMDocument('1.0', 'UTF-8');
			
			$this->output_item->preserveWhiteSpace 	= false;
			$this->output_item->formatOutput   		= true;

			// Create root node
			$root_node = $this->output_item->createElement($this->base_node);

			if (!empty($array)) $this->_buildContentXML_node($array[$this->base_node], $root_node);
			
			$this->output_item->appendChild($root_node);

			$output		= $this->output_item->saveXML();

			// Strip out any <?xml stuff
			$output = preg_replace('/\<\?xml.+\?\>/', '', $output, 1);

			return $output;

	}

	private function _buildContentXML_node($array, $parent_node) {

			if (!is_array($array)) return;

			foreach($array as $name => $value) {

				if (gettype($value) == 'array') {

					foreach($value as $n2 => $v2) {

						$current_node = $this->output_item->createElement($name);

						// Could use this to test if we wanted to add attributes to section nodes
						//if (gettype($n2) == 'integer') {
						
						if (empty($v2['type'])) {
							// This is a section node

							// Add child nodes
							$this->_buildContentXML_node($v2, $current_node);

						} else {						
							// This is an item node
							
							$node_definition = $this->PAGE_CONF['xml_nodes'][$v2['type']];

							// Trim leading/trailing whitespace based on config
							if ($this->PAGE_CONF['trim_input_values']) $v2['value'] = trim($v2['value']);
							
							// Node Value
							if (!empty($v2['value']) && is_array($v2['value'])) {	

								$current_node->setAttribute('lang', 'true');
							
								foreach ($v2['value'] as $lang => $lang_value) {

									$lang_node 	= $this->output_item->createElement('lang_'.$lang);
									$lang_value	= mb_convert_encoding($lang_value,'UTF-8','UTF-8');
									$lang_data 	= $lang_node->ownerDocument->createCDATASection($this->sanitizeValue($lang_value));
									$lang_node->appendChild($lang_data);
									$current_node->appendChild($lang_node);
								}
							
							} else if (!empty($v2['value']) && strlen($v2['value'])) {
								$v2['value'] 	= mb_convert_encoding($v2['value'],'UTF-8','UTF-8');
								$node_data 		= $current_node->ownerDocument->createCDATASection($this->sanitizeValue($v2['value']));
								$current_node->appendChild($node_data);
							}

							// Loop through attributes
							foreach($v2 as $a_key => $a_val) {	
								if ($a_key == 'type') continue;
								if (!strlen($a_val)) continue;
								
								if (!$node_definition['cdata'] || ($node_definition['cdata'] && (strtolower($a_key) != 'value'))) {
									$current_node->setAttribute($a_key, $this->sanitizeValue($a_val, true));
								}
							}
							
							// This can not contain children
							
						}

						// Add to parent node
						$parent_node->appendChild($current_node);

					}
					
				} else {
				
					// Normal text node
					// This should never really happen

					if ($this->PAGE_CONF['trim_input_values']) $value = trim($value);
					
					if (!empty($value) && strlen($value)) {
						$node_data = $current_node->ownerDocument->createCDATASection($this->sanitizeValue($value));
						$current_node->appendChild($node_data);
					}

					// Add to parent node
					$parent_node->appendChild($current_node);

				}				
				
				//pr($name, 'Appending node to parent');
				
				// Add to parent node
				//$parent_node->appendChild($current_node);

			}
			
			return true;

	} // End: _buildContentXML_node


	/*

	function _OLDbuildContentXML($array) {
			$this->_set_config();

	
			$this->output_item = simplexml_load_string('<'.$this->base_node.' />', 'SimpleXMLElement', LIBXML_NOCDATA);
			
			if (!empty($array)) $this->_buildContentXML_node($array[$this->base_node], $this->output_item);

			$xml	= $this->output_item->asXML();
			$output = $this->fixCdata($xml);

			// Strip out any <?xml stuff
			$output = preg_replace('/\<\?xml.+\?\>/', '', $output, 1);

			// Fix &amp;
			//$output = $this->encodeAmp($output);

			$output = $this->replaceHtmlTags($output);
			//return htmlspecialchars_decode($output);
			return $output;

	}
	private function _OLD_buildContentXML_node($array, $parent_node) {

			if (!is_array($array)) return;
			$result = '';

			foreach($array as $name => $value) {
			
				$node_name = $name;

				if (gettype($value) == 'array') {

					foreach($value as $n2 => $v2) {
						
						// Could use this to test if we wanted to add attributes to section nodes
						//if (gettype($n2) == 'integer') {
						
						if (empty($v2['type'])) {
							// This is a section node

							// Create node, no value
							$current_node 	= $parent_node->addChild($node_name);
				
							// Add child nodes
							$this->_buildContentXML_node($v2, $current_node);

						} else {						
							// This is an item node
							
							//echo '<p>' . $v2['type'] . '</p>';
							
							$node_definition = $this->PAGE_CONF['xml_nodes'][$v2['type']];

							// Trim leading/trailing whitespace based on config
							if ($this->PAGE_CONF['trim_input_values']) $v2['value'] = trim($v2['value']);

							// Node Value
							if (strlen($v2['value']) && $node_definition['cdata']) {
								$node_value = $this->makeCdata($v2['value'], 'content');
							} else {
								$node_value = null;
							}

							// Create node
							$current_node 	= $parent_node->addChild($node_name, utf8_encode($node_value));

							// Loop through attributes
							foreach($v2 as $a_key => $a_val) {	
								if ($a_key == 'type') continue;
								if (!strlen($a_val)) continue;
								
								if (!$node_definition['cdata'] || ($node_definition['cdata'] && (strtolower($a_key) != 'value'))) {
									$current_node->addAttribute($a_key, $a_val);
								}
							}
							
							// This can not contain children
							
						}
					}
					
				} else {
				
					// Normal text node
					// Should this really happen?
					// TODO: Check to see if we really need to add CDATA

					if ($this->PAGE_CONF['trim_input_values']) $value = trim($value);

					$current_node 	= $parent_node->addChild($node_name, $value);

				}

			}
			
			return true;

	} // End: _buildContentXML_node

	*/

	// ------------------------------------------------------------------------
    // https://gist.github.com/ChilSult44/6055567    
    
    function sanitizeValue($text, $attribute=false) {
    
        $allowed_tags = $this->PAGE_CONF['allowed_html_tags'];
        
        mb_regex_encoding('UTF-8');

        //replace MS special characters first
        $search     = array('/&lsquo;/u', '/&rsquo;/u', '/&ldquo;/u', '/&rdquo;/u', '/&mdash;/u');
        $replace    = array('\'', '\'', '"', '"', '-');
        $text       = preg_replace($search, $replace, $text);

        //make sure _all_ html entities are converted to the plain ascii equivalents - it appears
        //in some MS headers, some html entities are encoded and some aren't
        $text       = html_entity_decode($text, ENT_QUOTES, 'UTF-8');

        //try to strip out any C style comments first, since these, embedded in html comments, seem to
        //prevent strip_tags from removing html comments (MS Word introduced combination)
        if(mb_stripos($text, '/*') !== FALSE){
            $text   = mb_eregi_replace('#/\*.*?\*/#s', '', $text, 'm');
        }

        //introduce a space into any arithmetic expressions that could be caught by strip_tags so that they won't be
        //'<1' becomes '< 1'(note: somewhat application specific)
        $text       = preg_replace(array('/<([0-9]+)/'), array('< $1'), $text);
        $text       = strip_tags($text, $allowed_tags);

        //eliminate extraneous whitespace from start and end of line, or anywhere there are two or more spaces, convert it to one
        //LDW: This is stripping out line breaks, temporarily disabling
        //$text       = preg_replace(array('/^\s\s+/', '/\s\s+$/', '/\s\s+/u'), array('', '', ' '), $text);

        //strip out inline css and simplify style tags
        // LDW: This is not working properly, it's not closing tags properly.
        // $search     = array('#<(strong|b)[^>]*>(.*?)</(strong|b)>#isu', '#<(em|i)[^>]*>(.*?)</(em|i)>#isu', '#<u[^>]*>(.*?)</u>#isu');
        // $replace    = array('<b>$2</b>', '<i>$2</i>', '<u>$1</u>');
        // $text       = preg_replace($search, $replace, $text);

        //special character rules, have to be done one-by-one or else it removes foreign characters
       	$text 		= preg_replace('/\x03/', '', $text); // "ETX" character

        //on some of the ?newer MS Word exports, where you get conditionals of the form 'if gte mso 9', etc., it appears
        //that whatever is in one of the html comments prevents strip_tags from eradicating the html comment that contains
        //some MS Style Definitions - this last bit gets rid of any leftover comments */
        $num_matches = preg_match_all("/\<!--/u", $text, $matches);
        if($num_matches){
            $text = preg_replace('/\<!--(.)*--\>/isu', '', $text);
        }

        return $text;
    }

    function sanitizeAttrubute($text) {

        $search     = array('/&amp;/u', '/&/u');
        $replace    = array('', '');
        $text       = preg_replace($search, $replace, $text);

        return $text;
    }

	function makeCdata($value, $mode) {

			//$value = str_replace('&', '&amp;', $value);	
			$value = preg_replace('/&(?!\w+;)/', '&amp;', $value);

			// Only convert to safe characters on the way in from edit form			
			if ($mode == 'content') $value = makeSafeEntities($value);
			
			if (strlen($value)) $value = '<![CDATA['.$value.']]>';
			return $value;
	}

	function fixCdata($value) {
	
			$replace					= array();
			$replace['&lt;![CDATA[']	= '<![CDATA[';
			$replace[']]&gt;']			= ']]>';

			return strtr($value, $replace);
	}


	function encodeAmp($value) {

			$replace					= array();
			$replace['&']				= '&amp;';

			return strtr($value, $replace);
	}


	function unencodeAmp($value) {

			$replace					= array();
			$replace['&amp;']			= '&';

			return strtr($value, $replace);
	}

	function replaceHtmlTags($value) {
			
			$replace					= array();
			$replace['&lt;']			= '<';
			$replace['&gt;']			= '>';

			return strtr($value, $replace);
	}
	

	// ------------------------------------------------------------------------


	protected function _set_config() {

			if (!empty($this->PAGE_CONF)) return;

			$this->PAGE_CONF 		= CI()->loadConfig('page');
			$this->base_node	= CI()->ADMIN_CONF['xml_root_node'];

	}

}

?>