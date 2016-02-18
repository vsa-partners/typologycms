<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Typology CMS
 *
 * @author      VSA Partners / Louis D Walch (lwalch@vsapartners.com)
 * @link        http://www.vsapartners.com
 *
 */
 
 
require_once(APPPATH . 'controllers/manage.php');

class Page extends Manage {

	var	$has_module_config	= TRUE;
	var	$has_module_model	= TRUE;

	var $hide_redirects		= 0;
	
	// ------------------------------------------------------------------------

	function __construct() {
			parent::__construct();

 			$this->load->library('admin/xml_builder');

 			$this->load->model('page_attributegroup_model');
 			$this->load->model('page_attributejoin_model');
 			$this->load->model('page_attributevalue_model');

			if (!empty($this->ADMIN_CONF['google_api_key'])) 
				$this->layout->asset->add('js', $this->ADMIN_CONF['google_api_js'].$this->ADMIN_CONF['google_api_key'], 'google', false);
				
			$this->hide_redirects = $this->session->userdata('am_'.$this->module.'_hideredicts') ? $this->session->userdata('am_'.$this->module.'_hideredicts') : $this->hide_redirects;

	}


	// ------------------------------------------------------------------------

	function index() {
			
			if ($this->input->get('collapse'))  $this->menu_builder->resetActiveItems();			
			
			if (($action = $this->input->get('toggle_redirects'))) {
				if ($action == 'hide') {
					$this->session->set_userdata('am_'.$this->module.'_hideredicts', 1);			
					$this->hide_redirects = 1;
				} else {
					$this->session->set_userdata('am_'.$this->module.'_hideredicts', 0);
					$this->hide_redirects = 0;
				}			
			}
	
			$this->load->model('publish_queue_model');
			
			if ($this->ADMIN_CONF['publish']['publish_method'] == 'local_table') {
				$pending_pages	= false;
				$queue_jobs		= false;
				$view			= 'page/index_updatesonly';
			} else {
				$pending_pages 	= $this->page_model->getPending(9999, 'basic');
				$queue_jobs		= $this->publish_queue_model->getJobs($this->module);
				$view			= 'page/index';
			} 

			$updated_pages	= $this->page_model->getUpdated(100, 'basic');


			$view_data = array(
				'pending_pages' 	=> $pending_pages
				, 'updated_pages' 	=> $updated_pages
				, 'queue_jobs' 		=> $queue_jobs
				);

			$this->load->view($view, $view_data);

	}


	// ------------------------------------------------------------------------
	// EDIT

	public function edit($id=null, $mode=null) {

			// Add action to wrapper title			
			$this->layout->appendTitle(ucwords(__FUNCTION__));

			if (is_null($id) || !is_numeric($id)) show_error('Invalid id.');
			
			if ($id > 0) $this->layout->appendTitle($id);

			// TODO: Add xml editing mode for super admin
	
			$item 			= $this->_edit_getItem($id);
			
			if ($mode == 'xml') {
				$this->authentication->requirePermission('page_edit_xml');
				$edit_view	= 'page/edit/xml';
			} else {
				$edit_view	= (file_exists(APPPATH.'views/page/edit/edit_'.$item['fields']['type'].EXT)) ? 'page/edit/edit_'.$item['fields']['type'] : 'page/edit/edit';
			}

			// If this is a section, add it to the list of active menu items
			if ($item['fields']['type'] == 'section') $this->menu_builder->addActiveItem($id);

			// Edit Page
			$this->load->view($edit_view, $item);	

			return true;

	}

	public function edit_sort($id=null) {

			// Add action to wrapper title			
			$this->layout->appendTitle(ucwords(__FUNCTION__));
			
			if ($id < 1) show_error('Can not edit sort, invalid item returned.');
	
			$item 		= $this->_edit_getItem($id);

			// Get Child Pages
			$children 	= $this->page_model->getByParentId($id, 'navigation');
			$item['fields']['children'] = count($children) ? $children : array();					


			$this->load->view('page/edit/sort', $item);	

			return true;

	}

