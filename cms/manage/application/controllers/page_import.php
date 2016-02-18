<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Typology CMS
 *
 * @author      VSA Partners / Louis D Walch (lwalch@vsapartners.com)
 * @link        http://www.vsapartners.com
 *
 */
 
 
require_once(APPPATH . 'controllers/manage.php');

class Page_import extends Manage {

	var	$has_module_config	= TRUE;
	var	$has_module_model	= FALSE;

	var $require_login 		= TRUE;
	
	var $FILE_CONF;
	
	var $import_result		= array();
	var $update_pages		= array();
	var $update_spreadsheet	= array();
	
	var $upload_path		= false;
	
	
	// ------------------------------------------------------------------------

	function __construct() {
			parent::__construct();
			
			$this->FILE_CONF = $this->loadConfig('file');	
			
			ini_set('auto_detect_line_endings', 1);

			$this->upload_path = DOCROOT . zonepath($this->FILE_CONF['file_directory'], 'local') . '/' . $this->FILE_CONF['temp_folder'] . '/';

			// Required models
			$this->load->model('page_model');
			$this->load->model('page_attributejoin_model');
			$this->load->model('page_attributevalue_model');			
			$this->load->model('template_model');

//error_reporting(E_ALL);
		
	}


	// ------------------------------------------------------------------------

	function index() {

		// Load available templates
		
		$this->load->view('page_import/index', array(
			'templates' => $this->template_model->getForDropdown()
			));					

	}

	// ------------------------------------------------------------------------

	function confirm() {

		if (empty($_FILES['userfile']['tmp_name'])) {
			show_error('You did not select a file to upload.');
		}

		// UPLOAD FILE
		
		$config = array(
			'upload_path' 		=> $this->upload_path
			, 'allowed_types'	=> 'xls'
			, 'encrypt_name'	=> TRUE
			);
		$this->load->library('upload', $config);	
		if ($this->upload->do_upload()) {
			$tmp_file 			= $this->upload->data();
		} else {
			$error 				= array('error' => $this->upload->display_errors());
			show_error($error['error']);
		}

		$settings				= $this->input->post('settings');
		
		// VALIDATE FILE
		
		$this->_processRows($tmp_file['full_path'], $settings);

		$fields		= array(
			'tmp_name'		=> $tmp_file['file_name']
			, 'results'		=> $this->import_result
			, 'settings'	=> $settings
			);
		$this->load->view('page_import/confirm', $fields);
	
	}
	
	function execute() {
		
		$import_file 	= $this->upload_path . $this->input->post('tmp_name');
		$updated		= array();
		
		if (!is_readable($import_file)) {
			show_error('You did not select a file to upload.');
		}

		$settings				= $this->input->post('settings');

		$this->_processRows($import_file, $settings);

		$i = 0;

		for($i = 0; $i < count($this->update_pages); ++$i) {

			$fields		= $this->update_pages[$i];

			$page 		= $this->page_model->update_draft($fields);
			$updated[] 	= $page;

			$this->update_spreadsheet[$i+1][0] = $page['page_id'];
		
		}

		// Delete the uploaded file
		unlink($import_file);
		
		$this->load->view('page_import/execute', array('results'=> $updated, 'updated_rows'=>$this->update_spreadsheet));
	
	}



