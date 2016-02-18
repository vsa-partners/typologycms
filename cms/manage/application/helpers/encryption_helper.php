<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Typology CMS
 *
 * @author      VSA Partners / Louis D Walch (lwalch@vsapartners.com)
 * @link        http://www.vsapartners.com
 *
 */
 
 

function encryptString($string='',$user='',$email='') { 
		$CI = &get_instance();
		/*
		echo "<br/>string = ".$string;
		echo "<br/>User = ".$user;
		echo "<br/>email = ".$email;
		echo '<br/>Session = '. CI()->session->userdata('username');
		*/
		
		
		if ($user =='' && CI()->session->userdata('username') !=''){
			$user = CI()->session->userdata('username');
		}
		
		if ($email == ''){
			$query = $CI->db->query('SELECT email FROM user where user = "'.$user.'";')->result_array();
			$salt = $query[0]['email'];
		}
		
		if ($salt == ''){
			$salt = $email;
		}
		
		//v1
		$string = $salt . $string . $salt;
		//v2
		//$string = $salt . $string . $salt. CI()->SITE_CONF['encryption_salt'];

		//Original
		//$string = CI()->SITE_CONF['encryption_salt'] . $string . CI()->SITE_CONF['encryption_salt'];

		//For SHA-2 we are usinf SHA-256 from the family
		//$encrypted_string = hash('sha256', $string);

		// Create encryption handle. Even though this is RIJNDAEL_128 will be 256-bit because of key length
		
		$cipher = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');

		if (mcrypt_generic_init($cipher, CI()->SITE_CONF['encryption_key'], CI()->SITE_CONF['encryption_iv']) != -1) {
		
			// PHP pads with NULL bytes if string is not a multiple of the block size
			$encrypted_raw 		= mcrypt_generic($cipher, $string);
			$encrypted_string 	= bin2hex($encrypted_raw);

			// Terminate decryption handle and close module
			mcrypt_generic_deinit($cipher);
			mcrypt_module_close($cipher);
			
		} else {
			show_error('Fatal error. Unable to set up encryption mechanism. Please contact the system administrator.');
		}
		//echo $encrypted_string;exit;
		return hash('sha256', $encrypted_string);	

}
