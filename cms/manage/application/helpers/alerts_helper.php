<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Typology CMS
 *
 * @author      VSA Partners / Louis D Walch (lwalch@vsapartners.com)
 * @link        http://www.vsapartners.com
 *
 */
 
 

function displayAlerts() { 

		$alerts = '';

		if (count(CI()->errors)) 					$alerts .= _displayAlerts(CI()->errors, 'error');
		if (count(CI()->messages)) 					$alerts .= _displayAlerts(CI()->messages, 'message');
		if (CI()->session->flashdata('error')) 		$alerts .= _displayAlerts(CI()->session->flashdata('error'), 'error');
		if (CI()->session->flashdata('message')) 	$alerts .= _displayAlerts(CI()->session->flashdata('message'), 'message');
		
		if (strlen($alerts)) {
			echo '<script language="javascript">document.observe(\'dom:loaded\', function() {' .$alerts . ' TNDR.Modal.show(); }); </script>';
		
		}


}

function _displayAlerts($val, $style='general') {

		// Should this check to see if there are alerts to display before we output the script?
		
		$return = '';

		if (is_array($val)) {
			foreach ($val as $alert) {
				$return .= 'TNDR.Modal.add(\''.$alert.'\', \''.$style.'\', true, true);';
			}
		} else {
			$return .= 'TNDR.Modal.add(\''.$val.'\', \''.$style.'\', true, true);';
		}
			
		return $return;
}
