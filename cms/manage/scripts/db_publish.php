<?php

error_reporting(E_ALL);
ini_set('display_errors', 'On'); 

date_default_timezone_set('America/New_York');

define('NL', "\r\n");
define('DOCROOT', $_SERVER['DOCUMENT_ROOT']);
define('SITEPATH', str_replace('/scripts', '', dirname($_SERVER['SCRIPT_NAME'])));

define('VERBOSE', (!empty($_GET['verbose']) ? TRUE : FALSE));

class CMSPublish {

	var $config					= array();
		
	var $cms_directory			= null;

	var $valid_modules 			= array('page', 'file', 'template', 'config', 'maintenance', 'page_attributejoin', 'page_attributevalue', 'page_attributegroup', 'page_redirect');
	
	var $remote_method			= 'file_add/index/';
	
	private $_email_from		= '';
	
	private $_db_config			= array();
	private $_MANAGE_DB			= null;
	private $_WEB_DB			= null;
	
	private $_delete_jobs		= TRUE;
	private $_send_file_data	= TRUE;
	
	private $_completed_jobs	= array();
	private $_send_files		= array();
	private $_send_maintenance	= array();

	function __construct($options=array()) {

			ob_start();
			
			$this->cms_directory = realpath (DOCROOT . ((dirname($_SERVER['SCRIPT_NAME']) == '/') ? '/' : dirname($_SERVER['SCRIPT_NAME'])).'/../') . '/';
			
			$this->_loadDB();					

			$this->_MANAGE_DB 	= new DatabaseConnection($this->_db_config['default']['database'], $this->_db_config['default']['hostname'], $this->_db_config['default']['username'], $this->_db_config['default']['password']);

			$this->_email_from 	= 'noreply@'.$_SERVER["HTTP_HOST"];

			if (!$this->_loadConfig()) exit(' !!! ERROR LOADING CONFIG');

			if ($this->config['publish']['publish_method'] == 'local_table') {
				exit('Your publish configuration is set to "Local Table" mode. This means publishes are immediate and this script is not needed.');			
			}

			// Set timezone
			if (!empty($this->config['general']['timezone'])) {
				date_default_timezone_set($this->config['general']['timezone']);
			}



	}

	function __destruct() {			

			$buffer = ob_get_contents();
			@ob_end_clean();

			if (strlen($buffer)) {

				if ($this->config['publish']['send_publish_report_email'] == 'TRUE') {
					// Send notification email
					mail($this->config['publish']['publish_report_address'], 'CMS Publish Report', $buffer, 'From: '.$this->_email_from);	
				}
					
				echo nl2br($buffer);
			
			}
	
	}


	// ------------------------------------------------------------------------
	

	public function go() {

			$jobs				= $this->_MANAGE_DB->select('publish_queue', 'queue_date <= "'.date('Y-m-d H:i:s').'"');

			// Need to create second db call AFTER the first one has been used once. No idea why...
				$this->_WEB_DB 		= new DatabaseConnection($this->_db_config['publish_db']['database'], $this->_db_config['publish_db']['hostname'], $this->_db_config['publish_db']['username'], $this->_db_config['publish_db']['password']);
			
			if ($this->_db_config['dr']){
				//We need to create a aditional Database connection for the disaster recovery database
				$this->_DR_DB 		= new DatabaseConnection($this->_db_config['dr_db']['database'], $this->_db_config['dr_db']['hostname'], $this->_db_config['dr_db']['username'], $this->_db_config['dr_db']['password']);
			}
		
		
			if (count($jobs)) {

				if ($this->_delete_jobs === FALSE) echo 'WARN: Not going to delete job requests when done. Testing mode.' . NL;					
		
				foreach ($jobs as $job) {
	
					if (!in_array($job['module'], $this->valid_modules)) {
						echo 'Skipping, invalid module ('.$job['module'].')' . NL;
						continue; 					
					}
	
					$job_data = strlen($job['object']) ? unserialize(base64_decode($job['object'])) : '';
					
					switch ($job['queue_type']) {					
						case 'publish':
						case 'sort':
						case 'move':
							$this->_processPublishJob($job, $job_data);
							break;			
						case 'delete':
							$this->_processDeleteJob($job, $job_data);
							break;			
						case 'maintenance':
							$this->_processMaintenanceJob($job);
							break;			
						default:						
							// WHOOPS, SOMETHING IS WRONG.
							// TODO: Error						
							continue;
							break;					
					}	// End switch $job['queue_type']
	
				} // End foreach $jobs
	
				$this->_sendRemoteRequest();
				$this->_deleteQueues();
			
			} else {
			
				if (VERBOSE) echo 'No publish queues.';
			
			}
	
	}	
	