	protected function _edit_getItem($id=null, $model='page_model') {

			if (!$id) show_error(__METHOD__ . ' Invalid or missing item id.');
			
			$edit_params 		= array();
			
			// Save Info
			$this->current_id 	= $id;

			// TODO: Check user edit permissions ?

			if (($id < 0) && $this->input->get_post('dupe_id')) {

				// Duplicate an exiting page

				$edit_params	= $this->_edit_getItem($this->input->get_post('dupe_id'), $model);

				$edit_params['fields'][$this->id_field] = -1;
				$edit_params['fields']['title'] 		= '';
				$edit_params['fields']['status'] 		= 1;
				$edit_params['fields']['file_name'] 	= '';
				$edit_params['fields']['path'] 			= '';

			} else {
			
				// Edit existing page
				$page_item 					= $this->{$model}->first()->related(array('editor', 'approver'))->getById($id);
				$edit_xml					= $this->ADMIN_CONF['xml_empty'];

				if (empty($page_item[$this->id_field])) show_error('Could not edit. Invalid item returned.');

				$edit_params['fields'] 		= $page_item;

				// Add Edit XML
				if (!empty($page_item['template_id'])) {
					// Process page xml w/ template xml to make edit page nodes
					$edit_xml 	= $this->xml_builder->buildEditXML($page_item['template_xml'], $edit_params['fields']['content']);
				}

				if (!empty($page_item['template_options']['child_edit_style']) && ($page_item['template_options']['child_edit_style'] == 'list')) {
					$children 		= $this->{$model}->getByParentId($id, 'navigation');
					$edit_params['fields']['children'] = count($children) ? $children : array();					
				}

				if ($page_item['type'] == 'page_database') {
					$children 		= $this->{$model}->getByParentId($id, 'navigation');
					$edit_params['fields']['children'] = count($children) ? $children : array();					
				}
				
				// Add edit xml to page item to be passed into xsl
				$edit_params['fields']['edit_xml'] = $edit_xml;
				
			}

			// Set path, the menu will need this
			if (!empty($edit_params['fields']['path'])) $this->current_path = $edit_params['fields']['path'];

			return $edit_params;

	}


	// ------------------------------------------------------------------------
	// UPDATE

	public function update($id=null, $fields=null) {
	
			$this->current_id 	= $id;
			$save 				= true;
			$insert_module		= false;
			$fields 			= is_array($fields) ? $fields : $this->input->post('fields');

			$this->layout->setLayout('blank');

			// Add current user to update fields
			$fields['editor_id']		= $this->authentication->get('user_id');

			if ($this->current_id < 1)
				$this->authentication->requirePermission('global_create');

			if (!count($fields)) show_error('Could not save. Invalid or missing $fields.');

			if ($this->input->post('submit_publish')) {

				// Approve / publish page

				$update_page = $this->page_model->update_approvePublish($fields);
				$this->session->set_flashdata('message', 'Your '.$this->module.' has been saved and queued to be published.');

			} else if ($this->input->post('submit_request')) {

				// Request for approval

				$update_page = $this->page_model->update_requestApproval($fields);
				$this->session->set_flashdata('message', 'Your '.$this->module.' has been saved and approval has been requested.');

			} else if ($this->input->post('submit_decline')) {

				$this->session->set_flashdata('message', 'Your '.$this->module.' has been saved.');
				$update_page = $this->page_model->update_decline($fields);			
				
			} else if ($this->input->post('submit_draft')) {
				
				// Save draft

				$update_page = $this->page_model->update_draft($fields);
				$this->session->set_flashdata('message', 'Your '.$this->module.' has been saved.');
				
			} else {

			    // Unknown update method. User was probably logged out during editing, let's just go back to the edit page.

			    $update_page = array($this->id_field => $id);
				//$this->session->set_flashdata('message', 'Unknown update method. If you continue to see this error please contact your system administrator.');
				
				//show_error('Unknown update method. If you continue to see this error please contact your system administrator.');
			
			}

			// Successful, return to edit page.
			$url = $this->module.'/edit/'.$update_page[$this->id_field];
			redirect($url, 'location');
			echo '<a href="'.SITEPATH.$this->zone.'/'.$url.'">Return to edit</a>';

	}

