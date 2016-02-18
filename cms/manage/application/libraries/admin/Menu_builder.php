<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Typology CMS
 *
 * @author      VSA Partners / Louis D Walch (lwalch@vsapartners.com)
 * @link        http://www.vsapartners.com
 *
 */
 
 
// ------------------------------------------------------------------------

class Menu_builder {
	
	var $CI;
	
	var $_is_init 		= false;
	var $_active_items 	= array();
	var $_session_name	= 'admin_activeitems';
	
	// Local values from CI module
	var $module;
	var $id_field;
	var $CONF;
	var $current_path;

	var $menu			= 'access';
	var $item_view;
	var $access_menu	= array('style'=> 'none');
	
	var $select_type	= array();
	
	// ------------------------------------------------------------------------
	
	function __construct() { 

			// Load active menu items array from session
			$this->_getActiveItemsSession();
			
			CI()->load->helper('inflector');

	}


	// ------------------------------------------------------------------------
	// INIT
	
	public function init() {
	
			// Have to do this when the menu is executed becuse application values change beforehand

			$this->module			= CI()->module;
			$this->id_field			= CI()->id_field;
			$this->CONF				= CI()->CONF;
			$this->current_path		= !empty(CI()->current_path) ? CI()->current_path : '';			
			
			if (CI()->input->get_post('select_type')) {	
				$this->select_type		= explode('|',trim(CI()->input->get_post('select_type'), '|'));
			}
			
			if (!empty(CI()->CONF['access_menu'])) {

				$this->access_menu 		= CI()->CONF['access_menu'];
				
				$this->item_view		= 'menu/'.$this->menu.'_'.$this->access_menu['style'].'_item';
				$this->menu_view		= 'menu/'.$this->menu.'_'.$this->access_menu['style'];
				
				
				// Item View
				if (file_exists(APPPATH . 'views/' . $this->module . '/menu_' . $this->menu . '_item' . EXT)) {
					$this->item_view		= $this->module . '/menu_' . $this->menu . '_item';
				}
				
				// Menu View
				if (file_exists(APPPATH . 'views/' . $this->module . '/menu_' . $this->menu . EXT)) {
					$this->menu_view		= $this->module . '/menu_' . $this->menu;
				}
				
				
				// Create active items array if it dosn't exist to prevent errors
				if (empty($this->_active_items[$this->module])) 
					$this->_active_items[$this->module] = array();

			}
			
			$this->_is_init			= true;
	
	}

	// ------------------------------------------------------------------------
	// DISPLAY


	public function display($params=array(), $menu=null, $ajax=false) {
	
			if (!is_null($menu)) 	$this->menu = $menu;
			if (!$this->_is_init) 	$this->init();

			if ($this->access_menu['style'] == 'none') return false;

			
			$output				= '';
			$return				= '';
			$items				= $this->_loadAccessMenuItems($params);
			
			if (count($items)) {
	
				$cnt 	= count($items);
				$i		= 1;
	
				foreach ($items as $item) {
	
					//$last	= ($i == $cnt) ? true : false;
					
					$params = array(
						'item' 		=> $item
						, 'last'	=> ($i == $cnt) ? true : false
						);
					
					$output .= CI()->load->view($this->item_view, $params, TRUE);
					
					$i++;
	
				}	

			} else {
				$output = '<li class="last">'
						. '<div class="data">'
						. '<div class="node"> </div>'
						. '<div class="label"> (No '.ucfirst(plural($this->module)).')</div>'
						. '</div>'
						. '</li>';
			}

			// TODO: Add 'no items' output
				
			if ($ajax) {
				return $output;
			} else {
				return CI()->load->view($this->menu_view, array('output' => $output), TRUE);
			}

	}


	// ------------------------------------------------------------------------
	// ITEM LOADERS

	public function _loadAccessMenuItems($params=array()) {
	
			$items				= array();
			
			switch ($this->access_menu['style']) {
			
				case 'tree':
					$items 	= $this->_loadAccessMenuTreeItems($params);
					break;
	
				case 'list':
					$items 	= $this->_loadAccessMenuListItems($params);
					break;

				case 'list_custom':
					$items 	= $params;
					break;
	
				case 'custom':
					$items 	= $this->_loadAccessMenuCustomItems($params);
					break;
				
				case 'none':
				default:
					// Do Nothing			
					break;
					
			}

			return $items;
	
	}

