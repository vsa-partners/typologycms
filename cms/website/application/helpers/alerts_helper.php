<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Typology CMS
 *
 * @author      VSA Partners / Louis D Walch (lwalch@vsapartners.com)
 * @link        http://www.vsapartners.com
 *
 */
 
 

function displayAlerts() { 

		echo '<script language="javascript">'
			. NL . 'AlertDialog.init({button: "'.CI()->asset_path.'img/button_ok.gif"});'
			. NL . '</script>';

		if (count(CI()->errors)) echo _displayAlerts(CI()->errors, 'error');
		if (count(CI()->messages)) echo _displayAlerts(CI()->messages, 'message');
		if (CI()->session->flashdata('error')) echo _displayAlerts(CI()->session->flashdata('error'), 'error');
		if (CI()->session->flashdata('message')) echo _displayAlerts(CI()->session->flashdata('message'), 'message');


}

function _displayAlerts($val, $style='general') {

		// Should this check to see if there are alerts to display before we output the script?

		$return = '<script language="javascript">';
		if (is_array($val)) {
			foreach ($val as $alert) {
				$return .= 'AlertDialog.add(\''.$alert.'\', \''.$style.'\');';
			}
		} else {
			$return .= 'AlertDialog.add(\''.$val.'\', \''.$style.'\');';
		}
	
		$return .= '</script>';
		
		return $return;
}
