<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Typology CMS
 *
 * @author      VSA Partners / Louis D Walch (lwalch@vsapartners.com)
 * @link        http://www.vsapartners.com
 *
 */
 
 
require_once(APPPATH . 'controllers/manage.php');

class File extends Manage {

	var	$has_module_config	= TRUE;
	var	$has_module_model	= TRUE;
	
	var $_folder_perms		= 0777;
	
	var $_default_parent_id	= 0;
	var $_current_parent_id	= null;

	var $require_login 		= TRUE;


	function __construct() {

			if (!empty($_POST['multiupload']) && ($_POST['multiupload'] == 145)) {
				$this->require_login = FALSE;
			}

			parent::__construct();
		
	}
	
	
	// ------------------------------------------------------------------------
	// CREATE

   function create($type=null, $parent=null) {
			
			if (is_null($parent)) $parent = $this->_default_parent_id;
			
			if ($type == 'file') {
			
				// CREATE FILE
				
			
			} else if ($type == 'collection') {
				
				// CREATE COLLECTION

				// Add action to wrapper title            
				$this->layout->appendTitle(ucwords(__FUNCTION__));            

				$this->_current_parent_id = $parent;
	
				$this->current_id			= -1;
				$edit_item					= $this->MODEL->getEmptyItem();
				$edit_item['type']			= $type;
				$edit_item['parent_id']		= $parent;
	
				$edit_params = array('fields' => $edit_item);
				$this->load->view('file/edit_collection', $edit_params);
			
			} else {
				show_error('Invalid create request. Unknown type: "'.$type.'".');
			}

    }
    

	// ------------------------------------------------------------------------
	// UPDATE

	public function update($type=null, $id=null) {
	
			$this->current_id 	= $id;
			$fields 			= $this->input->post('fields');
			$fields['type']		= $type;

			$this->layout->setLayout('plain');

			if (!count($fields)) show_error('Could not save. Invalid or missing update fields.');

			if ($this->input->get_post('return') == 'picker') {
				$this->layout->setLayout('plain');	
				$this->layout->setBodyClass('popup');	
			}

			if ($type == 'file') {
			
				// UPDATE FILE
				if (!($update_item =  $this->_updateFile($fields))) return FALSE;

				if ($this->input->get_post('return') == 'picker') {
					$url = $this->module . '/picker/' 
						. '?parent_id=' . $fields['parent_id']
						. '&hilite=file_' .$update_item[$this->id_field]
						. '&destination=' . $this->input->get_post('destination');
				} else {
					$this->session->set_flashdata('message', 'Your item has been saved.');
					$url = $this->module . '/edit/' . $update_item[$this->id_field];
				}

			
			} else if ($type == 'collection') {

				// UPDATE COLLECTION
				
				// Make sure there is not already a collection with that name?
				$existing_collection = $this->MODEL->get(array('title' => $fields['title'], 'parent_id' => $fields['parent_id']));
				if (!empty($existing_collection[0]['file_id'])) {
					
					// Collection already exits
					$this->load->view('file/error_collection_dupe', array('fields' => $fields));
					return FALSE;
					
				} 
				
				$update_item = $this->MODEL->update($fields);

				if ($this->input->get_post('return') == 'picker') {
					$url = $this->module . '/picker/' 
						. '?parent_id=' . $update_item[$this->id_field]
						. '&destination=' . $this->input->get_post('destination')
						. '&multi=' . $this->input->get_post('multi');
				} else {
					$this->session->set_flashdata('message', 'Your item has been saved.');
					$url = $this->module . '/edit/' . $update_item[$this->id_field];
				}

			} else {
				show_error('Invalid update request. Unknown type: "'.$type.'".');
			}
			
			redirect($url, 'location');
			echo '<a href="'.SITEPATH.$this->zone.'/'.$url.'">Return to edit</a>';


	}


