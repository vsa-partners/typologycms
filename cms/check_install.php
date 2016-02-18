<?php

    //phpinfo();

	error_reporting(E_ALL);
	ini_set('display_errors', 'On'); 

	$cms_dir 		= $_SERVER['DOCUMENT_ROOT'] . ((dirname($_SERVER['SCRIPT_NAME']) == '/') ? '/' : dirname($_SERVER['SCRIPT_NAME'])).'/';

	echo '<strong>CMS Directory:</strong>' . $cms_dir;

	// -----------------------------------------------------
    
    $min_version    = '5.2.6';
    
	echo '<hr/>';
	echo '<h3>Checking PHP version:</h3>';

    if (version_compare(PHP_VERSION, $min_version, '<')) {    
		echo '<p><strong style="color: red;">FAIL. You must have PHP version '.$min_version.' installed.</strong></p>';
    } else {
		echo '<p><strong style="color: green;">PASSED, You have '.PHP_VERSION.'.</strong></p>';
    }

	// -----------------------------------------------------
    
    echo '<hr/>';
    echo '<h3>Checking server settings:</h3>';

    if (is_callable('apache_get_modules')) {
    
        $ini_options 	= array(
            'short_open_tag'		=> true
            );
    
        foreach ($ini_options as $varname => $value) {
    
            $current = ini_get($varname);
            
            if ($current == $value) {
                $message = '<strong style="color: green;">PASSED</strong>';
            } else {
                $message = '<strong style="color: red;">FAIL. This must be set to "'.(int)$value.'", current value is "'.$current.'" in php.ini.</strong>';
            }
    
            echo '<p>'.$varname . ' : ' . $message .'</p>';
        
        }
    
        if (in_array('mod_rewrite', apache_get_modules())) {
            echo '<p>mod_rewrite : <strong style="color: green;">PASSED</strong></p>';	} else {
            echo '<p>mod_rewrite : <strong style="color: red;">FAIL. This is a required Apache module.</strong></p>';
        }
    
    } else {

        echo '<p><strong style="color: orange;">UNKNOWN.</strong> Unable to call "apache_get_modules", PHP must be running as CGI - assuming you already checked server requirements.</p>';
    
    }

    
	// -----------------------------------------------------


	$folder_checks 	= array(
		'website/application'		=> FALSE
		, 'manage/application'		=> FALSE
		, 'system'				    => FALSE
		, 'system/logs'			    => TRUE
		, 'templates'			    => FALSE
		, 'local/files'				=> TRUE
		, 'local/files/000'			=> TRUE
		, 'local/files/tmp'			=> TRUE
		, 'website/cache'			=> TRUE
		, 'manage/cache'			=> TRUE
		);
		
	echo '<hr/>';
	echo '<h3>Checking directories:</h3>';

	foreach ($folder_checks as $directory => $check_write) {

		if (!is_dir($cms_dir.$directory)) {
			$message = '<strong style="color: red;">FAIL. Directory does not exist.</strong>';
		} else if ($check_write && !is_writable($cms_dir.$directory)) {
			$message = '<strong style="color: red;">FAIL. Do not have write permissions to directory.</strong>';
		} else {
			$message = '<strong style="color: green;">PASSED</strong>';
		}

		echo '<p>'.$directory . '/ : ' . $message .'</p>';
	
	}

	// -----------------------------------------------------

	$extension_checks 	= array(
	    'curl','dom','gd','json','libxml','mcrypt','mysql','mbstring','SimpleXML','xsl'
	    );

	echo '<hr/>';
	echo '<h3>Checking for required extensions:</h3>';

	foreach ($extension_checks as $ext) {

		if (!extension_loaded($ext)) {
			$message = '<strong style="color: red;">FAIL. Extension is not installed.</strong>';
		} else {
			$message = '<strong style="color: green;">PASSED</strong>';
		}

		echo '<p>'.$ext . '/ : ' . $message .'</p>';
	
	}

	// -----------------------------------------------------


	echo '<hr/>';
	echo '<h3>Checking database connection:</h3>';
	
	if (!file_exists($cms_dir.'local/database.php')) {

		echo '<strong style="color: red;">FAIL. Can not locate local/database.php</strong>';

	} else {
	
		define('BASEPATH', TRUE);
		include($cms_dir.'local/database.php');
	
		if (empty($db['default']['hostname']) || empty($db['default']['username']) || empty($db['default']['password']) || empty($db['default']['database'])) {

			echo '<strong style="color: red;">FAIL. Database cridenials missing from config.</strong>';

		} else {

			$DB = @mysql_connect($db['default']['hostname'], $db['default']['username'], $db['default']['password']);

			if (!$DB) {

				echo '<strong style="color: red;">FAIL. Error connecting to the database with the cridenials you provided.</strong>';

			} else {
			
				$db_selected    = @mysql_select_db($db['default']['database'], $DB);        
				
				if (!$db_selected) {

                    echo '<strong style="color: red;">FAIL: Could not select database.</strong>';				
				
			    } else {
			
                    $result     = mysql_query('SHOW TABLES');
    
                    if (!$result) {
                        echo '<strong style="color: red;">FAIL. ' . mysql_error() . '</strong>';
                    } else {
                        echo '<strong style="color: green;">PASSED: Successfully connected to database and executed "SHOW TABLES".</strong>';				
                    }
                
                }
			
			}
			
			mysql_close($DB);
			unset($config);

		}
	}

	// -----------------------------------------------------
