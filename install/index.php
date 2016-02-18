<?php
session_start();
error_reporting(0);
function myheader($url){
	echo '<script>window.location="'.$url.'";</script>';
	exit;
	}
if ($_GET['remove_me']==1){
	rrmdir('../install');  //<------ Remove it in Production.
	myheader("/manage");
  	exit; 
}

function rrmdir($dir) { 
  foreach(glob($dir . '/*') as $file) { 
    if(is_dir($file)) rrmdir($file); else unlink($file); 
  } rmdir($dir); 
}

if (isset($_POST['btnSubmit'])){
	//Set encryption keys
	$encryption_key = hash('sha256', date('U').session_id());
	$encryption_iv = hash('sha256', session_id().date('U'));
	
	copy('app_website.sample.php','../cms/manage/application/config/app_website.php');
	$path_to_file = '../cms/manage/application/config/app_website.php';
	$fh = fopen($path_to_file, 'r');
	$file_contents = fread($fh, filesize($path_to_file));
	fclose($fh);
	
	$file_contents = str_replace("c_encryption_key",$encryption_key,$file_contents);
	$file_contents = str_replace("c_encryption_iv",$encryption_iv,$file_contents);
	
	$fh = fopen($path_to_file, 'w');
	fwrite($fh, $file_contents);
	fclose($fh);
	
	//Writing the configuration file
	copy('database.sample.php','../cms/local/database.php');
	$path_to_file = '../cms/local/database.php';
	$fh = fopen($path_to_file, 'r');
	$file_contents = fread($fh, filesize($path_to_file));
	fclose($fh);
	
	$file_contents = str_replace("dhostname",$_POST['dhostname'],$file_contents);
	$file_contents = str_replace("dusername",$_POST['dusername'],$file_contents);
	$file_contents = str_replace("dpassword",$_POST['dpassword'],$file_contents);
	$file_contents = str_replace("ddatabase",$_POST['ddatabase'],$file_contents);
	
	$file_contents = str_replace("phostname",$_POST['phostname'],$file_contents);
	$file_contents = str_replace("pusername",$_POST['pusername'],$file_contents);
	$file_contents = str_replace("ppassword",$_POST['ppassword'],$file_contents);
	$file_contents = str_replace("pdatabase",$_POST['pdatabase'],$file_contents);
	
	$fh = fopen($path_to_file, 'w');
	fwrite($fh, $file_contents);
	fclose($fh);
	
	$salt = $_POST['email'];
	$string = $_POST['password'];
	$string = $salt . $string . $salt;
	
	$cipher = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');

		if (mcrypt_generic_init($cipher, $encryption_key, $encryption_iv) != -1) {
		
			// PHP pads with NULL bytes if string is not a multiple of the block size
			$encrypted_raw 		= mcrypt_generic($cipher, $string);
			$encrypted_string 	= bin2hex($encrypted_raw);

			// Terminate decryption handle and close module
			mcrypt_generic_deinit($cipher);
			mcrypt_module_close($cipher);
		}
	$finishedpassword = hash('sha256', $encrypted_string);
	
	//Executing the database configuration file
	require_once($cms_dir.'../cms/local/database.php');
	
	if ($_POST['dinstalldatabase']){
		
		if (!(empty($db['default']['hostname']) || empty($db['default']['username']) || empty($db['default']['password']) || empty($db['default']['database']))) {
	
		$mysqli = new mysqli($db['default']['hostname'], $db['default']['username'], $db['default']['password'], $db['default']['database']) or myheader("index.php?error_install=1&msg=Error connecting to MySQL server: ". mysql_error());
		//------------------Backup Drop------------------------------
		if ($_POST['dinstalldatabaseold']){
				$mysqli->query('SET foreign_key_checks = 0');
				if ($result = $mysqli->query("SHOW TABLES"))
				{
					while($row = $result->fetch_array(MYSQLI_NUM))
					{
						$mysqli->query('RENAME TABLE '.$row[0].' TO bkup_'.$row[0]);
					}
				}
				$mysqli->query('SET foreign_key_checks = 1');
		}else{
				$mysqli->query('SET foreign_key_checks = 0');
				if ($result = $mysqli->query("SHOW TABLES"))
				{
					while($row = $result->fetch_array(MYSQLI_NUM))
					{
						$mysqli->query('DROP TABLE IF EXISTS '.$row[0]);
					}
				}
				$mysqli->query('SET foreign_key_checks = 1');
		}
		$mysqli->close();
	//-------------------------------------------------	
	mysql_connect($db['default']['hostname'], $db['default']['username'], $db['default']['password']) or myheader("index.php?error_install=1&msg=Error connecting to MySQL server: ". mysql_error());
		// Select database
		mysql_select_db($db['default']['database']) or myheader("index.php?error_install=1&msg=Error selecting MySQL database: ". mysql_error());
		
		// Temporary variable, used to store current query
		$templine = '';
		
		// Read in entire file
		$lines = file("db_blank_manage.sql");
		// Loop through each line
		foreach ($lines as $line)
		{
		// Skip it if it's a comment
		if (substr($line, 0, 2) == '--' || $line == '')
			continue;
		
		// Add this line to the current segment
		$templine .= $line;
		// If it has a semicolon at the end, it's the end of the query
		if (substr(trim($line), -1, 1) == ';')
		{
			// Perform the query
			mysql_query($templine) or myheader("index.php?error_install=1&msg=Error performing query:". $templine . ":" . mysql_error());
			// Reset temp variable to empty
			$templine = '';
		}
		}
		//echo "Import Manage DB Import Done<br/>";
		
		if ($_POST['ldomain'] !=''){
			mysql_query("UPDATE Config SET value='".$_POST['ldomain']."' WHERE name='live_domain'");
			//echo 'live_domain Updated for Manage DB<br/>';
		}
		if ($_POST['sdomain'] !=''){
			mysql_query("UPDATE Config SET value='".$_POST['sdomain']."' WHERE name='staging_domain'");
			//echo 'staging_domain Updated for Manage DB<br/>';
		}
		
		mysql_query("INSERT INTO `user` (`user`, `email`, `type`, `password`, `password_options_json`, `permission_group`, `permissions`, `enabled`, `options`, `create_date`, `update_date`, `login_date`) VALUES
('".$_POST['user']."', '".$_POST['email']."', 'administrator', '".$finishedpassword."', NULL, 'administrator', '', 1, '{\"receive_pending_emails\":\"0\",\"display_profiler\":\"0\"}', '".date('Y-m-d H:i:s')."', '".date('Y-m-d H:i:s')."', '0000-00-00 00:00:00');");
			//echo 'User Inserted<br/>';
		
		mysql_close();
		}
	}
	
	if ($_POST['pinstalldatabase']){
		
		if (!(empty($db['publish_db']['hostname']) || empty($db['publish_db']['username']) || empty($db['publish_db']['password']) || empty($db['publish_db']['database']))) {
	
	mysql_connect($db['publish_db']['hostname'], $db['publish_db']['username'], $db['publish_db']['password']) or myheader("index.php?error_install=1&msg=Error connecting to MySQL server: ". mysql_error());
		// Select database
		mysql_select_db($db['publish_db']['database']) or myheader("index.php?error_install=1&msg=Error selecting MySQL database: ". mysql_error());
		
		// Temporary variable, used to store current query
		$templine = '';
		
		// Read in entire file
		$lines = file("db_blank_website.sql");
		// Loop through each line
		foreach ($lines as $line)
		{
		// Skip it if it's a comment
		if (substr($line, 0, 2) == '--' || $line == '')
			continue;
		
		// Add this line to the current segment
		$templine .= $line;
		// If it has a semicolon at the end, it's the end of the query
		if (substr(trim($line), -1, 1) == ';')
		{
			// Perform the query
			mysql_query($templine) or myheader("index.php?error_install=1&msg=Error performing query:". $templine . ":" . mysql_error());
			// Reset temp variable to empty
			$templine = '';
		}
		}
			
		//echo "Import Website DB Import Done<br/>";
		
		if ($_POST['ldomain'] !=''){
			mysql_query("UPDATE Config SET value='".$_POST['ldomain']."' WHERE name='live_domain'");
			//echo 'live_domain Updated for Website DB<br/>';
		}
		if ($_POST['sdomain'] !=''){
			mysql_query("UPDATE Config SET value='".$_POST['sdomain']."' WHERE name='staging_domain'");
			//echo 'staging_domain Updated for Website DB<br/>';
		}
		
		mysql_close();
		}
		
	}
	myheader("index.php?install=1");
}
?>
<?php
define('BASEPATH', TRUE);
if ($_GET['fixdir'] != ''){
		if( chmod($_GET['fixdir'], 0777) ) {
			// more code
			chmod($_GET['fixdir'], 0755);
		}
		else
			echo $_GET['fixdir']. " Not Fixed contact administrator.";
	}
/*
if ($_GET['fixdbmanage']=='y' || $_GET['fixdbweb']=='y'){
require_once($cms_dir.'../cms/local/database.php');
	
if (!(empty($db['default']['hostname']) || empty($db['default']['username']) || empty($db['default']['password']) || empty($db['default']['database']))) {
	
	mysql_connect($db['default']['hostname'], $db['default']['username'], $db['default']['password']) or myheader('Error connecting to MySQL server: ' . mysql_error());
		// Select database
		mysql_select_db($db['default']['database']) or myheader('Error selecting MySQL database: ' . mysql_error());
		
		// Temporary variable, used to store current query
		$templine = '';
	
	if ($_GET['fixdbmanage']=='y'){
		// Read in entire file
		$lines = file("db_blank_manage.sql");
		// Loop through each line
		foreach ($lines as $line)
		{
		// Skip it if it's a comment
		if (substr($line, 0, 2) == '--' || $line == '')
			continue;
		
		// Add this line to the current segment
		$templine .= $line;
		// If it has a semicolon at the end, it's the end of the query
		if (substr(trim($line), -1, 1) == ';')
		{
			// Perform the query
			mysql_query($templine) or print('Error performing query \'<strong>' . $templine . '\': ' . mysql_error() . '<br /><br />');
			// Reset temp variable to empty
			$templine = '';
		}
		}
		
		//echo "Import Manage DB Import Done<br/>";
	}
	
	if ($_GET['fixdbweb']=='y'){
	// Read in entire file
		$lines = file("db_blank_website.sql");
		// Loop through each line
		foreach ($lines as $line)
		{
		// Skip it if it's a comment
		if (substr($line, 0, 2) == '--' || $line == '')
			continue;
		
		// Add this line to the current segment
		$templine .= $line;
		// If it has a semicolon at the end, it's the end of the query
		if (substr(trim($line), -1, 1) == ';')
		{
			// Perform the query
			mysql_query($templine) or print('Error performing query \'<strong>' . $templine . '\': ' . mysql_error() . '<br /><br />');
			// Reset temp variable to empty
			$templine = '';
		}
		}
		
		//echo "Import Website DB Import Done<br/>";
	}

}
	
}
*/

 $min_version    = '5.2.6';
?>
<!doctype html>
<html>
    <head>
    <meta charset="UTF-8">
    <title>TypologyCMS Setup</title>
    <script type="text/javascript" group="proto" src="prototype_min.js" language="javascript"> </script>
    <script type="text/javascript" group="proto" src="control.tabs.js" language="javascript"> </script>
    <script type="text/javascript" src="jquery-1.9.1.js" language="javascript"> </script>
    <script type="text/javascript" src="http://code.jquery.com/jquery-migrate-1.0.0.js" language="javascript"> </script>
    <script type="text/javascript" src="jQuery.Validate.min.js"></script>
    <script type="text/javascript">
        var $jq = jQuery.noConflict();
    </script>
    <link rel="stylesheet" href="style.css" type="text/css" media="screen" charset="utf-8" />
    </head>

    <body>
<div id="layout">
      <div style="margin:auto; width:720px;">
    <div style="margin-top:20px;" class="tndr right"><a target="_blank" href="http://www.vsapartners.com"><img alt="VSA" src="/cms/manage/assets/img/vsa_logo.png"></a></div>
    <?php if ($_GET[install]==1){ ?>
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>
    <div class="statusbar statusbar_20">
          <div class="statusbar_icon"></div>
          <div class="statusbar_text"> TypologyCMS installed Successfully. </div>

          <div class="statusbar_button"> </div>
        </div>
    <div class="statusbar statusbar_20">
          <div class="statusbar_icon"></div>
          <div class="statusbar_text"> <a href="/">We've set up a default page, take a look here to get started.</a> </div>

          <div class="statusbar_button">  </div>
        </div>
    <div class="statusbar statusbar_10">
          <div class="statusbar_text"> PLEASE REMEMBER TO COMPLETELY REMOVE THE INSTALLATION FOLDER.
        You will not be able to proceed beyond this point until the installation folder has been removed. This is a security feature of TypologyCMS. </div>
          <div class="statusbar_button"> <a href="index.php?remove_me=1">Remove Installation Folder</a> </div>
        </div>
    <?php exit;} 

if ($_GET[error_install] == 1){?>
    <br/>
    <br/>
    <br/>
    <br/>
    <br/>
    <div class="statusbar statusbar_90">
          <div class="statusbar_text"> TypologyCMS installed Failed.<br/>
        <?=$_GET[msg]?>
      </div>
          <div class="statusbar_button"> </div>
        </div>
    <?php }?>
    <form method="post" id="form1">
          <div class="tab_set">
        <div class="tab_nav clearfix">
              <ul id="edit_tabs2">
            <li><a href="#tab_content" class="active"><span>Configuration</span></a></li>
            <li><a href="#tab_information" class=""><span>Database</span></a></li>
            <li><a href="#tab_versions" class=""><span>Overview</span></a></li>
          </ul>
            </div>
        <!-- CONTENT -->
        <div class="tab_content" id="tab_content" style="">
              <button href="" id="n1" class="right button button_outline" style="margin-bottom:10px;"><span>Next ></span></button>
              <table width="100%" cellspacing="0" cellpadding="0" border="0" class="data_table small">
            <tr>
                  <th colspan="2">Site Configuration Points</th>
                </tr>
            <tbody>
                  <tr>
                <td width="50%">Live Domain</td>
                <td width="50%"><input class="required" type="text" name="ldomain" id="ldomain" /><br/><br/>Url of the live server</td>
              </tr>
                  <tr>
                <td width="50%">Staging Domain</td>
                <td width="50%"><input class="required" type="text" name="sdomain" id="sdomain" /><br/><br/>Url of the staging server</td>
              </tr>
                </tbody>
          </table>
              <br/>
              <table width="100%" cellspacing="0" cellpadding="0" border="0" class="data_table small">
            <tr>
                  <th colspan="2">Admin User</th>
                </tr>
            <tbody>
                  <tr>
                <td width="50%">Admin User name</td>
                <td width="50%"><input class="required" type="text" name="user" id="user" /><br/><br/>Set the username for your Super Administrator accoount</td>
              </tr>
                  <tr>
                <td width="50%">Admin Password</td>
                <td width="50%"><input class="required" type="password" name="password" id="password" /><br/><br/>Set the password for your Super Administrator account and confirm it in the field below.</td>
              </tr>
                  <tr>
                <td width="50%">Admin Email</td>
                <td width="50%"><input class="required" type="text" name="email" id="email" /><br/><br/>Enter an email address. This will be the email address of the web site Super Administrator</td>
              </tr>
                </tbody>
          </table>
            </div>
        <div class="tab_content" id="tab_information" style="display: none;">
              <button href="" id="n2" class="right button button_outline" style="margin-bottom:10px;"><span>Next ></span></button>
              <button href="" id="p1" class="right button button_outline" style="margin-bottom:10px;"><span>< Previous</span></button>
              <table width="100%" cellspacing="0" cellpadding="0" border="0" class="data_table small">
            <tr>
                  <th colspan="2">Default Database</th>
                </tr>
            <tbody>
                  <tr>
                <td width="50%">Default Hostname</td>
                <td width="50%"><input class="required" type="text" name="dhostname" id="dhostname" /><br/><br/>This is usually “localhost”</td>
              </tr>
                  <tr>
                <td width="50%">Default Username</td>
                <td width="50%"><input class="required" type="text" name="dusername" id="dusername" /><br/><br/>Either something as “root” or a username given by the host</td>
              </tr>
                  <tr>
                <td width="50%">Default Password</td>
                <td width="50%"><input type="password" name="dpassword" id="dpassword" /><br/><br/>For site security using a password for the database account is mandatory.</td>
              </tr>
                  <tr>
                <td width="50%">Default Database</td>
                <td width="50%"><input class="required" type="text" name="ddatabase" id="ddatabase" /><br/><br/>Some hosts allow only a certain DB name per site. Use table prefix in this case for distinct TypologyCMS sites.</td>
              </tr>
                  <tr>
                <td width="50%">Install Manage Database</td>
                <td width="50%"><p class="field switch">
                    <label class="cb-enable"><span>YES</span></label>
                    <label class="cb-disable selected"><span>NO</span></label>
                    <input type="checkbox" class="checkbox" name="dinstalldatabase" id="dinstalldatabase" />
                  </p>
                  <br/><br/><br/><br/>Install TypologyCMS manage database script
                  </td>
              </tr>
                  <tr>
                <td width="50%">Old Database Process</td>
                <td width="50%"><p class="field switch">
                    <label class="cb-enable"><span>Backup</span></label>
                    <label class="cb-disable selected"><span>Remove</span></label>
                    <input type="checkbox" class="checkbox" name="dinstalldatabaseold" id="dinstalldatabaseold" />
                  </p>
                  <br/><br/><br/><br/>Any existing backup tables from former TypologyCMS installations will be replaced
                  </td>
              </tr>
                </tbody>
          </table>
              <br/>
              <table width="100%" cellspacing="0" cellpadding="0" border="0" class="data_table small">
            <tr>
                  <th colspan="2">Publish Database</th>
                </tr>
            <tbody>
                  <tr>
                <td width="50%">Publish Hostname</td>
                <td width="50%"><input type="text" name="phostname" id="phostname" /><br/><br/>This is usually “localhost”</td>
              </tr>
                  <tr>
                <td width="50%">Publish Username</td>
                <td width="50%"><input type="text" name="pusername" id="pusername" /><br/><br/>Either something as “root” or a username given by the host</td>
              </tr>
                  <tr>
                <td width="50%">Publish Password</td>
                <td width="50%"><input type="password" name="ppassword" id="ppassword" /><br/><br/>For site security using a password for the database account is mandatory.</td>
              </tr>
                  <tr>
                <td width="50%">Publish Database</td>
                <td width="50%"><input type="text" name="pdatabase" id="pdatabase" /><br/><br/>Some hosts allow only a certain DB name per site. Use table prefix in this case for distinct TypologyCMS sites.
                </td>
              </tr>
                  <tr>
                <td width="50%">Install Publish Database</td>
                <td width="50%"><p class="field switch">
                    <label class="cb-enable"><span>YES</span></label>
                    <label class="cb-disable selected"><span>NO</span></label>
                    <input type="checkbox" class="checkbox" name="pinstalldatabase" id="pinstalldatabase" />
                  </p>
                  <br/><br/><br/><br/>Install TypologyCMS manage database script
                  </td>
              </tr>
                  <tr>
                <td width="50%">Old Database Process</td>
                <td width="50%"><p class="field switch">
                    <label class="cb-enable"><span>Backup</span></label>
                    <label class="cb-disable selected"><span>Remove</span></label>
                    <input type="checkbox" class="checkbox" name="pinstalldatabaseold" id="pinstalldatabaseold" />
                  </p>
                  <br/><br/><br/><br/>Any existing backup tables from former TypologyCMS installations will be replaced
                  </td>
              </tr>
                </tbody>
          </table>
            </div>
        <div class="tab_content" id="tab_versions" style="display: none;">
              <button href="" id="btnSubmit" name="btnSubmit" type="submit" form="form1" class="right button button_outline" style="margin-bottom:10px;"><span>Finish</span></button>
              <button href="" id="p2" class="right button button_outline" style="margin-bottom:10px;"><span>< Previous</span></button>
              <table width="100%">
            <tr>
                  <td style="padding:5px;" width="50%"><table width="100%" cellspacing="0" cellpadding="0" border="0" class="data_table small">
                      <tr>
                      <th colspan="2"> Main Configuration </th>
                    </tr>
                      <tr>
                      <td colspan="2"><strong>Site Configuration Points</strong></td>
                    </tr>
                      <tr>
                      <td width="50%"> Live Domain </td>
                      <td width="50%"><label id="ldomain_l" /></td>
                    </tr>
                      <tr>
                      <td width="50%"> Staging Domain </td>
                      <td width="50%"><label id="sdomain_l" /></td>
                    </tr>
                      <tr>
                      <td colspan="2"><strong>Admin User</strong></td>
                    </tr>
                      <tr>
                      <td width="50%"> Admin User name </td>
                      <td width="50%"><label id="user_l" /></td>
                    </tr>
                      <tr>
                      <td width="50%"> Admin Password </td>
                      <td width="50%"><label id="password_l" /></td>
                    </tr>
                      <tr>
                      <td width="50%"> Admin Email </td>
                      <td width="50%"><label id="email_l" /></td>
                    </tr>
                    </table></td>
                  <td style="padding:5px;" width="50%"><table width="100%" cellspacing="0" cellpadding="0" border="0" class="data_table small">
                      <tr>
                      <th colspan="2"> Database Configuration </th>
                    </tr>
                      <tr>
                      <td width="50%"> Default Hostname </td>
                      <td width="50%"><label id="dhostname_l" /></td>
                    </tr>
                      <tr>
                      <td width="50%"> Default Username </td>
                      <td width="50%"><label id="dusername_l" /></td>
                    </tr>
                      <tr>
                      <td width="50%"> Default Password </td>
                      <td width="50%"><label id="dpassword_l" /></td>
                    </tr>
                      <tr>
                      <td width="50%"> Default Database </td>
                      <td width="50%"><label id="ddatabase_l" /></td>
                    </tr>
                      <tr>
                      <td width="50%"> Install Manage Database </td>
                      <td width="50%"><label id="dinstalldatabase_l">NO</label></td>
                    </tr>
                      <tr>
                      <td width="50%"> Old Database Process </td>
                      <td width="50%"><label id="dinstalldatabaseold_l">NO</label></td>
                    </tr>
                      <tr>
                      <td width="50%"> Publish Hostname </td>
                      <td width="50%"><label id="phostname_l" /></td>
                    </tr>
                      <tr>
                      <td width="50%"> Publish Username </td>
                      <td width="50%"><label id="pusername_l" /></td>
                    </tr>
                      <tr>
                      <td width="50%"> Publish Password </td>
                      <td width="50%"><label id="ppassword_l" /></td>
                    </tr>
                      <tr>
                      <td width="50%"> Publish Database </td>
                      <td width="50%"><label id="pdatabase_l" /></td>
                    </tr>
                      <tr>
                      <td width="50%"> Install Publish Database </td>
                      <td width="50%"><label id="pinstalldatabase_l">NO</label></td>
                    </tr>
                      
                      <td width="50%"> Old Database Process </td>
                      <td width="50%"><label id="pinstalldatabaseold_l">NO</label></td>
                    </tr>
                    </table></td>
                </tr>
          </table>
              <br/>
              <table width="100%">
            <tr>
                  <td style="padding:5px;" width="50%"><table width="100%" cellspacing="0" cellpadding="0" border="0" class="data_table small">
                      <tr>
                      <th colspan="2"> Pre-Installation Check </th>
                    </tr>
                      <tr>
                      <td width="50%"> PHP Version >=
                          <?=$min_version?></td>
                      <td width="50%"><?php
            if (version_compare(PHP_VERSION, $min_version, '<')) {    
				echo '<span style="padding: 2px 5px; color: #fff; background-color:red;">NO</span>';
			} else {
				echo '<span style="padding: 2px 5px; color: #fff; background-color:green;">YES</span>';
			}?></td>
                    </tr>
                      <?php
            if (is_callable('apache_get_modules')) {
    
        $ini_options 	= array(
            'short_open_tag'		=> true
            );
    
        foreach ($ini_options as $varname => $value) {
    
            $current = ini_get($varname);
            
            if ($current == $value) {
                $message = '<span style="padding: 2px 5px; color: #fff; background-color:green;">YES</span>';
            } else {
                $message = '<span style="padding: 2px 5px; color: #fff; background-color:red;">NO</span>';
            }
    
            echo '<tr><td width="50%">HTTPD.CONF Setting '.$varname . '</td><td width="50%">' . $message .'</td></tr>';
        
        }
    
        if (in_array('mod_rewrite', apache_get_modules())) {
            echo '<tr><td width="50%">HTTPD.CONF  Setting mod_rewrite </td><td width="50%"><span style="padding: 2px 5px; color: #fff; background-color:green;">YES</span></td></tr>';	} else {
            echo '<tr><td width="50%">HTTPD.CONF Setting mod_rewrite </td><td width="50%"><span style="padding: 2px 5px; color: #fff; background-color:red;">NO</span></td></tr>';
        }
    
    } else {
			echo '<tr>
					<td colspan="2" style="text-align:center">
						<span style="padding: 2px 5px; color: #fff; background-color:orange;">UNKNOWN</span> Unable to call "apache_get_modules", PHP must be running as CGI - assuming you already checked server requirements.
					</td>
				</tr>';
    }?>
                      <?php      
     $extension_checks 	= array(
	    'curl','dom','gd','json','libxml','mcrypt','mysql','mbstring','SimpleXML','xsl'
	    );

	foreach ($extension_checks as $ext) {

		if (!extension_loaded($ext)) {
			$message = '<span style="padding: 2px 5px; color: #fff; background-color:red;">NO</span>';
		} else {
			$message = '<span style="padding: 2px 5px; color: #fff; background-color:green;">YES</span>';
		}

		echo '<tr><td width="50%">PHP.INI Setting '.$ext . '</td><td width="50%">' . $message .'</td></tr>';
	
	}?>
                    </table></td>
                  <td style="padding:5px;" width="50%"><table width="100%" cellspacing="0" cellpadding="0" border="0" class="data_table small">
                      <tr>
                      <th colspan="2"> Checking Directories </th>
                    </tr>
                      <?php
			$cms_dir = $_SERVER['DOCUMENT_ROOT'] . ((dirname($_SERVER['SCRIPT_NAME']) == '/') ? '/' : '').'/cms/';
			
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
				
			foreach ($folder_checks as $directory => $check_write) {
		
				if (!is_dir($cms_dir.$directory)) {
					$message = '<span style="padding: 2px 5px; color: #fff; background-color:red;">NO</span> Directory does not exist.</strong>';
				} else if ($check_write && !is_writable($cms_dir.$directory)) {
					$message = '<span style="padding: 2px 5px; color: #fff; background-color:red;">NO</span> Do not have write permissions to directory';
				} else {
					$message = '<span style="padding: 2px 5px; color: #fff; background-color:green;">YES</span>';
				}
				echo '<tr><td width="50%">'.$directory . '/ </td><td width="50%">' . $message .'</td></tr>';
			}
			?>
                    </table></td>
                </tr>
          </table>
              <br/>
            </div>
      </div>
        </form>
  </div>
    </div>
<script language="javascript">
		var tabs = new Control.Tabs('edit_tabs2');  
		
		$('p1').observe('click',function(event){ 
			tabs.previous();  
			Event.stop(event);  
		}.bindAsEventListener(edit_tabs2));  
		$('n1').observe('click',function(event){  
			tabs.next();  
			Event.stop(event);  
		}.bindAsEventListener(edit_tabs2));  
		$('p2').observe('click',function(event){ 
			tabs.previous();  
			Event.stop(event);  
		}.bindAsEventListener(edit_tabs2));  
		$('n2').observe('click',function(event){  
			tabs.next();  
			Event.stop(event);  
		}.bindAsEventListener(edit_tabs2)); 
    </script>
    <script type="text/javascript">
	   jQuery(document).ready(function() {
	  	 jQuery("#form1").validate();
	   });
	</script> 
<script>
		$jq(document).ready( function(){
		$jq(".cb-enable").click(function(){
			var parent = $jq(this).parents('.switch');
			$jq('.cb-disable',parent).removeClass('selected');
			$jq(this).addClass('selected');
			$jq('.checkbox',parent).attr('checked', true);
			$jq('#'+$jq('.checkbox',parent).attr('id')+'_l').text('YES');
		});
		$jq(".cb-disable").click(function(){
			var parent = $jq(this).parents('.switch');
			$jq('.cb-enable',parent).removeClass('selected');
			$jq(this).addClass('selected');
			$jq('.checkbox',parent).attr('checked', false);
			$jq('#'+$jq('.checkbox',parent).attr('id')+'_l').text('NO');
		});
	});
   $jq(function(){
	   	$jq('#ldomain').keyup(function(){
                 $jq('#ldomain_l').text($jq(this).val());
         });
		 $jq('#sdomain').keyup(function(){
                 $jq('#sdomain_l').text($jq(this).val());
         });
		 $jq('#user').keyup(function(){
                 $jq('#user_l').text($jq(this).val());
         });
		 $jq('#password').keyup(function(){
                 var text='';
				 for (i = 1; i <= $jq(this).val().length ; i++) {
					text += "*";
				}
				$jq('#password_l').text(text);
         });
		 $jq('#email').keyup(function(){
                 $jq('#email_l').text($jq(this).val());
         });
		 
		 $jq('#dusername').keyup(function(){
                 $jq('#dusername_l').text($jq(this).val());
         });
		 
		 $jq('#dhostname').keyup(function(){
                 $jq('#dhostname_l').text($jq(this).val());
         });
		 
		 $jq('#dpassword').keyup(function(){
			 var text='';
				 for (i = 1; i <= $jq(this).val().length ; i++) {
					text += "*";
				}
				$jq('#dpassword_l').text(text);
         });
		 
		 $jq('#ddatabase').keyup(function(){
                 $jq('#ddatabase_l').text($jq(this).val());
         });
		 
		 $jq('#phostname').keyup(function(){
                 $jq('#phostname_l').text($jq(this).val());
         });
		 
		 $jq('#pusername').keyup(function(){
                 $jq('#pusername_l').text($jq(this).val());
         });
		 
		 $jq('#ppassword').keyup(function(){
                var text='';
				for (i = 1; i <= $jq(this).val().length ; i++) {
					text += "*";
				}
				$jq('#ppassword_l').text(text);
         });
		 
		 $jq('#pdatabase').keyup(function(){
                 $jq('#pdatabase_l').text($jq(this).val());
         });
    });
    </script>
<style>
    .cb-enable, .cb-disable, .cb-enable span, .cb-disable span { background: url(switch.gif) repeat-x; display: block; float: left; }
	.cb-enable span, .cb-disable span { line-height: 30px; display: block; background-repeat: no-repeat; font-weight: bold; }
	.cb-enable span { background-position: left -90px; padding: 0 10px; }
	.cb-disable span { background-position: right -180px;padding: 0 10px; }
	.cb-disable.selected { background-position: 0 -30px; }
	.cb-disable.selected span { background-position: right -210px; color: #fff; }
	.cb-enable.selected { background-position: 0 -60px; }
	.cb-enable.selected span { background-position: left -150px; color: #fff; }
	.switch label { cursor: pointer; }
	.switch input { display: none; }
	.error{ color:red; margin-left:5px;}
    </style>
</body>
</html>