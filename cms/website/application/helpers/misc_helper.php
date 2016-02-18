<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Typology CMS
 *
 * @author      VSA Partners / Louis D Walch (lwalch@vsapartners.com)
 * @link        http://www.vsapartners.com
 *
 */
 


function translatedValue($val, $lang, $br=true) {

		$name 		= 'lang_'.$lang;
		$fallback	= 'lang_EN';

		$return		= '';

		if (!empty($val->{$name})) {
			$return = (string) $val->{$name};
		} else if (!empty($val['value'][$name])) {
			$return = $val['value'][$name];		
		} else if (!empty($val[$name])) {
			$return = $val[$name];		
		} else if (!empty($val->{$fallback})) {
			$return = (string) $val->{$fallback};
		} else if (!empty($val['value'][$fallback])) {
			$return = $val['value'][$fallback];		
		} else if (!empty($val[$fallback])) {
			$return = $val[$fallback];		
		} else {
			return '';
		}
		
		if ($br === true) {
			return nl2br($return);
		} else {
			return $return;
		}

}
 
function loadLanguage() {

		$lang 		= 'EN';
		$allowed	= array('EN', 'CY');

		if (CI()->input->get_post('lang')) {

			$in		= strtoupper(CI()->input->get_post('lang'));
			
			if (in_array($in, $allowed)) {
				$lang = $in;
				CI()->session->set_userdata('lang', $lang);
			}

		} else if (CI()->session->userdata('lang')) {
			$lang	= CI()->session->userdata('lang');
		}
		
		return $lang;

}
 
 
function CI() {
	try {
		$ci = &get_instance();
		return $ci;
	} catch (Exception $e) {
		exit("Can't get CI instance");
	}
	/*
		if (!function_exists('get_instance')) exit("Can't get CI instance");
		$ci = &get_instance();
		return $ci;
	*/
}


function pr($val,$title=false,$echo=true, $htmlent=false) {

		$title = ((!empty($title)) ? '<strong>'.$title.' =</strong> ' : '');


		if (is_array($val)) {
			$tag 	= 'pre';
			$value	= print_r($val, true);
		} else {
			$tag = 'pre';
			$value	= $val;
		}
		
		$output = '<'.$tag.'>' . $title
				. (($htmlent) ? htmlentities($value) : $value)
				. '</'.$tag.'>';

		
		if ($echo) {
			echo $output;
		} else {
			return $output;
		}
	
}


function zonepath($file=null, $zone=null) {

        $zone   = (is_null($zone)) ? CI()->zone : $zone;
		$path   = SITEPATH . 'cms/' . $zone . '/';
		
		if (!is_null($file)) $path .= '/'.$file;
		
		return reduce_multiples($path, '/');

}


/* clear_array_values
   Used by CRUD to create blank item from table columns
*/
function clear_array_values($v) {
		return '';	
}



function xml2Array($xml, $root = true) {
	
	if (is_string($xml)) {
		$xml 	= simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
	}

	if (!$xml->children()) {
		return (string)$xml;
	}
 
	$array = array();
	foreach ($xml->children() as $element => $node) {
		$totalElement = count($xml->{$element});
 
		if (!isset($array[$element])) {
			$array[$element] = "";
		}
 
		// Has attributes
		if ($attributes = $node->attributes()) {
			$data = array(
				'value' => (count($node) > 0) ? xml2Array($node, false) : (string)$node
				// 'value' => (string)$node (old code)
			);
 
			foreach ($attributes as $attr => $value) {
				$data['@'.$attr] = (string)$value;
			}
 
			if ($totalElement > 1) {
				$array[$element][] = $data;
			} else {
				$array[$element] = $data;
			}
 
		// Just a value
		} else {
			if ($totalElement > 1) {
				$array[$element][] = xml2Array($node, false);
			} else {
				$array[$element] = xml2Array($node, false);
			}
		}
	}
 
	if ($root) {
		return array($xml->getName() => $array);
	} else {
		return $array;
	}
}



function array2XML($node, $level=1) {

		if (!is_array($node)) return $node;

		$xml = '';
		
		if ($level == 1) $node = array('data'=>$node);
	
		foreach ($node as $key => $value)  {
			$xml .= array2XML_node($key, $value);
		}
	
		return $xml;
}

