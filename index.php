<?php

//	------------------------------------------------------------------------
// 	PHP ERROR REPORTING LEVEL

	error_reporting(0);
	ini_set('display_errors', 'Off'); 

	// error_reporting(E_ALL);
	// ini_set('display_errors', 'On'); 

//	------------------------------------------------------------------------
//	APPLICATION FOLDER NAME - No trailing slash

	$application_folder 	= "cms/website/application";

//	------------------------------------------------------------------------
//	SYSTEM FOLDER NAME - No trailing slash

	$system_folder 		 	= "cms/system";

//	------------------------------------------------------------------------
//	WEBSITE DIRECTORY

	define('SITEPATH', (dirname($_SERVER['SCRIPT_NAME']) == '/') ? '/' : dirname($_SERVER['SCRIPT_NAME']).'/');







// =========================================================================
// END OF USER CONFIGURABLE SETTINGS
// =========================================================================



// ------------------------------------------------------------------------
// SET THE SERVER PATH

if (strpos($system_folder, '/') === FALSE) {
	if (function_exists('realpath') AND @realpath(dirname(__FILE__)) !== FALSE) {
		$system_folder = realpath(dirname(__FILE__)).'/'.$system_folder;
	}
} else {
	// Swap directory separators to Unix style for consistency
	$system_folder = str_replace("\\", "/", $system_folder); 
}


// ------------------------------------------------------------------------
// SET THE SERVER PATH
/*
EXT		- The file extension.  Typically ".php"
FCPATH	- The full server path to THIS file
SELF		- The name of THIS file (typically "index.php)
BASEPATH	- The full server path to the "system" folder
APPPATH	- The full server path to the "application" folder
*/

define('EXT', '.'.pathinfo(__FILE__, PATHINFO_EXTENSION));
define('FCPATH', __FILE__);
define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));
define('BASEPATH', $system_folder.'/');

if (is_dir($application_folder)) {
	define('APPPATH', $application_folder.'/');
} else {
	if ($application_folder == '') {
		$application_folder = 'application';
	}
	define('APPPATH', BASEPATH.$application_folder.'/');
}


// ------------------------------------------------------------------------
// LOAD THE FRONT CONTROLLER - Start Application

require_once BASEPATH.'codeigniter/CodeIgniter'.EXT;
