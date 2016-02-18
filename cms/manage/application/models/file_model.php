<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Typology CMS
 *
 * @author      VSA Partners / Louis D Walch (lwalch@vsapartners.com)
 * @link        http://www.vsapartners.com
 *
 */
 
 
require_once(APPPATH . 'models/core/application_model.php');

class File_model extends Application_model {

	var $table 		= 'file';
	var $id_field	= 'file_id';
	var $db_fields  = array('file_id','title','file_name','type','mime','ext','is_image','options','parent_id','create_date','update_date','import_id');


	var $FILE_CONF;

	var $select_set = array(
						'basic' 			=> array('file_id', 'title', 'mime', 'update_date')
						);
    // ------------------------------------------------------------------------

    function __construct() {	
			parent::__construct();
			$this->FILE_CONF = CI()->loadConfig('file');
    }    


    // ------------------------------------------------------------------------


	public function getTmpDirectory() {
	
			$dir = DOCROOT . zonepath($this->FILE_CONF['file_directory'], 'local') . '/' . $this->FILE_CONF['temp_folder'];
	
			if (!@is_dir($dir)) {
				// Create directory
				if (!@mkdir($dir, $this->_folder_perms)) show_error('Error uploading file. Could not create temporary upload directory. Please contact your system administrator.<br/><em>Path: '.$dir.'</em>');
			}
			
			return $dir;
			
	}

	public function getIdDirectory($path) {

			$full_path = dirname($path);
	
			if (!@is_dir($full_path)) {
				$folder_path = array($full_path);
			} else {
				// Already exists
				return true;
			}
			
			while (!@is_dir(dirname(end($folder_path)))
				&& dirname(end($folder_path)) != '/'
				&& dirname(end($folder_path)) != '.'
				&& dirname(end($folder_path)) != '') {
					array_push($folder_path, dirname(end($folder_path)));
			}
			
			while ($parent_folder_path = array_pop($folder_path)) {
				if(!@mkdir($parent_folder_path, $this->_folder_perms)) show_error("Can't create folder \"$parent_folder_path\".\n");
			}		

			return true;
	
	}



    // ------------------------------------------------------------------------
	// EXTENDED METHODS

	public function get($fields=array(), $select_set=FALSE) {
	
			$item 			= parent::get($fields, $select_set);
			$item_updated 	= array();
			
			if (count($item)) {
			
				// Make model specific updates to result array
				for ($i=0; $i < count($item); $i++) {
	
					$row 							= $item[$i];
					
					if (empty($params['SELECT_SET']) || ($params['SELECT_SET'] != 'basic')) {
					
						$row['sort_name'] 				= strtolower($row['title']);
						$row['date'] 					= date('m-d-Y H:i', strtotime($row[$this->date_field['update']]));
						$row['timestamp'] 				= date('U', strtotime($row[$this->date_field['update']]));
	
						// Only perform the following if this is a file
						if (!empty($row['type']) && ($row['type'] == 'file')) {
						
							$var_length					= (int) $this->FILE_CONF['file_dir_depth'] * 3;
							$path_array					= str_split(str_pad($row[$this->id_field], $var_length, '0', STR_PAD_LEFT), 3);	
							$upload_path				= implode('/', $path_array);
							
							/*
							// To get the file's directory path. Don't need but lets keep around.		
							unset($path_array[count($path_array)-1]);
							$dir_path					= implode('/', $path_array);
							$row['server_dir'] 			= DOCROOT . $this->FILE_CONF['file_location'] . $dir_path;
							*/
							
							$base_view_path				= $row[$this->id_field] 
														//. (($this->FILE_CONF['force_name_in_uri']) ? '/' . $row['file_name']  : '')
														. '/' . $row['file_name']
														. $row['ext'];

							// Add file paths
//							$row['real_path'] 			= DOCROOT . zonepath($this->FILE_CONF['file_directory'] . '/' . $upload_path . $row['ext'], '/', 'local');	

							$row['base_path'] 			= DOCROOT . zonepath($this->FILE_CONF['file_directory'] . '/' . $upload_path, 'local');	
							$row['server_path'] 		= $row['base_path'] . $row['ext'];

							$row['view_path'] 			= reduce_multiples($this->FILE_CONF['file_website_location'] . $base_view_path, '/');

							$row['manage_path']			= reduce_multiples(SITEPATH . $this->zone .CI()->SITE_CONF['file_uri_trigger'] . '/' . $base_view_path, '/');
		
							// Add file size
							$row['file_size'] 			= file_exists($row['server_path']) ? filesize($row['server_path']) : 0;
							$row['file_size_display']	= file_exists($row['server_path']) ? $this->formatFileSize(filesize($row['server_path'])) : 0;
	
						}
					}
	
					$item_updated[] = $row;

				}
				
				if (count($item_updated) > 1 && (!empty($item_updated[0]['sort_name']))) {
					// Sort array
					$sort_array = array();
					foreach($item_updated as $row) $sort_array[] = $row['sort_name'];
					array_multisort($sort_array, SORT_ASC, $item_updated);
				}

	 		}

			return $item_updated;
	}