	//No change for DR
	private function _processMaintenanceJob($job=array()) {
			$this->_send_maintenance[] 	= $job['title'];
			$this->_completed_jobs[] 	= $job['queue_id'];
	}

	//Updated for DR
	private function _processDeleteJob($job=array(), $job_data=array()) {

			if (empty($job['module_id'])) return;
			
			$query = 'DELETE FROM '.$job['module'].' WHERE '.$job['module'].'_id = '.$this->_WEB_DB->prepData($job['module_id']).' LIMIT 1;';
			
			if (!$this->_WEB_DB->query($query)) {
				echo '** ERROR: '.$this->_WEB_DB->getError() . NL;
			} else {
				echo '- Deleting '.$job['module'].' # '.$job['module_id'].' on live servers.' . NL;
				$this->_completed_jobs[] = $job['queue_id'];
			}

			// Extra stuff just for a specific module

			if ($job['module'] == 'page') {
			
				// Don't forget to clear the cache too
				$query = 'DELETE FROM cache WHERE module = "page" AND module_id = '.$this->_WEB_DB->prepData($job['module_id']);
				$this->_WEB_DB->query($query);

				$query 	= 'INSERT INTO activity (module, module_id, description, date) VALUES ("page", "'.$job['module_id'].'", "Deleting page (Queue)", NOW()); ';
				$this->_MANAGE_DB->query($query);

			}
	
	
		if ($this->_db_config['dr']){
			

			if (empty($job['module_id'])) return;
			
			$query = 'DELETE FROM '.$job['module'].' WHERE '.$job['module'].'_id = '.$this->_DR_DB->prepData($job['module_id']).' LIMIT 1;';
			
			if (!$this->_DR_DB->query($query)) {
				echo '** ERROR: '.$this->_DR_DB->getError() . NL;
			} else {
				echo '- Deleting '.$job['module'].' # '.$job['module_id'].' on live servers.' . NL;
				$this->_completed_jobs[] = $job['queue_id'];
			}

			// Extra stuff just for a specific module

			if ($job['module'] == 'page') {
			
				// Don't forget to clear the cache too
				$query = 'DELETE FROM cache WHERE module = "page" AND module_id = '.$this->_DR_DB->prepData($job['module_id']);
				$this->_DR_DB->query($query);

				$query 	= 'INSERT INTO activity (module, module_id, description, date) VALUES ("page", "'.$job['module_id'].'", "DR: Deleting page (Queue)", NOW()); ';
				$this->_MANAGE_DB->query($query);

			}
	
		
		}
	}