	public function _loadAccessMenuCustomItems($params) {

	        if (count($params)) {
	            return $params;
	        } else {
    			return !empty($this->CONF['access_menu']['items']) ? $this->CONF['access_menu']['items'] : array();
    		}
    		
	}

    
	public function _loadAccessMenuListItems($params=array()) {
	
			$model_name 	= (!empty($this->access_menu['model']) ? $this->access_menu['model'] : $this->module ) . '_model';
			$model_param 	= !empty($this->access_menu['model_param']) ? $this->access_menu['model_param'] : array();

			if (CI()->input->get_post('parent_id')) {
				$model_param['parent_id'] = CI()->input->get_post('parent_id');
			}

			$items 			= CI()->$model_name->selectSet('navigation')->doAutoJoin(FALSE)->get($model_param);

			return $items;

	}
	
	
	public function _loadAccessMenuTreeItems($params=array()) {
	
			if (empty($params['id'])) 		$params['id'] = 0;
			
			$model_name = $this->module . '_model';
						
			$items 		= CI()->$model_name->selectSet('navigation')->doAutoJoin(TRUE)->getByParentId($params['id']);
			$return 	= array();
			
			if (count($items)) {

				foreach ($items as $item) {
	
					$show_children 	= false;

					$conf 			= $this->CONF['items'][$item['type']];
					
					$return[$item[$this->id_field]] = $item;
	
					// Checking to see if children should be displayed
					if (!empty($item['template_options']['child_edit_style']) && ($item['template_options']['child_edit_style'] == 'list')) {
						$show_children 	= false;
					} else if (count($conf['allowed_children'])
						&& (($item['type'] == 'root') 
							|| in_array($item[$this->id_field], $this->_active_items[$this->module])
							|| (!empty($this->current_path) && (substr($this->current_path, 0, strlen($item['path'])) == $item['path']))
						)) { $show_children = true; }

					// Item configuration is set to exclude children
					if (isset($conf['list_children']) && ($conf['list_children'] === FALSE)) {					    
						$show_children = false; 
					    continue;
					}

					// Check to see if children should be displayed
					if (in_array($item[$this->id_field], $this->_active_items[$this->module])
						|| (!empty($this->current_path) && (substr($this->current_path, 0, strlen($item['path'])) == $item['path']))) { 
						$show_children = true; 
					}

					// Always open up the root item
					if ($item['type'] == 'root') { 
						$show_children = true; 
					}
                    
                    // Yes, we need to show them
					if ($show_children) {
						$new_params 		= $params;
						$new_params['id'] 	= $item[$this->id_field];
						$return[$item[$this->id_field]]['children'] = $this->_loadAccessMenuTreeItems($new_params);
					}
		
				}	
			
			} else {

			}

			return $return;
	
	}


	// ------------------------------------------------------------------------	

	public function checkSelectType($type, $default=null) {
	
			if (!count($this->select_type)) {
				if (!is_null($default)) {
					$this->select_type[] = $default;
				} else {
					return true;
				}			
			}
			
			if (in_array($type, $this->select_type)) {
				return true;
			} else {
				return false;
			}
	
	}

	public function getSelectType($is_query=true) {
	
			if (!count($this->select_type)) return false;
	
			$return = '';
			if ($is_query) $return .= 'select_type=';
			$return .= '|' . implode('|', $this->select_type) . '|';
			
			return $return;
	
	}
	

	// ------------------------------------------------------------------------
	// ACTIVE MENU ITEMS
	
	public function addActiveItem($id=null) {
	
			if (!$this->_is_init) $this->init();

			if (is_null($id) || in_array($id, $this->_active_items[$this->module])) return;

			$this->_active_items[$this->module][] = $id;
			
			// Save active menu items session changes
			$this->_setActiveItemsSession();
	
	}


	public function removeActiveItem($id=null) {

			if (!$this->_is_init) $this->init();

			if (is_null($id) || !in_array($id, $this->_active_items[$this->module])) return;

			$items 	= $this->_active_items[$this->module];
			$flat 	= str_replace('|'.$id.'|','|',('|'.implode('|', $items).'|'));			

			$this->_active_items[CI()->module] = explode('|', trim($flat, '|'));

			// Save active menu items session changes
			$this->_setActiveItemsSession();
	
	}


	public function resetActiveItems($all=false) {
	
			if (!$this->_is_init) $this->init();	
	
			if ($all == true) {
				$this->_active_items 				= array();
			} else {
				$this->_active_items[$this->module] = array();
			}

			// Save active menu items session changes
			$this->_setActiveItemsSession();
			
	}

	public function _setActiveItemsSession() {
			
			$this->_active_items = array_unique($this->_active_items);
	
			CI()->session->set_userdata($this->_session_name, $this->_active_items);		
	}

	public function _getActiveItemsSession() {
			$this->_active_items	= CI()->session->userdata($this->_session_name) ? CI()->session->userdata($this->_session_name) : array();
	}

	
}