	public function update_sort($id=null) {
	
			$fields 			= $this->input->post('fields');

			$this->load->model('publish_queue_model');

			if (!count($fields)) show_error('Could not save. Invalid or missing $fields.');
	
			$i=1;
	
			foreach ($fields['children'] as $key => $val) {

				$update = array('page_id'=>$key, 'sort'=>$i);
				$this->page_model->update($update);
				
				if ($val == '20') {
					// Only published pages
					$this->publish_queue_model->sort($this->module, $key, $update);
				}
				
				$i++;
				
			}

			// Successful, return to edit page.
			$url = $this->admin_dir.$this->module.'/edit/'.$id;
			redirect($url, 'location');
			echo '<a href="'.SITEPATH.$url.'">Return to edit</a>';
			
	}


	// ------------------------------------------------------------------------
	// VERSIONS

	public function versions($page_id=null, $version_id=null, $revert=null) {

			if (empty($page_id) || ($page_id < 0)) show_error('Invalid or missing id.');

			$this->current_id = $page_id;


			// Add action to wrapper title			
			$this->layout->appendTitle(ucwords(__FUNCTION__));			

			$this->load->model('page_versions_model');

			if (!is_null($revert)) {
			
				if ($this->page_versions_model->revertTo($version_id)) {

					$this->session->set_flashdata('message', 'Successfully reverted page to version #'.$version_id);

					// Successful, return to edit page.
					$url = $this->admin_dir.$this->module.'/edit/'.$page_id;
					redirect($url, 'location');
					echo '<a href="'.SITEPATH.$url.'">Return to edit</a>';
									
				}
			
			} else if (!is_null($version_id)) {
			
				// Viewing a specific version number

				$this->output->enable_profiler(FALSE);

				$this->layout->setLayout('plain');	
				$this->layout->setBodyClass('popup');	

				$version  	= $this->page_versions_model->first()->getById($version_id);
				
				$this->load->view('page/version', array('version'=>$version));
			
			} else {
			
				// Viewing version list

				$this->load->model('publish_queue_model');
				
				$jobs 		= $this->publish_queue_model->getByModule($this->module, $page_id);
				$versions  	= $this->page_versions_model->related('editor')->getByPageId($page_id);
				
				$this->load->view('page/version_list', array('versions'=>$versions, 'jobs'=>$jobs));
			
			}

	}


	// ------------------------------------------------------------------------
	// CREATE

	public function create($parent=null) {
	
			$this->authentication->requirePermission('global_create');

			// Add action to wrapper title			
			$this->layout->appendTitle(ucwords(__FUNCTION__));			
	
			if (is_null($parent) || $parent < 1) show_error('Invalid parent specified');
				
			// Get parent item
			$this->current_id 	= $parent;
			$parent_item 		= $this->page_model->first()->getById($parent);

			if (empty($parent_item['type'])) show_error('Invalid parent specified.');

			$properties 		= $this->CONF['items'][$parent_item['type']];
			
			// Can this item type have children?
			if (!count($properties['allowed_children']))
				show_error('Can not create page. The parent type you specified is not allowed to have children.');

			// Load available templates
			$this->load->model('template_model');
			
			// Get list of available child template. Either a restricted list or those specified in CMS.
			$templates 	= $this->template_model->getForDropdown($this->create_getTemplateQuery($parent_item));

			// If there is only one template available, no need for dropdown.	
			if (count($templates) == 2){
				$templates = array_slice($templates, 1, 1, TRUE);
			}
			
			
			// Get list of available child types. Either a restricted list or those specified in CMS.
			if (!empty($parent_item['template_options']['child_type'])) {
				$allowed 		= array($parent_item['template_options']['child_type']=>$this->CONF['items'][$parent_item['template_options']['child_type']]['name']);
			} else {
				$allowed = array();
				foreach ($properties['allowed_children'] as $allow) {
					$allowed[$allow] = $this->CONF['items'][$allow]['name'];
				}
			}

			asort($allowed, SORT_STRING);

			// Page type should always be first
			if (array_key_exists('page', $allowed)) {
				$_tmp = array('page'=>$allowed['page']);
				unset($allowed['page']);
				$allowed = array_merge($_tmp, $allowed);
			}
			
			$data = array(
				'menu' 				=> 'page'
				, 'parent'	 		=> $parent_item
				, 'allowed'			=> $allowed
				, 'templates'		=> $templates
				, 'copy_template'	=> $this->input->get_post('template_id')
				, 'copy_type'		=> $this->input->get_post('type')
				);
			
			$this->load->view('page/create', $data);	
			
	}