function array2XML_node($key, $value, $level=0) {

		//if (is_int($key)) $key = 'node_'.$key;
		if (is_int($key)) $key = 'node';

		$tag_name 	= $key;
		$attribs	= '';
		$node_value	= '';
		$level ++;
		
		// Get Node Value
		if (is_array($value)) {
		
			foreach ($value as $sub_key => $sub_value) {
			
				if (substr($sub_key, 0, 1) == '@') {
					// Node Attribute
					$attribs .= ' '.substr($sub_key, 1).'="'.$sub_value.'"';
				} else {
					$node_value .= array2XML_node($sub_key, $sub_value, $level);
				}
			
			}	
			
			$node_value .= "\n" . str_repeat("\t", $level);
		
		} else {

			$make_cdata = true;

			if (stripos($key, 'XML')
				|| is_numeric($value)
				|| preg_match("/^(\\t|\\r|\\n)*\\<data.*/", $value) 
				|| substr($value, 0, 6) == '<data>'){ $make_cdata = false; }

			if ($make_cdata)	$value = '<![CDATA['.$value.']]>';
			
			$node_value = $value;
		
		}

		return "\n" . str_repeat("\t",$level).'<'.$tag_name.$attribs.'>'.$node_value.'</'.$tag_name.'>';


}

function array2XML_old($array, $level=1) {

		if (!is_array($array)) return $array;

		$xml = '';
		
		if ($level == 1) $xml .= '<data>';
	
		foreach ($array as $key => $value)  {
			
			//if (is_int($key)) $key = 'node_'.$key;
			if (is_int($key)) $key = 'node';
			
			if (is_array($value)) {
	
				$xml .= str_repeat("\t",$level)."<$key>\n";
				$xml .= array2XML($value, $level+1);
				$xml .= str_repeat("\t",$level)."</$key>\n";
	
			} else {
				
				$make_cdata = true;
	
				if (stripos($key, 'XML')) 				$make_cdata = false;
				if (is_numeric($value))					$make_cdata = false;
				if (substr($value, 0, 6) == '<data>') 	$make_cdata = false;
				if (preg_match("/^(\\t|\\r|\\n)*\\<data.*/", $value))	
					$make_cdata = false;
		
				if ($make_cdata)	$value = '<![CDATA['.$value.']]>';
			
				$xml .= str_repeat("\t",$level) . "<$key>$value</$key>\n";
	
			}
		
		}
	
		if ($level == 1) $xml .= '</data>';
	
		return $xml;
}




function br2nl($result){
		$result = preg_replace("/(\r\n|\n|\r)/", "", $result);
		$result = preg_replace("=<br */?>=i", "\n", $result);
		return $result;
}



function get_date_menu($dates=null, $params=array()){

		if (empty($dates)) return;

		$start 	= strtotime($dates['min']);
		$end 	= strtotime($dates['max']);

		$name	= !empty($params['name']) ? $params['name'] : 'date';
		$val	= CI()->input->post($name) ? CI()->input->post($name) : CI()->input->get($name);

		$result = '<select name="'.$name.'" class="date">'
				. '<option value="">Select Date</option>'
				. _get_date_menu_row($start, $end, $val)
				. '</select>';

		return $result;

}

function _get_date_menu_row($start, $end, $val) {

		if (empty($start) || empty($end)) return;
		
		$display 	= date('F Y', $start);
		$value 		= date('Y-m', $start);
		$selected	= ($value == $val) ? 'SELECTED' : '';
		$result 	= '<option value="'.$value.'" '.$selected.'>' . $display . '</option>';
		
		$next 		= strtotime('+1 Month', $start);
		
		if ($next < $end)
			$result	.=_get_date_menu_row($next, $end, $val);
			
		return $result;
		
}





function csv_from_array($rows, $delim = ",", $newline = "\n", $enclosure = '"') {
		if (!count($rows))  return;
	
		$out = '';
		
		//pr($rows, 'rows');
		
		// First generate the headings from the table column names
		foreach ($rows as $key => $row) {
			foreach ($row as $key => $item) {
				$out .= $enclosure.str_replace($enclosure, $enclosure.$enclosure, $key).$enclosure.$delim;
			}
			break;
		}
		
		$out = rtrim($out);
		$out .= $newline;
		
		// Next loop array and build out the rows
		foreach ($rows as $key => $row) {
			foreach ($row as $item) {
				$out .= $enclosure.str_replace($enclosure, $enclosure.$enclosure, $item).$enclosure.$delim;
			}
			$out = rtrim($out);
			$out .= $newline;
		}
	
		return $out;
}