	//Updated for DR
	private function _processPublishAttributeJob($job=array(), $job_data=array()) {


// if ($job['module_id'] != '10063') return;

			if (empty($job_data['joins']) || !count($job_data['joins'])) {
				return;
			}

	        // First we need a list of current values
			$current_raw		= $this->_WEB_DB->select('page_attributejoin', 'page_id = "'.$job['module_id'].'"');
	        $current            = array();
	        foreach ($current_raw as $item) {
	            if (!array_key_exists($item['page_attributegroup_id'], $current)) $current[$item['page_attributegroup_id']] = array();
	            $current[$item['page_attributegroup_id']][] = $item['page_attributevalue_id'];
	        }

// echo '<pre>current_raw:'.print_r($current_raw, true).'</pre>';
// echo '<pre>CURRENT:'.print_r($current, true).'</pre>';
// echo '<pre>JOINS:'.print_r($job_data['joins'], true).'</pre>';

			// ---------------------------------------------------------------------------
			// Delete Attribute Joins

			$job_data['joins_grouped'] = array();
			foreach ($job_data['joins'] as $item) {
	            if (!array_key_exists($item['page_attributegroup_id'], $job_data['joins_grouped'])) $job_data['joins_grouped'][$item['page_attributegroup_id']] = array();
	            $job_data['joins_grouped'][$item['page_attributegroup_id']][] = $item['page_attributevalue_id'];
	        }
			foreach ($job_data['joins_grouped'] as $group_id => $values) {
				if (!empty($current[$group_id]) && count($current[$group_id])) {
					$value_delete   = array_diff($current[$group_id], $values);

//echo '<pre>TO DELETE:'.print_r($value_delete, true).'</pre>';

					if (count($value_delete)) {
						foreach ($value_delete as $delete_id) {
							$query = 'DELETE FROM page_attributejoin WHERE page_attributevalue_id = '.$this->_WEB_DB->prepData($delete_id).' AND page_attributegroup_id = '.$this->_WEB_DB->prepData($group_id).' AND page_id = '.$this->_WEB_DB->prepData($job['module_id']);
							$this->_WEB_DB->query($query);
						}
					}
				}
			}

			// ---------------------------------------------------------------------------
			// Update Attribute Joins

			$table_fields 	= $this->_WEB_DB->getTableFields($job['module']);
			$update_fields 	= array();
			$insert_fields 	= array();

			foreach ($job_data['joins'] as $join) {

				if ($join['page_attributevalue_id'] > 0) {

					foreach ($join as $k => $v) {

						if (!in_array($k, $table_fields)) continue;

						if (is_array($v))$v = json_encode($v);
						$value 				= $this->_WEB_DB->prepData($v);
						$update_fields[] 	= $k . "=" . $value;
						$insert_fields[$k]	= $value;
						
					}

					$query 	= 'INSERT INTO ' . $job['module'] . ' (' 
							. implode(', ', array_keys($insert_fields)) 
							. ') VALUES ('
							. implode(', ', $insert_fields)
							. ') ON DUPLICATE KEY UPDATE '
							. implode(', ', $update_fields);

					if (!$this->_WEB_DB->query($query)) {
						echo '** ERROR: '.$this->_WEB_DB->getError() . NL;
					}

				}

			}
			
			$this->_completed_jobs[] = $job['queue_id'];
			echo '- Publishing '.$job['module'].' # '.$job['module_id']. NL;
			


	
		if ($this->_db_config['dr']){
			


// if ($job['module_id'] != '10063') return;

			if (empty($job_data['joins']) || !count($job_data['joins'])) {
				return;
			}

	        // First we need a list of current values
			$current_raw		= $this->_DR_DB->select('page_attributejoin', 'page_id = "'.$job['module_id'].'"');
	        $current            = array();
	        foreach ($current_raw as $item) {
	            if (!array_key_exists($item['page_attributegroup_id'], $current)) $current[$item['page_attributegroup_id']] = array();
	            $current[$item['page_attributegroup_id']][] = $item['page_attributevalue_id'];
	        }

// echo '<pre>current_raw:'.print_r($current_raw, true).'</pre>';
// echo '<pre>CURRENT:'.print_r($current, true).'</pre>';
// echo '<pre>JOINS:'.print_r($job_data['joins'], true).'</pre>';

			// ---------------------------------------------------------------------------
			// Delete Attribute Joins

			$job_data['joins_grouped'] = array();
			foreach ($job_data['joins'] as $item) {
	            if (!array_key_exists($item['page_attributegroup_id'], $job_data['joins_grouped'])) $job_data['joins_grouped'][$item['page_attributegroup_id']] = array();
	            $job_data['joins_grouped'][$item['page_attributegroup_id']][] = $item['page_attributevalue_id'];
	        }
			foreach ($job_data['joins_grouped'] as $group_id => $values) {
				if (!empty($current[$group_id]) && count($current[$group_id])) {
					$value_delete   = array_diff($current[$group_id], $values);

//echo '<pre>TO DELETE:'.print_r($value_delete, true).'</pre>';

					if (count($value_delete)) {
						foreach ($value_delete as $delete_id) {
							$query = 'DELETE FROM page_attributejoin WHERE page_attributevalue_id = '.$this->_DR_DB->prepData($delete_id).' AND page_attributegroup_id = '.$this->_DR_DB->prepData($group_id).' AND page_id = '.$this->_DR_DB->prepData($job['module_id']);
							$this->_DR_DB->query($query);
						}
					}
				}
			}

			// ---------------------------------------------------------------------------
			// Update Attribute Joins

			$table_fields 	= $this->_DR_DB->getTableFields($job['module']);
			$update_fields 	= array();
			$insert_fields 	= array();

			foreach ($job_data['joins'] as $join) {

				if ($join['page_attributevalue_id'] > 0) {

					foreach ($join as $k => $v) {

						if (!in_array($k, $table_fields)) continue;

						if (is_array($v))$v = json_encode($v);
						$value 				= $this->_DR_DB->prepData($v);
						$update_fields[] 	= $k . "=" . $value;
						$insert_fields[$k]	= $value;
						
					}

					$query 	= 'INSERT INTO ' . $job['module'] . ' (' 
							. implode(', ', array_keys($insert_fields)) 
							. ') VALUES ('
							. implode(', ', $insert_fields)
							. ') ON DUPLICATE KEY UPDATE '
							. implode(', ', $update_fields);

					if (!$this->_DR_DB->query($query)) {
						echo '** ERROR: '.$this->_DR_DB->getError() . NL;
					}

				}

			}
			
			$this->_completed_jobs[] = $job['queue_id'];
			echo '- Publishing '.$job['module'].' # '.$job['module_id']. NL;
			


	
		}
	}

