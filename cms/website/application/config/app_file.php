<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

// ------------------------------------------------------------------------
// ITEM TYPES

$config['items']					= array(
										'root' 					=> array(
											'name'				=> 'Root Item'
											, 'allowed_children'=> array('file', 'collection')
											, 'icons'			=> array('sm' => 'img/mini_icons/home.gif')
											)
										, 'collection' 		=> array(
											'name'				=> 'File Collection'
											, 'allowed_children'=> array('file')
											, 'icons'			=> array('sm' => 'img/mini_icons/folder.gif')
											)
										, 'file' 				=> array(
											'name'				=> 'File'
											, 'allowed_children'=> array()
											, 'icons'			=> array('sm' => 'img/mini_icons/document.gif')
											)
										);
		

$config['force_name_in_uri'] 		= TRUE;

$config['file_directory']			= 'files';
$config['file_website_location']	= SITEPATH . CI()->SITE_CONF['file_uri_trigger'] . '/';

$config['file_dir_depth'] 			= 3;								// 
$config['temp_folder'] 				= 'tmp';

$config['file_cache_time'] 			= 5; // Days

$config['match_mime_to_ext'] 		= TRUE;

$config['jpg_quality']  			= '75';

// ------------------------------------------------------------------------
// UPLOAD RESTRICTIONS

$config['allowed_types'] 			= 'gif|jpg|png|pdf';
$config['max_size']					= '10000';
$config['max_width']  				= '1024';
$config['max_height']  				= '768';



// ------------------------------------------------------------------------
// ACCESS MENU

$config['access_menu']	= array(
								'style'					=> 'list'
								, 'model_param'			=> array('parent_id' => '1')
								);