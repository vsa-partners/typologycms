<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Typology CMS
 *
 * @author      VSA Partners / Louis D Walch (lwalch@vsapartners.com)
 * @link        http://www.vsapartners.com
 *
 */
 
 
require_once(APPPATH . 'controllers/manage.php');

class Util_mysql extends Manage {

	var	$has_module_config	= TRUE;

	var $module_path 		= 'util_mysql';

	var $current_table;
	var $current_action;
	var $current_id;
	var $tables;


	// ------------------------------------------------------------------------

	function __construct() {
			parent::__construct();

			// Add controller to wrapper title			
			$this->layout->appendTitle('Util : Database');

			$this->authentication->requirePermission('mysql');

			// Set internal navigation variables
			$this->current_table	= $this->uri->segment(2);
			$this->current_action	= $this->uri->segment(3);
			$this->current_id		= $this->uri->segment(4);
			$this->tables			= $this->db->list_tables();

	}


	// ------------------------------------------------------------------------

	function _remap() {

		if ($this->current_table) {
		
			if ($this->current_table == 'query') {
				return $this->_query();		
			} else if (!in_array($this->current_table, $this->tables)) {
				// Make sure it's a valid table
				show_error('Invalid Table ('.$this->current_table.')');
			}

			$this->load->model($this->current_table.'_model', 'dynamic_model');

			switch ($this->current_action) {
				case 'edit':		$this->_edit();		break;
				case 'delete':		$this->_delete();	break;
				case 'optimize':	$this->_optimize();	break;
				case 'repair':		$this->_repair();	break;
				case 'truncate':	$this->_truncate();	break;
				default:			$this->_list();		break;
			}
		
		} else {

			// Nothing, show default admin index
			$this->index();
		}
		
	}	
	
	
    // ------------------------------------------------------------------------
	// TABLE ACTIONS
	
	private function _list() {
	
			$rows = $this->dynamic_model->get(array(), 'basic');
			
			$this->id_field = $this->dynamic_model->id_field;
			
			$this->load->view('util_mysql/list', array('rows'=>$rows));			
				
	}
	private function _optimize() {
			
			$this->load->dbutil();
			$this->dbutil->optimize_table($this->current_table);

			$this->messages[] = 'Table optimized.';
			$this->_list();		
				
	}
	private function _repair() {
			
			$this->load->dbutil();
			$this->dbutil->repair_table($this->current_table);

			$this->messages[] = 'Table repaired.';
			$this->_list();		
				
	}
	private function _truncate() {
			
			$this->db->truncate($this->current_table);
			$this->messages[] = 'Table emptied.';
			$this->_list();		
				
	}

    // ------------------------------------------------------------------------
	// QUERY

	private function _query() {

			$this->load->view('util_mysql/query');			
		
				
	}

    // ------------------------------------------------------------------------
	// ROW ACTIONS

	private function _delete() {
			
			$this->dynamic_model->delete($this->current_id);

			$this->messages[] = 'Database item deleted.';
			$this->_list();		
				
	}
	private function _edit() {
	
			// Need to save?
			if ($fields = $this->input->post('fields')) {
				
				if ($this->current_id > 0) {

					// Update
					$where = $this->dynamic_model->id_field.'=\''.$fields[$this->dynamic_model->id_field].'\'';
					$query = $this->db->update_string($this->current_table, $fields, $where); 

					if ($this->db->simple_query($query)) {
						$this->messages[] = 'Database changes saved.';
					} else {
					
						show_error('Error excuting query: '.$query);
					
					}

				} else {

					// Insert

					// Remove id field
					unset($fields[$this->dynamic_model->id_field]);

					$query = $this->db->insert_string($this->current_table, $fields); 

					if ($this->db->simple_query($query)) {
						$insert_id = $this->db->insert_id();
						$this->session->set_flashdata('message', 'Database changes saved.');
						redirect('/manage/util_mysql/'.$this->current_table.'/edit/'.$insert_id);
					}

				}

			}
			
			$item 	= $this->dynamic_model->first()->getById($this->current_id);
			$fields = $this->db->field_data($this->current_table);
			
			$this->load->view('util_mysql/edit', array('item'=>$item, 'fields'=>$fields));	
	
	}
	
	
    // ------------------------------------------------------------------------

	
	public function displayAccessMenu() {
		return $this->load->view('util_mysql/menu_access', NULL, TRUE);
	}	

}