	public function multiple($type=null, $id=-1) {
	
			$this->layout->setLayout('plain');	

			if (empty($_FILES) || !count($_FILES)) {
				show_error('You did not select any files to upload.');			
			}

			$upload_results				= array();

			$default_fields				= $this->input->post('fields');
			$default_fields['file_id']	= -1;
			$default_fields['type']		= 'file';

			$config = array(
				'upload_path' 		=> $this->MODEL->getTmpDirectory()
				, 'allowed_types'	=> $this->CONF['allowed_types']
				, 'max_size'		=> $this->CONF['max_size']
				, 'max_width' 		=> $this->CONF['max_width']
				, 'max_height'  	=> $this->CONF['max_height']
				, 'overwrite'		=> TRUE
				);
			$this->load->library('upload', $config);	

			$out = array();

			foreach ($_FILES as $file_key => $file) {
				
				if (empty($file['tmp_name'])) continue;

				// Upload tmp file
				if ($this->upload->do_upload($file_key)) {

					$tmp_file 			= $this->upload->data();

                    $file['name']               = rtrim($file['name'], $tmp_file['file_ext']);

					// Add tmp_file values to fields before update
					$fields						= $default_fields;
					$fields['title']			= $file['name'];
					$fields['file_name']		= $file['name'];
					$fields['is_image']			= $tmp_file['is_image'];
					$fields['mime']				= $tmp_file['file_type'];
					$fields['ext']				= $tmp_file['file_ext'];
					$fields['options'] 			= array(
													'image_size_str' 	=> $tmp_file['image_size_str']
													, 'image_width'		=> $tmp_file['image_width']
													, 'image_height'	=> $tmp_file['image_height']
													);

					// UPDATE DATABASE
					$upload_result 				= $this->MODEL->update($fields);
	

					// Make sure the id directory exists
					$this->MODEL->getIdDirectory($upload_result['server_path']);
		
					// Move file (we need the id from insert)
					rename($tmp_file['full_path'], $upload_result['server_path']);

					echo "{success: 'true', file_id: '".$upload_result['file_id']."'}";
					
					/*

					$out = array(
						'file_id' 	=> $upload_result['file_id']
						, 'success'	=> 'true'
						, 'title' 	=> $upload_result['title']
						);
				
					*/

				} else {
				
					echo "{success: 'false', error: '".addslashes(strip_tags($this->upload->display_errors()))."'}";

					/*
					$out	= array(
						'title'			=> $file['name']
						, 'success'		=> 'NO'
						, 'error'		=> $this->upload->display_errors()
						);
					
					*/

				}
			
			}
			
			/*
			header('Content-type: application/json');
			$out_json = json_encode($out, true);
			
			if (is_null($out_json)) {

				$out	= array(
						'title'			=> 'HELLO'
						, 'success'		=> 'NO'
						, 'error'		=> 'WRONG'
						);
				echo json_encode($out, true);
			
			} else {

				echo 'out_json:';
				var_dump($out_json);
			
			}
			
			exit('the end');
			
			*/
			
			exit();
			
	}


	private function _updateFile($fields) {
			
			$fields['type']				= 'file';
			$tmp_file					= FALSE;
			$collection					= $fields['parent_id'];

			if (!empty($_FILES['userfile']['tmp_name'])) {
			
				// UPLOADING FILE

				$config = array(
					'upload_path' 		=> $this->MODEL->getTmpDirectory()
					, 'allowed_types'	=> $this->CONF['allowed_types']
					, 'max_size'		=> $this->CONF['max_size']
					, 'max_width' 		=> $this->CONF['max_width']
					, 'max_height'  	=> $this->CONF['max_height']
					, 'overwrite'		=> TRUE
					);
				$this->load->library('upload', $config);	
	
				// Upload tmp file
				if ($this->upload->do_upload()) {
					$tmp_file 			= $this->upload->data();
				} else {
					$error 				= array('error' => $this->upload->display_errors());
					show_error($error['error']);
				}
			
			} else if ($this->input->post('tmp_file')) {

				// COMING FROM ERROR PAGE, TMP FILE ALREADY UPLOADED
				$tmp_file 				= json_decode(base64_decode($this->input->post('tmp_file')), TRUE);	

			}


			if ($tmp_file) {

				// UPLOADING A FILE, SOME ADDITIONAL CHECKING REQUIRED

				if ($this->input->post('dupe_action') == 'replace') {
	
					// Replacing an existing file
					
					if ($this->input->post('existing_id')) {
						$fields[$this->id_field] = $this->input->post('existing_id');
					}
	
				} else {
	
					// CHECK IF THIS IS A UNIQUE FILE
					
					$existing_file = $this->MODEL->get(array(
										'title'			=> !empty($fields['title']) ? $fields['title'] : $tmp_file['raw_name']
										, 'parent_id'	=> $collection
										));

					if (!empty($existing_file[0]['file_id'])) {
					
						// File already exits
											
						$view_params 	= array(
							'collection'	=> $collection
							, 'tmp_file'	=> base64_encode(json_encode($tmp_file))
							, 'existing'	=> $existing_file[0]
							);
			
						$this->load->view('file/error_file_dupe', $view_params);
			
						return FALSE;
						
					} 
					
					// Only do this if we are not replacing an existing file
					if (empty($fields['title'])) $fields['title'] = $tmp_file['raw_name'];
	
				}

				// Add tmp_file values to fields before update
				$fields['file_name']		= $tmp_file['raw_name'];
				$fields['is_image']			= $tmp_file['is_image'];
				$fields['mime']				= $tmp_file['file_type'];
				$fields['ext']				= $tmp_file['file_ext'];
				$fields['options'] 	= array(
												'image_size_str' 	=> $tmp_file['image_size_str']
												, 'image_width'		=> $tmp_file['image_width']
												, 'image_height'	=> $tmp_file['image_height']
												);
			}


			// UPDATE DATABASE
			$upload_result 				= $this->MODEL->update($fields);

			if ($tmp_file) {

				// Make sure the id directory exists
				$this->MODEL->getIdDirectory($upload_result['server_path']);
	
				// Move file (we need the id from insert)
				rename($tmp_file['full_path'], $upload_result['server_path']);
			}	

			$this->session->set_flashdata('hilight', 'file_'.$upload_result[$this->id_field]);
			
			return $upload_result;			
	
	}


