<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Typology CMS
 *
 * @author      VSA Partners / Louis D Walch (lwalch@vsapartners.com)
 * @link        http://www.vsapartners.com
 *
 */
 
 
require_once(APPPATH . 'models/core/application_model.php');

class Template_model extends Application_model {

	var $table			= 'template';
	var $id_field		= 'template_id';
    var $db_fields      = array('template_id','template_title','template_file_name','template_path','template_type','template_parent_id','template_html_xsl_path','template_xml_xsl_path','template_xml','template_options','template_attributes','template_sort','template_create_date','template_update_date','template_cache_time');

	var $title_field	= 'template_title';
	var $sort_field		= 'template_title';

	var $json_fields	= array('template_options', 'template_attributes');
	
	var $select_set 	= array(
							'basic' 		=> array('template_id', 'template_title', 'template_update_date', 'template_html_xsl_path', 'template_xml_xsl_path', 'template_options', 'template_attributes')
							, 'navigation'	=> array('template_id', 'template_title', 'template_create_date', 'template_update_date', 'template_type')
							);

	var $relations		= array(
							'page'	=> array('type' => 'has_many')
							);								


    // ------------------------------------------------------------------------
	// EXTENDED METHODS

	public function get($fields, $select_set=false) {

			$return = parent::get($fields, $select_set);
			
			return $return;
		
	}
	
	public function update($fields) {
	
			$fields['template_title'] = trim($fields['template_title']);
			
			if (!empty($fields['template_title'])) {
				$this->load->helper('url');
				$fields['template_file_name'] = strtolower(url_title($fields['template_title']));
			}


			if (empty($fields['template_attributes'])) {
			    $fields['template_attributes'] = array();			
			}


			if (!empty($fields['template_attributes_new'])) {

                // Get list of all attribuge groups for dropdown
                $all_attributegroups = CI()->page_attributegroup_model->getAllGroupsForDropdown(array());
                
				foreach ($fields['template_attributes_new'] as $group_id) {

				    if (!empty($group_id)) {
				    
                        $fields['template_attributes'][$group_id] = array(
                            'title'     => $all_attributegroups[$group_id]
                            , 'multi'   => 'no'
                            , 'can_add' => 'yes'
                            , 'attribute_group_id' => $group_id
                            );
                        
                    }
				    
                }
                
			}


			$update_item = parent::update($fields);


			// Clear page cache for linked pages

			// Update child sort if method changed
			if ((!empty($fields['orig_sort_method']) && !empty($fields['template_options']['child_sort_method']))
				&& ($fields['orig_sort_method'] != $fields['template_options']['child_sort_method'])) {
				
				$this->load->model('page_model');
				$pages = CI()->page_model->get(array($this->id_field=>$update_item[$this->id_field]));
				
				foreach ($pages as $page) {
					if ($page['type'] != 'section') continue;
					CI()->page_model->_updateSort(array('parent_id'=>$page['page_id']));
				}
				
			}		

			// Queue template for publish
			CI()->load->model('publish_queue_model');
			CI()->publish_queue_model->publish($this->table, $update_item[$this->id_field], $update_item);

			return $update_item;			
			
	}


	public function getEmptyItem($params=array()) {
			
			$item = parent::getEmptyItem();
	
			$item['template_xml']							= $this->ADMIN_CONF['xml_empty'];
			$item['template_type']							= !empty($params['type']) ? $params['type'] : $this->table;
            $item['template_file_name']                     = null;

			$item['template_options']['defaultFormat'] 		= 'html';
            $item['template_options']['html_action']        = null;
            $item['template_options']['xml_action']         = 'deny';
            $item['template_options']['child_sort_method']  = null;
            $item['template_options']['child_edit_style']   = null;

            $item['template_options']['event_show_time']    = 'no';
            $item['template_options']['event_show_end']     = 'no';
    
            $item['template_options']['child_template']     = array('');
            $item['template_options']['child_type']         = null;

            $item['template_attributes']                    = array();


            
			return $item;
		
	}


    // ------------------------------------------------------------------------
	// DELETE

	public function delete($id) {

			// Queue template for publish
			CI()->load->model('publish_queue_model');
			CI()->publish_queue_model->delete($this->table, $id);

			return parent::delete($id);

	}

}