	//Updated for DR
	private function _processPublishJob($job=array(), $job_data=array()) {

			$update_fields 	= array();
			$insert_fields 	= array();


			// Attribute join publish is quite complex, it should have it's own update method
			if ($job['module'] == 'page_attributejoin') {
				return $this->_processPublishAttributeJob($job, $job_data);
			}


			if (($job['module'] == 'file') && !empty($job_data['server_path'])) {
				// Publish is for file with path set, save and remove it
				// TODO: the 'files/' should be coming from config (file_directory)
				
				if ($this->_send_file_data) {
					$this->_send_files['files/' . $job_data['app_path']] = $job_data['server_path'];
				} else {
					echo ' - (Sending file data disabled in publish config)'. NL;
				}
				unset($job_data['server_path']);
			}

			$table_fields = $this->_WEB_DB->getTableFields($job['module']);
			
//echo '<pre>'.print_r($job_data, true).'</pre>';			
			
			foreach ($job_data as $k => $v) {

				if (!in_array($k, $table_fields)) continue;

				if (is_array($v))$v = json_encode($v);
				$value 				= $this->_WEB_DB->prepData($v);
				$update_fields[] 	= $k . "=" . $value;
				$insert_fields[$k]	= $value;
				
			}

			$query 	= 'INSERT INTO ' . $job['module'] . ' (' 
					. implode(', ', array_keys($insert_fields)) 
					. ') VALUES ('
					. implode(', ', $insert_fields)
					. ') ON DUPLICATE KEY UPDATE '
					. implode(', ', $update_fields);

			if (!$this->_WEB_DB->query($query)) {
				echo '** ERROR: '.$this->_WEB_DB->getError() . NL;
			} else {
				echo '- Publishing '.$job['module'].' # '.$job['module_id']. NL;
				$this->_completed_jobs[] = $job['queue_id'];
			}

			// Extra stuff just for a specific module

			if ($job['module'] == 'page') {
			
				// Don't forget to clear the cache too
				$query = 'DELETE FROM cache WHERE module = "page" AND module_id = '.$this->_WEB_DB->prepData($job['module_id']);
				$this->_WEB_DB->query($query);

				$query 	= 'INSERT INTO activity (module, module_id, description, date) VALUES ("page", "'.$job['module_id'].'", "Publishing page (Queue)", NOW()); ';
				$this->_MANAGE_DB->query($query);
			
			} else if ($job['module'] == 'template') {

				// TODO: Need to add server path for this to work properly
				/*
				if (!empty($job_data['template_html_xsl_path'])) {
					$template_html_xsl_path = $job_data['template_html_xsl_path'];
					$this->_send_files[$template_html_xsl_path] = DOCROOT . SITEPATH . $template_html_xsl_path;
				}
				
				if (!empty($job_data['template_xml_xsl_path'])) {
					$template_xml_xsl_path = $job_data['template_xml_xsl_path'];
					$this->_send_files[$template_xml_xsl_path] = DOCROOT . SITEPATH . $template_xml_xsl_path;
				}
				*/
			
			}
		if ($this->_db_config['dr']){
			$update_fields 	= array();
			$insert_fields 	= array();


			// Attribute join publish is quite complex, it should have it's own update method
			if ($job['module'] == 'page_attributejoin') {
				return $this->_processPublishAttributeJob($job, $job_data);
			}


			if (($job['module'] == 'file') && !empty($job_data['server_path'])) {
				// Publish is for file with path set, save and remove it
				// TODO: the 'files/' should be coming from config (file_directory)
				
				if ($this->_send_file_data) {
					$this->_send_files['files/' . $job_data['app_path']] = $job_data['server_path'];
				} else {
					echo ' - (Sending file data disabled in publish config)'. NL;
				}
				unset($job_data['server_path']);
			}

			$table_fields = $this->_DR_DB->getTableFields($job['module']);
			
//echo '<pre>'.print_r($job_data, true).'</pre>';			
			
			foreach ($job_data as $k => $v) {

				if (!in_array($k, $table_fields)) continue;

				if (is_array($v))$v = json_encode($v);
				$value 				= $this->_DR_DB->prepData($v);
				$update_fields[] 	= $k . "=" . $value;
				$insert_fields[$k]	= $value;
				
			}

			$query 	= 'INSERT INTO ' . $job['module'] . ' (' 
					. implode(', ', array_keys($insert_fields)) 
					. ') VALUES ('
					. implode(', ', $insert_fields)
					. ') ON DUPLICATE KEY UPDATE '
					. implode(', ', $update_fields);

			if (!$this->_DR_DB->query($query)) {
				echo '** ERROR: '.$this->_DR_DB->getError() . NL;
			} else {
				echo '- Publishing '.$job['module'].' # '.$job['module_id']. NL;
				$this->_completed_jobs[] = $job['queue_id'];
			}

			// Extra stuff just for a specific module

			if ($job['module'] == 'page') {
			
				// Don't forget to clear the cache too
				$query = 'DELETE FROM cache WHERE module = "page" AND module_id = '.$this->_DR_DB->prepData($job['module_id']);
				$this->_DR_DB->query($query);

				$query 	= 'INSERT INTO activity (module, module_id, description, date) VALUES ("page", "'.$job['module_id'].'", "DR: Publishing page (Queue)", NOW()); ';
				$this->_MANAGE_DB->query($query);
			
			} else if ($job['module'] == 'template') {

				// TODO: Need to add server path for this to work properly
				/*
				if (!empty($job_data['template_html_xsl_path'])) {
					$template_html_xsl_path = $job_data['template_html_xsl_path'];
					$this->_send_files[$template_html_xsl_path] = DOCROOT . SITEPATH . $template_html_xsl_path;
				}
				
				if (!empty($job_data['template_xml_xsl_path'])) {
					$template_xml_xsl_path = $job_data['template_xml_xsl_path'];
					$this->_send_files[$template_xml_xsl_path] = DOCROOT . SITEPATH . $template_xml_xsl_path;
				}
				*/
			
			}
		}
	
	return;
	}