	// ------------------------------------------------------------------------
	// EDIT

	function edit($id=null) {
			
			if ($id == $this->_default_parent_id) return $this->index();

			// Add action to wrapper title			
			$this->layout->appendTitle(ucwords(__FUNCTION__));			

			$this->current_id 	= $id;
			$edit_item 			= $this->MODEL->first()->getById($id);

			if (!count($edit_item)) show_error('Could not edit. Invalid item returned.');

			if ($edit_item['type'] == 'file') {

				// EDIT FILE

				if (!empty($edit_item['parent_id'])) {
					$this->_current_parent_id = $edit_item['parent_id'];
				}

				// Need to get parent's parent
				$parent_item = $this->MODEL->first()->getById($edit_item['parent_id']);
				$this->CONF['access_menu']['model_param']['parent_id'] 	= $edit_item['parent_id'];
				$this->_current_parent_id 								= $parent_item['parent_id'];

				$edit_params = array(
					'fields' 		=> $edit_item
					, 'collection'	=> $this->MODEL->first()->getById($edit_item['parent_id'])
					);
				$this->load->view('file/edit_file', $edit_params);

			} else if ($edit_item['type'] == 'collection') {

				// EDIT COLLECTION
				
				$this->CONF['access_menu']['model_param']['parent_id'] 	= $this->current_id;	
				$this->_current_parent_id 								= $edit_item['parent_id'];

				$edit_params = array(
					'fields' 		=> $edit_item
					, 'files'		=> $this->MODEL->get(array('parent_id'=> $id))
					);
				$this->load->view('file/edit_collection', $edit_params);
			
			} else {
				show_error('Missing or invalid edit item.');
			}

	}


	// ------------------------------------------------------------------------
	// DELETE

	public function delete($id=false) {

			$this->authentication->requirePermission('global_delete');

			$this->layout->setLayout('plain');
		
			if (($this->input->get($this->id_field) == $id) && ($this->input->get('DELETE') == 'DELETE') && ($id > 1)) {

				$del_item 	= $this->MODEL->first()->getById($id);
				
				$this->MODEL->delete($id);

				$this->session->set_flashdata('message', 'Item deleted.');

				$url = $this->admin_dir.$this->module;
				if ($del_item['type'] == 'file')  $url .= '/edit/' . $del_item['parent_id'];
				redirect($url, 'location');
				echo '<a href="'.SITEPATH.$url.'">Return to edit</a>';

			} else {
				show_error(__METHOD__ . ' Invalid params');
			}
	
	}

	// ------------------------------------------------------------------------
	
	public function picker() {
			
			if ($this->input->get_post('parent_id')) {
				$this->picker_parent_item = $this->MODEL->first()->getById($this->input->get_post('parent_id'));
			} else {
				$this->picker_parent_item = array();
			}
		
			return parent::picker();
		
	}


}