	public function create_getTemplateQuery($parent_item=null) {

			$return = array();

			if ( !empty($parent_item['template_options']['child_template']) &&  is_array($parent_item['template_options']['child_template']) ) {

				$child_template = $parent_item['template_options']['child_template'];
				$child_template = array_filter($child_template);
				if (count($child_template)) $return = array('template_id'=>$child_template);

			}

			return $return;

	}	
	
	// ------------------------------------------------------------------------
	// MOVE 
	
	public function move($id=false) {
		
			
			if (($this->input->get('move_'.$this->id_field) != $id) || !$this->input->get('move_parent_id')) 
				show_error(__METHOD__ . ' Invalid params');

			$parent 	= $this->page_model->first()->getById($this->input->get('move_parent_id'), 'navigation');
			$item		= $this->page_model->first()->getById($id, 'navigation');

			$new_path 	= (($parent['path'] != '/') ? $parent['path'] : '') . '/' . $item['file_name'];

			$itemfields = array(
				$this->id_field		=> $id
				, 'parent_id'		=> $this->input->get('move_parent_id')
				, 'type'			=> $this->input->get('move_type')
				, 'status'			=> $this->input->get('move_status')
				, 'path'			=> $new_path
				);
				
			$this->page_model->move($itemfields);

			$this->session->set_flashdata('message', 'Your '.$this->module.' has been moved.');
			
			// Successful, return to edit page.
			$url = $this->admin_dir.$this->module.'/edit/'.$id;
			redirect($url, 'location');
			echo '<a href="'.SITEPATH.$url.'">Return</a>';
	
	}


	// ------------------------------------------------------------------------
	// DELETE 
	
	public function delete($id=false) {
		
			$this->authentication->requirePermission('global_delete');
		
			if (($this->input->get($this->id_field) != $id) || ($this->input->get('DELETE') != 'DELETE') || !$id)
				show_error(__METHOD__ . ' Invalid params');
				
			$this->page_model->delete($id);

			// Remove item from list of active menu items - just in case
			$this->menu_builder->removeActiveItem($id);
		
			// Add activity record
			$this->activity->log($this->module, $id, 'Delete Page');
			$this->session->set_flashdata('message', 'Item deleted.');
			
			// Successful, return to admin.
			$url = $this->admin_dir.$this->module;
			redirect($url, 'location');
			echo '<a href="'.SITEPATH.$url.'">Return</a>';
		
	
	}

	// ------------------------------------------------------------------------
	// UNPUBLISH 
	
	public function unpublish($id=false) {
	
			if ($this->page_model->update_unpublish($id)) {
				$this->session->set_flashdata('message', 'Page unpublished. <br/>Live version and any pending queues have been removed.');
			}
			
			// Successful, return to edit page.
			$url = $this->admin_dir.$this->module.'/edit/'.$id;
			redirect($url, 'location');
			echo '<a href="'.SITEPATH.$url.'">Return to edit</a>';

	}


	// ------------------------------------------------------------------------
	// ACTIVITY

	public function activity($id=null) {
			if (empty($id)) show_error('Invalid or missing id.');
			
			$this->current_id = $id;			
			
			$edit_params = array(
				'items'		=> $this->activity->get(array('module_id' => 'page', 'module_id' => $id), NULL, TRUE)
				, 'back'	=> TRUE
				);

			// Display Activity
			$this->load->view('activity/list', $edit_params);

	}


	// ------------------------------------------------------------------------
	// PUBLISH MULTIPLE