	//No change for DR
	private function _sendRemoteRequest() {
	
//echo '<pre>_sendRemoteRequest:: _send_files:'.print_r($this->_send_files, true).'</pre>';		


			if (!$this->_send_file_data || (!count($this->_send_files) && !count($this->_send_maintenance))) return;

			// TODO: Make sure remote url ends in / and reduce //
			
			$boundary 	= '---------------------'.substr(md5(rand(0,32000)), 0, 10); 
			$data		= '--' . $boundary . NL;
			
			// Add authorization code. Required.
			$data .= 'Content-Disposition: form-data; name="remote_auth_code"'.NL.NL.$this->config['publish']['remote_auth_code'].NL;
			$data .= '--' . $boundary . NL;

			// FILES

			if ($this->_send_file_data && count($this->_send_files)) {

				$file_post_data = '';
			
				foreach ($this->_send_files as $app_path => $server_path) {
	
					if (file_exists($server_path)) {

						echo ' - Sending remote file: '.$app_path . NL;

						$data .= 'Content-Disposition: form-data; name="remote_files[]"'.NL.NL.$app_path.NL;
						$data .= '--' . $boundary . NL;					
					
						$file_data 	= '';
						$handle 	= fopen($server_path, "rb");
		
						while (!feof($handle)) $file_data .= fread($handle, 8192);
						fclose($handle);
		
						$file_post_data .= 'Content-Disposition: form-data; name="'.$app_path.'"; filename="'.$app_path.'"'.NL;
						$file_post_data .= 'Content-Type: image/jpeg'.NL;
						$file_post_data .= 'Content-Transfer-Encoding: binary'.NL.NL;
						$file_post_data .= $file_data.NL;
						$file_post_data .= '--' . $boundary . NL;					
					
					} else {
	
						echo ' !! WARN: Skipping file, doesn\' exist: '.$server_path . NL;
					
					}
				
				}
				
				$data .= $file_post_data;
			
			}


			// MAINTENANCE

			if (count($this->_send_maintenance)) {

				foreach ($this->_send_maintenance as $request) {

					echo ' - Sending remote maintenance request: '.$request . NL;

					$data .= 'Content-Disposition: form-data; name="remote_maintenance[]"'.NL.NL.$request.NL;
					$data .= '--' . $boundary . NL;
				}
			
			}


			$params = array(
					'http' => array(
						'method' 	=> 'POST'
						, 'header' 	=> 'Content-Type: multipart/form-data; boundary='.$boundary
						, 'content' => $data
						)
					);

			$ctx = stream_context_create($params);
			
			
			// Send request data to server. If there is more then one in the config, will send to each one.

			if (!is_array($this->config['publish']['publish_url'])) $this->config['publish']['publish_url'] = array($this->config['publish']['publish_url']);

			foreach ($this->config['publish']['publish_url'] as $server) {
			
				if (!strlen($server)) continue;
			
				$url 	= rtrim($server, '/') . '/maintenance/remote/';
				$fp 	= fopen($url, 'rb', false, $ctx);
				

				if (strpos($http_response_header[0], '404') !== false) {
					exit(' !!! Error remote server not found! ('.$url.')');
				} else if (strpos($http_response_header[0], '500') !== false) {

					$response = stream_get_contents($fp);
					if ($response === false) {
						echo 'NO RESPONSE';
					} else {
						echo $response; 
					}

					exit(' !!! Error remote server 500! ('.$url.')');
				} else {
				
					$response = stream_get_contents($fp);
					if ($response === false) {
						throw new Exception("Problem reading data from $url");
					} else {
						echo $response; 
					}
				
				}
			
			}

	}
	
