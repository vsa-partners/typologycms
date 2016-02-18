<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Typology CMS
 *
 * @author      VSA Partners / Louis D Walch (lwalch@vsapartners.com)
 * @link        http://www.vsapartners.com
 *
 */
 
 
require_once(APPPATH . 'controllers/page.php');

class Page_calendar extends Page {
	
	var	$has_module_model	= FALSE;
	var	$date				= array();
	var $id_field			= 'page_id';
	var $module				= 'page';

	function __construct() {
			
			parent::__construct();

			$this->CONF = $this->loadConfig('page');
			$this->load->model('page_model');
			$this->load->model('page_calendar_model');

//			$this->load->model('page_calendar_event_model');
			
//			$this->MODULE_CONF =  array_merge($this->MODULE_CONF, $this->loadConfig('page_calendar'));

			$input_year = $this->input->get('year') ? $this->input->get('year') : $this->input->post('year');
			$input_month = $this->input->get('month') ? $this->input->get('month') : $this->input->post('month');

			$this->date['year'] 	= (!empty($input_year)) ? $input_year : date('Y');
			$this->date['month'] 	= ((!empty($input_month)) ? $input_month : date('m'));
	
	}


	public function edit($id=null) {

			$this->current_id = $id;

			// Add action to wrapper title			
			$this->layout->appendTitle(ucwords(__FUNCTION__));

			if (is_null($id) || !is_numeric($id)) show_error('Invalid id.');
			
			if ($id > 0) $this->layout->appendTitle($id);

			// TODO: Add xml editing mode for super admin
						
			$item 						= $this->_edit_getItem($id, 'page_calendar_model');
			
            if (in_array($item['fields']['type'], array('page_calendar_event', 'mirror_calendar_event_source'))) {

				// Coming from template edit, redirect to correct template edit url
			
				$url = 'page_calendar/event/'.$item['fields'][$this->id_field];
				redirect($url, 'location');
				echo '<a href="'.SITEPATH.$this->zone.'/'.$url.'">Return to edit</a>';

			} else {

				$template = ($item['fields']['type'] == 'mirror_calendar') ? 'page_calendar/list_mirror' : 'page_calendar/list';

				// Show edit Page

				$item['fields']['children']	= $this->page_calendar_model->getByParentId($id, 'navigation');
				$this->load->view($template, $item);	
			
			}

	}


	public function event($id=null, $action=null, $event_id=null) {

			$this->current_id = $id;

			// Add action to wrapper title			
			$this->layout->appendTitle(ucwords(__FUNCTION__));

			if (is_null($id) || !is_numeric($id)) show_error('Invalid id.');
			
			if ($id > 0) $this->layout->appendTitle($id);

			// TODO: Add xml editing mode for super admin
	
			$item 			= $this->_edit_getItem($id, 'page_calendar_model');

			$template = ($item['fields']['type'] == 'mirror_calendar_event') ? 'page_calendar/event_mirror' : 'page_calendar/event';

			// Edit Page
			$this->load->view($template, $item);	

			return true;
	
	}


	// ------------------------------------------------------------------------
	// UPDATE

	public function update($id=null, $fields=null) {
	
			$this->current_id 	= $id;
			$save 				= true;
			$fields 			= is_array($fields) ? $fields : $this->input->post('fields', FALSE);

			$this->layout->setLayout('blank');

			// Add current user to update fields
			$fields['editor_id']		= $this->authentication->get('user_id');

			if ($this->current_id < 1) $this->authentication->requirePermission('global_create');

			if (!count($fields)) show_error('Could not save. Invalid or missing $fields.');

			if ($this->input->post('submit_publish')) {

				// Approve / publish page

				$update_page = $this->page_calendar_model->update_approvePublish($fields);
				$this->session->set_flashdata('message', 'Your '.$this->module.' has been saved and queued to be published.');

			} else if ($this->input->post('submit_request')) {

				// Request for approval

				$update_page = $this->page_calendar_model->update_requestApproval($fields);
				$this->session->set_flashdata('message', 'Your '.$this->module.' has been saved and approval has been requested.');

			} else if ($this->input->post('submit_decline')) {

				$this->session->set_flashdata('message', 'Your '.$this->module.' has been saved.');
				$update_page = $this->page_calendar_model->update_decline($fields);			
				
			} else if ($this->input->post('submit_draft')) {
				
				// Save draft

				$update_page = $this->page_calendar_model->update_draft($fields);
				$this->session->set_flashdata('message', 'Your '.$this->module.' has been saved.');
				
			} else {
				
				show_error('Unknown update method. If you continue to see this error please contact your system administrator.');
			
			}
			
            if (in_array($update_page['type'], array('page_calendar_event', 'mirror_calendar_event_source'))) {
				$url_page = 'event';
			} else {
				$url_page = 'edit';
			}

			// Successful, return to edit page.
			$url = 'page_calendar/'.$url_page.'/'.$update_page[$this->id_field];
			redirect($url, 'location');
			echo '<a href="'.SITEPATH.$this->zone.'/'.$url.'">Return to edit</a>';

	}





}