	public function publishPages($mode=null) {

			$this->authentication->requirePermission('global_publish');
	
			switch($mode) {
				/*
				This query looks wrong. It should look for all unpublished pages which have proper status. Think this will also get draft.
				case 'all':
					$pages = $this->page_model->get(array('published'=>0));
					break;
				*/
				case 'id':
					if (!$this->input->get_post('id')) show_error('Unknown publish request.');
					$pages = $this->page_model->getById($this->input->get_post('id'));
					break;
				default:
					show_error('Unknown publish request.');
					break;
			}		

			$data 	= '';
			$unset 	= array('template_title', 'template_file_name', 'template_options', 'template_attributes', 'template_xml', 'attributes');

			foreach ($pages as $page) {

				// Reformat attribute values for publish
				if (!empty($page['attribute_values']) && count($page['attribute_values'])) {

					$attribute_values = array();

					foreach ($page['attribute_values'] as $group => $values) {

						$group_name 	= ltrim($group, 'group_');
						$group_values 	= array();

						foreach ($values as $v) { $group_values[] = $v['page_attributevalue_id']; }

						$attribute_values[$group_name] = $group_values;

					}

					$page['attribute_values'] = $attribute_values;

				}

				// Let's clean up a little before sending this to model
				foreach ($unset as $field) {
					if (isset($page[$field])) unset($page[$field]);					
				}

				$this->page_model->update_approvePublish($page);

				// Add activity record
				$this->activity->log($this->module, $page[$this->id_field], 'Publish');

				$data .= '<br/>Page '.$page[$this->id_field] .' published.';

			}

			$this->layout->show($data);

	}

	
	// ------------------------------------------------------------------------
	// AJAX METHODS

	function _ajax_treeNavChildren($menu=null) {
	
		$id 	= $this->input->get_post('id');
		
		if (empty($id)) show_error(__METHOD__ . ' Invalid or missing parameters.');

		echo $this->menu_builder->display(array('id' => $id), $menu, TRUE);

	}
	
	function _ajax_checkUniquePath() {
	
			$parent_path 	= $this->input->post('parent_path');
			$file_name 		= strtolower(preg_replace("/[^a-z\-_\d]/i", "", underscore($this->input->post('file_name'))));

			$path			= $parent_path . $file_name;
			
			if (empty($path)) show_error(__METHOD__ . ' Invalid or missing parameters.');
	
			$item  = $this->page_model->get(array('path' => $path), 'basic');
			
			if (!empty($item[0][$this->id_field]) && ($item[0][$this->id_field] != $this->input->post('id'))) {
				$status = 'FALSE';			
			} else {
				$status = 'TRUE';
			}
			
			echo json_encode(array(
				'unique' 		=> $status
				, 'path'		=> $path
				));
				

	}

	function _ajax_toggleStatus() {
	
			$id 		= $this->input->get_post('id');
			$publish 	= $this->input->get_post('publish');
			
			if (empty($id) || ($publish != 1)) show_error(__METHOD__ . ' Invalid or missing parameters.');
	
			$update  = $this->page_model->publish($id);

	}

	function _ajax_dupeNode() {
		
			$item_path 		= $this->input->get_post('item_path');
			$parent_path	= $this->input->get_post('parent_path');
			$parent_id		= $this->input->get_post('parent_id');
			$id 			= $this->input->get_post('id');
			$sortkey		= $this->input->get_post('sortkey');
			$page_item 		= $this->page_model->first()->getById($id);

			if (!empty($page_item['template_id'])) {

				$dupe_xml 	= $this->xml_builder->buildDupeXML($page_item['template_xml'], $item_path);
				
				// Render XSL
				$xml = array(
					'dupe_xml'		=> $dupe_xml
					, 'sortkey'		=> $sortkey
					, 'parent_path'	=> $parent_path
					, 'parent_id'	=> $parent_id
					);
					
				
				$xsl =  '/application/xsl/template_nodes/dupe_node.xsl';

				$this->layout->show($this->xsl_transform->transform($xsl, $xml));
			
			}
	}

	function _ajax_recallPublished() {
	
			$this->authentication->requirePermission('global_publish');
	
			$id 			= $this->input->get_post('id');
			
			if (!$id) { echo 'ERROR'; return; }

			$this->load->model('page_published_model');
			$this->page_published_model->delete($id);

			$update_fields 	= array(
				$this->id_field		=> $id
				, 'published' 		=> 0
				, 'publish_date'	=> '0000-00-00 00:00:00'
				);
			$this->page_model->update($update_fields);
			
			// Add activity record
			$this->activity->log($this->module, $id, 'Recall Published');

			echo 'SUCCESS';

	}


	function _ajax_addAttributeValue() {
	
			$value  = $this->input->get_post('value');
			$group  = $this->input->get_post('group');
			
			if (!$value) { echo 'ERROR'; return; }
			if (!$group) { echo 'ERROR'; return; }

			echo $this->page_attributevalue_model->addAttributeValue($group, $value);

	}	
	