	//No change for DR
	private function _deleteQueues() {
	
			if (!count($this->_completed_jobs) || ($this->_delete_jobs === FALSE)) return;

			$query 		= 'DELETE FROM publish_queue WHERE queue_id IN ('. implode(', ', $this->_completed_jobs) .'); ';
			if (!$this->_MANAGE_DB->query($query)) {
				echo '<b>** ERROR: '.$this->_WEB_DB->getError() . '</b>'. NL;
			} else {
				echo '- Deleting publish queue # '.implode(', ', $this->_completed_jobs) . NL;					
			}	
	
	}


	// ------------------------------------------------------------------------

	//No change for DR
	private function _loadConfig() {

			$config		= $this->_MANAGE_DB->select('config', 'zone = "manage"');

			if (count($config)) {

				foreach ($config as $item) {

					if (strstr($item['options'], '"multi":"yes"')) {

						// Make sure its a json string
						if ((substr($item['value'], 0, 1) == '[') && (substr($item['value'], -1) == ']')) {
							$item['value'] = json_decode($item['value'], true);
						}

					}					

					if (!array_key_exists($item['zone_group'], $this->config)) {
						$this->config[$item['zone_group']] = array();
					}

					$this->config[$item['zone_group']][$item['name']] = $item['value'];
				}

				return TRUE;
			
			} else {
				return FALSE;
			}	


	}
	
	//No change for DR
	private function _loadDB() {

			$path = DOCROOT.'/cms/local/database.php';
			if (!file_exists($path)) {
		
				echo '<strong style="color: red;">FAIL. Can not locate config/database.php</strong>';
				return FALSE;
		
			} else {
				
				define('BASEPATH', TRUE);
				include($path);
			
				if (empty($db['default']['hostname']) || empty($db['default']['username']) || empty($db['default']['password']) || empty($db['default']['database'])) {
					echo '<strong style="color: red;">FAIL. Local database cridenials missing from config.</strong>';
					return FALSE;
				} else if (empty($db['publish_db']['hostname']) || empty($db['publish_db']['username']) || empty($db['publish_db']['password']) || empty($db['publish_db']['database'])) {
					echo '<strong style="color: red;">FAIL. Publish database cridenials missing from config.</strong>';
					return FALSE;
				} else {
					require_once('db_wrapper.inc.php');
					$this->_db_config = $db;
					return TRUE;
				}

			}
	
	}
	

}


// Style output for admin
if (VERBOSE) {
	echo '<style type="text/css">'
		. NL . ' BODY { background-color: #f6f6f6; font-family: courier; font-size: 11px; line-height: 14px;}'
		. '</style>';
}


$PUBLISH = new CMSPublish();
$PUBLISH->go();
