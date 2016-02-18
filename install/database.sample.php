<?php

/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| For complete instructions please consult the "Database Connection"
| page of the User Guide.
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
|	['hostname'] The hostname of your database server.
|	['username'] The username used to connect to the database
|	['password'] The password used to connect to the database
|	['database'] The name of the database you want to connect to
|	['dbdriver'] The database type. ie: mysql.  Currently supported:
				 mysql, mysqli, postgre, odbc, mssql
|	['dbprefix'] You can add an optional prefix, which will be added
|				 to the table name when using the  Active Record class
|	['pconnect'] TRUE/FALSE - Whether to use a persistent connection
|	['db_debug'] TRUE/FALSE - Whether database errors should be displayed.
|	['cache_on'] TRUE/FALSE - Enables/disables query caching
|	['cachedir'] The path to the folder where cache files should be stored
|	['char_set'] The character set used in communicating with the database
|	['dbcollat'] The character collation used in communicating with the database
|
| The $active_group variable lets you choose which connection group to
| make active.  By default there is only one group (the "default" group).
|
| The $active_record variables lets you determine whether or not to load
| the active record class
*/

$active_group 	= "default";
$active_record 	= TRUE;

$db_options		= array(
	'dbdriver' => "mysql"
	, 'dbprefix' => ""
	, 'pconnect' => TRUE
	, 'db_debug' => TRUE
	, 'cache_on' => FALSE
	, 'cachedir' => ""
	, 'char_set' => "utf8"
	, 'dbcollat' => "utf8_general_ci"
	);
	

$db['default']					= $db_options;
$db['default']['hostname'] 		= "dhostname";
$db['default']['username'] 		= "dusername"; 
$db['default']['password'] 		= "dpassword";
$db['default']['database'] 		= "ddatabase";

/*

switch ($_SERVER['SERVER_NAME']) {
	
	case 'staging.publicartfund.org':
	case '50.57.52.150':

		// STAGING

		$db['default']					= $db_options;
		$db['default']['hostname'] 		= "localhost";
		$db['default']['username'] 		= ""; 
		$db['default']['password'] 		= "";
		$db['default']['database'] 		= "cms_manage";

		break;

	case 'publicartfund.org':
	case 'www.publicartfund.org':
	case '50.57.52.151':

		// PRODUCTION

		$db['default']					= $db_options;
		$db['default']['hostname'] 		= "localhost";
		$db['default']['username'] 		= ""; 
		$db['default']['password'] 		= "";
		$db['default']['database'] 		= "cms_website";
	
		break;

	default:
		// Throw error if no match, this is to avoid accidently showing the wrong server confirguration.
		exit('Unable to detect site environment configuration.');
		break;

}

*/

/* This user must be different then the one above or mysql will experience issues with multiple db connections */

$db['publish_db']				= $db_options;
$db['publish_db']['hostname'] 	= "phostname";
$db['publish_db']['username'] 	= "pusername";
$db['publish_db']['password'] 	= "ppassword";
$db['publish_db']['database']   = "pdatabase";


