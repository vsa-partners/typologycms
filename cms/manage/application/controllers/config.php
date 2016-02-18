<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Typology CMS
 *
 * @author      VSA Partners / Louis D Walch (lwalch@vsapartners.com)
 * @link        http://www.vsapartners.com
 *
 */
 
 
require_once(APPPATH . 'controllers/manage.php');

class Config extends Manage {

	var	$has_module_config	= TRUE;
	var	$has_module_model	= TRUE;

	// ------------------------------------------------------------------------

	function __construct() {
			parent::__construct();

	}


	// ------------------------------------------------------------------------

	public function _remap($action=null) {

			$this->getAccessItems();

			switch($action) {
			
				case 'create':
					$this->create();
					break;

				case 'update':
					$this->update();
					break;

				case 'index':
					parent::index();			
					break;
					
				default:
					$this->edit($action);
					break;
			}

	}

	public function getAccessItems() {
	
			// Dynamically add zones to config for access menu
			
			$zones 			= $this->MODEL->getZones();
			
			$this->CONF['access_menu']['items'] = array();

			foreach ($zones as $zone) {
			
				$this->CONF['access_menu']['items'][] = array(
					'title' 	=> ucfirst($zone['zone'])
					, 'href'	=> base_url() . $this->module . '/' . $zone['zone']
					, 'icon'	=> 'img/mini_icons/arrow_collapse.gif'
					);
			}			
			
	}

	public function create() {
			$this->load->view('config/create');	
	}
	
	public function edit($edit_zone=false) {
			
			$edit_params 	= array(
								'items' 		=> ($edit_zone) ? $this->MODEL->get(array('zone'=>$edit_zone)) : array()
								, 'edit_zone' 	=> $edit_zone
								);
			
			$this->load->view('config/edit', $edit_params);	

	}
	

	public function update() {
	
			$url = $this->admin_dir.$this->module.'/';

			if ($this->input->post('create')) {
			
				// Create new config item
				$fields 				= $this->input->post('fields');			
				$fields['config_id'] 	= -1;
				
				$url .= $fields['zone'] . '/';

				$this->MODEL->update($fields);			
			
			} else {
			
				// Edit entire zone
	
				$save 		= true;
				$fields 	= $this->input->post('fields');

				if (!count($fields)) show_error('Could not save. Invalid or missing update fields.');
				
				// Loop over posted values and see what has changed
				foreach ($fields as $key => $value) {		
					$update = false;

					if (is_array($value['value'])) {

						// This is a multi
						if (!is_array($value['previous'])){
							// Something doesn't look right, just update it
							$update = true;
						} else if (implode('|',$value['value']) != implode('|',$value['previous'])) {
							// One of the values inside this multi has changed
							$update = true;
						}

					} else if ($value['value'] != $value['previous']) {

						// Normal text string, and it's changed
						$update = true;

					}

					if ($update) $this->MODEL->update(array('config_id' => $key, 'value' => $value['value']));			

				}

				$this->session->set_flashdata('message', 'Your item has been saved.');
	
				if ($this->input->get_post('zone')) $url .= $this->input->get_post('zone') . '/';
				
			}
		
			redirect($url, 'location');
			echo '<a href="'.SITEPATH.$url.'">Return to edit</a>';

	}



}