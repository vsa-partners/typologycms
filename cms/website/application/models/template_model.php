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
    var $db_fields  	= array('template_id', 'template_title', 'template_html_xsl_path', 'template_xml_xsl_path', 'template_attributes');

	var $title_field	= 'template_title';
	var $sort_field		= 'template_title';
	
	var $select_set 	= array(
							'basic' 		=> array('template_id', 'template_title', 'template_update_date', 'template_html_xsl_path', 'template_xml_xsl_path', 'template_options', 'template_attributes')
							, 'navigation'	=> array('template_id', 'template_title', 'template_type', 'template_create_date', 'template_update_date', 'template_module')
							);

	var $relations		= array(
							'page'	=> array('type' => 'has_many')
							);								


    // ------------------------------------------------------------------------
	// EXTENDED METHODS

	public function update($fields) {
			
			$fields['template_title'] = trim($fields['template_title']);
			
			if (!empty($fields['template_title'])) {
				$this->load->helper('url');
				$fields['template_file_name'] = strtolower(url_title($fields['template_title']));
			}

			// options: Create JSON string from post array
			if (!empty($fields['template_options']) && is_array($fields['template_options'])) 
				$fields['template_options'] = json_encode($fields['template_options']);
			
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

			return $update_item;			
			
	}


	public function getEmptyItem($params=array()) {
			
			$item = parent::getEmptyItem();
	
			if (!empty($params['template_type'])) {
				$item['template_type']	= $params['template_type'];
			} else {
				$item['template_type']	= $this->table;
			}
	
			$item['template_xml']								= '<data/>';
			$item['template_options']['defaultFormat'] 	        = 'html';
			$item['template_type']								= !empty($params['type']) ? $params['type'] : $this->table;
			
			return $item;
		
	}

 
	 public function get($fields=null, $relations=false) {
			
			$result 			= parent::get($fields, $relations);
			$result_updated 	= array();

			// Make module specific updates to result array
			foreach ($result as $row) {
				
				// options: Create array from JSON string
				if (!empty($row['template_options'])) {
					$row['template_options'] = json_decode($row['template_options'], TRUE);
				}

				$result_updated[] = $row;

			}
			
			return $result_updated;

			
	}

}