	private function _processRows($file_path, $settings=array()) {	

		$file_data 			= $this->_readExcel($file_path);
		
		$lang 				= 'EN';
		$lang_path			= '/lang_'.$lang;

		// VALIDATE REQUIRED METHOD

		if ($settings['method'] == 'create') {

			if (empty($settings['parent_id'])) show_error('I need to know where to put created pages, please select a parent folder.');
			if (empty($settings['template_id'])) show_error('I need to know what kind of pages these are, please select a template.');

			$create_template 	= $this->template_model->first()->getById($settings['template_id']);
			$create_page		= $this->page_model->getEmptyItem(array(
				'template_id'	=> $settings['template_id']
				, 'parent_id'	=> $settings['parent_id']
				, 'type'		=> 'page'
				));	

		}

		$method			= $settings['method'];

		// VALIDATE FILE DATA,  ARE THERE ROWS?		
		if (count($file_data) < 2) show_error('Invalid data in file.');
		
		$columns		= $file_data[0];
		$columns_names	= array_flip($columns);
		$rows			= $file_data[1];
		
		foreach ($rows as $row_num => $row) {

			$page_id 			= false;
			$update_item		= array();
			$spreadsheet_item	= array();

			// Blank Page ID, will be updated later. It needs to always be the first column
			$spreadsheet_item[] = 'page_id';

			// ------------------------------------------------------
			// UPDATE EXISTING PAGE

			if ($method	== 'update') {

				if (!array_key_exists($columns_names['page_id'], $row)) {
					$this->addProcessRecord($row_num, '', 'SKIP - No page id found');
					continue;
				}

				$page_id 				= $row[$columns_names['page_id']];
				$update_item['page_id']	= $page_id;
				
				if (empty($page_id)) {
					$this->addProcessRecord($row_num, '', 'SKIP - No page id found');
					continue;
				}

				$page_data	= $this->page_model->first()->getById($page_id);
				
				if (!$page_data || !count($page_data) || ($page_data['page_id'] < 0)) {
					$this->addProcessRecord($row_num, $page_id, 'SKIP - No page found in CMS');
					continue;
				}
				
				if (empty($page_data['content'])) {
					$this->addProcessRecord($row_num, $page_id, 'SKIP - Page in CMS does not have any content to update');
					continue;
				}

				$page_xml = $page_data['content'];

			}

			// ------------------------------------------------------
			// CREATE NEW PAGE

			else if ($method  == 'create') {

				if (!array_key_exists('title', $columns_names)) {
					$this->addProcessRecord($row_num, '', 'SKIP - No title column found');
					continue;
				}

				$page_xml 					= $create_template['template_xml'];
				$page_id					= -1;
				$update_item				= $create_page;
				$update_item['module']		= 'page';

			}

			// ------------------------------------------------------

			else {
				show_error('Oops, something is wrong. Invalid method.');
			}

			// ------------------------------------------------------

			// No matter what, let's remove the page_id row
			if (array_key_exists('page_id', $columns_names)) unset($row[$columns_names['page_id']]);	

			$update			= false;
		
			$content_object	= simplexml_load_string('<?xml version="1.0" encoding="utf-8"?>' . $page_xml, 'SimpleXMLElement', LIBXML_NOCDATA);

			foreach ($row as $col_num => $col_value) {
				
				$col_name		= $columns[$col_num];

				$col_value		= utf8_encode($col_value);
				//$col_value		= html_entity_decode($col_value, ENT_QUOTES, 'UTF-8');
				$col_value		= mb_convert_encoding($col_value,'UTF-8','UTF-8');
				
				if ($col_name == 'title') {
					
					// Special case for updating the page title
					$update 				= true;
					$update_item['title'] 	= $col_value;

					$this->addProcessRecord($row_num, $page_id, '<span style="color: green;">Update "title"</span>');

				} else if (strpos($col_name, 'attribute') === 0) {

					// Updating attributes

					if (!array_key_exists('attribute_values', $update_item)) $update_item['attribute_values'] = array();

					$attribute_parts = explode('/', $col_name);

					$update_item['attribute_values'][$attribute_parts[1]] = explode(',', $col_value);

					$update 			= true;

					$this->addProcessRecord($row_num, $page_id, '<span style="color: green;">Update attribute "'.$attribute_parts[1].'"</span>');

				} else {
				
					// Updating content
					
					$path	= $col_name;
				
					$cell 	= $content_object->xpath($path);
					
					// Skip this cell if it doesn't have a title
					if (!array_key_exists($col_num, $columns)) {
						continue;
					}
					
					if (!$cell || !count($cell)) {
						$this->addProcessRecord($row_num, $page_id, 'No match for "'.$col_name.'"');
						continue;
					}

					if ($cell[0]->attributes()->lang == 'true') {
						$cell 	= $content_object->xpath($path.$lang_path);
					}
					
					// All good, update the XML already
					$update 		= true;
					$cell[0][0]		= $col_value;

					$this->addProcessRecord($row_num, $page_id, '<span style="color: green;">Update "'.$col_name.'"</span>');
				
				}

				$spreadsheet_item[] = $col_value;
			
			}			

			if ($update) {

				$output = $content_object->asXML();

				// Strip out any <?xml stuff
				$output = preg_replace('/\<\?xml.+\?\>/', '', $output, 1);

				$update_item['content'] 	= ($output);

				$this->update_spreadsheet[]	= $spreadsheet_item;
				$this->update_pages[] 		= $update_item;
			
			}
		
		}		
	
	}


	private function _readExcel($file) {

		require_once DOCROOT .'cms/manage/application/libraries/excel_read/reader.php';
		$data = new Spreadsheet_Excel_Reader();

		if (!$data) show_error('The file you uploaded could not be processed');

		$data->setOutputEncoding('CP1251');
		$data->read($file);

		if (!$data->sheets[0]['numRows']) show_error('The file you uploaded appears to be empty');

		$rows			= $data->sheets[0]['cells'];
		$columns		= array_shift($rows);
		$columns_names	= array_flip($columns);

		// Make sure we have an id column, required for update and will be updated when creating new pages
		if (!array_key_exists('page_id', $columns_names)) show_error('Uploaded spreadsheet does not contain "page_id" column');
		
		return array($columns, $rows);
	
	}


	private function addProcessRecord($row, $page_id, $result) {
	
		if (!array_key_exists($row, $this->import_result)) {
			$this->import_result[$row] = array(
				'page_id' 	=> $page_id
				, 'result'	=> array()
				);
		}
	
		$this->import_result[$row]['result'][] = $result;

	}

	// ------------------------------------------------------------------------



}