	protected function formatFileSize($size) {
			$count = 0;
			$format = array("B","KB","MB","GB","TB","PB","EB","ZB","YB");
			while(($size/1024)>1 && $count<8) {
				$size=$size/1024;
				$count++;
			}
			$return = number_format($size,0,'','.')." ".$format[$count];
			return $return;
	}

	public function update($fields=array()) {
	
			if (!empty($fields['file_name'])) {
                $fields['file_name']   = strtolower(preg_replace("/[^a-z\-_\d]/i", "", underscore($fields['file_name'])));
            }

			$row =  parent::update($fields);

			if (empty($row['ext'])) {
				$sub = $this->getById($row[$this->id_field]);
				$row = $sub[0];
			}

			$var_length				= (int) $this->FILE_CONF['file_dir_depth'] * 3;
			
			$path_array				= str_split(str_pad($row[$this->id_field], $var_length, '0', STR_PAD_LEFT), 3);	
			$upload_path			= implode('/', $path_array);

			// Add file paths
			$row['server_path'] 	= DOCROOT . zonepath($this->FILE_CONF['file_directory'], 'local') . '/' . $upload_path . $row['ext'];
			$row['app_path'] 		= $upload_path . $row['ext'];

			if ($this->ADMIN_CONF['publish']['publish_method'] != 'local_table') {
				// Queue file for publish
				CI()->load->model('publish_queue_model');
				CI()->publish_queue_model->publish($this->table, $row[$this->id_field], $row);
			}

			return $row;

	}	


	public function delete($one, $two=NULL) {
	
		$id = $one;

		if (! ($id > 1)) show_error(__METHOD__ . ' Invalid id: '.$id);

		$item = CI()->file_model->first()->getById($id);
		
		if (parent::delete($id)) {

			if (!empty($item[$this->id_field])) {
	
				// Queue file for delete
				CI()->load->model('publish_queue_model');
				CI()->publish_queue_model->delete($this->table, $id);
	
				$children = $this->get(array('parent_id' => $id));
				if (count($children)) {
					foreach ($children as $child) {
						$this->delete($child[$this->id_field]);
					}
				}
		
				// Only perform the following if this is a file
				if (($item['type'] == 'file') && !empty($item['base_path'])) {
		
					// TODO: Should we add checks to make sure base_path starts with doc_root?
					// TODO: Should we check to make sure id_field is valid?
				
					$rm_result 	= shell_exec('rm -fv '.$item['base_path'] . '*' . $item['ext']);
		
					log_message('debug', 'Deleting file '.$item[$this->id_field].' ('.$rm_result.')');		
		
				}
			
			}
		
		}

		return true;

	}
	
}