	// ------------------------------------------------------------------------
	// UTILS
	
	function util($name=false) {
		
			if (method_exists($this,'_util_'.$name)) {
				$output = call_user_func(array($this, '_util_'.$name));
			} else {
				show_error(__METHOD__ . ' Invalid or missing utility name.');
			}
			
			// WRAPPER?
			$this->layout->setLayout('plain');	
			$this->layout->setBodyClass('popup');	
			$this->layout->show($output);	

	}

	private function _util_itempicker() { 
		
			$module = $this->input->get('module');
			
			return $this->menu_builder->display(array(
							'module'		=> !empty($module) ? $module : $this->module
							, 'view' 		=> $this->ADMIN_CONF['views']['menus']['item_picker']
							, 'mode'		=> 'admin'
							, 'params'		=> array(
								'type'		=> 'picker'
								, 'type' 	=> $this->input->get('type')
								)
							)
						);
		
	}

	private function _util_filepicker() { 
	
			$params = array('hello'=>'hi');

			$this->load->library('File_picker', $params);
			
			return $this->file_picker->browse();
	
	}


	// ------------------------------------------------------------------------

	public function allowed_children($type) {
	
		if (count($this->CONF['items'][$type]['allowedChildren'])) { 
			return true;
		} else {
			return false;
		}

	}

	// Move to helpers ?
	public function display_status_icon($status) {
		return $this->CONF['status'][$status]['icon'];
	}


	// ------------------------------------------------------------------------
	// REPORTS
	
	function report($name=false, $count=100) {
	
			$view = null;
		
			switch ($name) {
				
				case 'draft':
				
					$output	= array('data' => $this->page_model->getDrafts());
					$view 	= 'page/report_unpublished';
				
					break;

				case 'activity':

					$output	= array('data' => $this->page_model->getUpdated($count));
					$view 	= 'page/report_activity';
				
					break;

				case 'pending':

					$count	= 9999;

					$output	= array('data' => $this->page_model->getPending($count));
					$view 	= 'page/report_pending';
				
					break;


					
				default:
					
					$output = 'Reports';
					break;
			}

			
			// WRAPPER?
			if (!is_null($view)) {
				$this->load->view($view, $output);	
			} else {
				$this->layout->show($output);	
			}
	}






	function mirrorEntireSection() {
		$this->_mirrorEntireSection($this->input->get('to_parent'), $this->input->get('from_parent'));
	}
	function _mirrorEntireSection($to_parent=false, $from_parent=false) {

		if (!$to_parent) show_error('Missing from_parent');
		if (!$from_parent) show_error('Missing to_parent');

		$sources 		= $this->page_model->getByParentId($from_parent);

		// Get all the contents of the source folder
		foreach ($sources as $source) {

			if ($source['type'] == 'section') {
				CI()->page_model->update(array('page_id'=>$source['page_id'], 'type'=>'mirror_section_source' ));
				$source['type'] = 'mirror_section_source';
			} else if ($source['type'] == 'page') {
				CI()->page_model->update(array('page_id'=>$source['page_id'], 'type'=>'mirror_page_source' ));
				$source['type'] = 'mirror_page_source';
			}

			// Is there a copy of this already in there?
			$mirror_item 	= CI()->page_model->get(array('parent_id'=>$to_parent, 'source_id'=>$source['page_id'] ), 'navigation');

			if (!count($mirror_item)) {

				// Create it
				$mirror_create = array(
					'title'			=> $source['title']
					, 'module'		=> 'page'
					, 'type'		=> ($source['type'] == 'mirror_page_source') ? 'mirror_page' : 'mirror_section'
					, 'parent_id'	=> $to_parent
					, 'template_id'	=> $source['template_id']
					, 'source_id'	=> $source['page_id']
					, 'page_id'		=> -1
					);

//				pr($mirror_create, 'mirror_create');

				$mirror_item = CI()->page_model->update($mirror_create);

			} else {

				$mirror_item = $mirror_item[0];

			}

//			pr($mirror_item, 'mirror_item');

			if ($source['type'] == 'mirror_section_source') {
				$this->_mirrorEntireSection($mirror_item['page_id'], $source['page_id']);
			}

		}

	}


}