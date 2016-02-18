<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Typology CMS
 *
 * @author      VSA Partners / Louis D Walch (lwalch@vsapartners.com)
 * @link        http://www.vsapartners.com
 *
 */


function timeFromArray($array){

		if (!is_array($array)) return;

		$result	= array();			
		$result[0] = ($array['ampm']) ? $array['hour']+12 : $array['hour'];
		$result[1] = $array['min'];
		$result[2] = '00';
					
		return implode(':', $result);
					
}	


function timeToArray($time){
	
	if (empty($time)) return;
	
	$result = array();
	$time 	=  explode(':', $time);

	$result['hour'] 	= $time[0];
	$result['min'] 		= $time[1];
	$result['ampm'] 	= 0;

	if ($result['hour'] > 12) {
		$result['ampm'] = 1;
		$result['hour'] = ($result['hour'] - 12);
	}
	
	return $result;

}

function timeArrayToDisplay($array){

	if (!is_array($array)) return;

	$result	= '';
	$result .= ltrim($array['hour'], '0');
	$result .= ':' . $array['min'];
	$result .= ' ' . (($array['ampm']) ? 'pm' : 'am');
	
	return $result;
}
