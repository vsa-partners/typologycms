<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Typology CMS
 *
 * @author      VSA Partners / Louis D Walch (lwalch@vsapartners.com)
 * @link        http://www.vsapartners.com
 *
 */
 
 
require_once(APPPATH . 'models/core/application_model.php');

class Page_attributegroup_model extends Application_model {

	var $table 		= 'page_attributegroup';
	var $id_field	= 'page_attributegroup_id';
    var $db_fields  = array('page_attributegroup_id','group_title','group_key','type');
    	

    public function getByIdWithValues($fields) {

        $result               = $this->getById($fields);
        $result['values']     = CI()->page_attributevalue_model->getGroupValues($result['page_attributegroup_id']);

        return $result;

    }

	public function update($fields) {

            $fields['group_key'] = strtolower(preg_replace("/[^a-z\-_\d]/i", "", underscore($fields['group_title'])));
	
			$update_item = parent::update($fields);

            // Everytime you update a group, let's publish it.
            CI()->load->model('publish_queue_model');
            CI()->publish_queue_model->publish($this->table, $update_item[$this->id_field], $update_item);  

			return $update_item;			
			
	}


	public function getAllGroups() {
	
	    $data = $this->db->query('SELECT '.$this->id_field.', group_title as title, type FROM '.$this->table);
	    if ($data->num_rows() > 0) {
	        return $data->result_array();
	    }
	
	}
	
	
	public function getAllGroupsForDropdown() {
	
	    $result   = $this->getAllGroups();

        // Add default option
        $result_updated[''] = '( Select Attribute Group )';

        if (count($result)) {
            foreach ($result as $row) {
                $result_updated[$row[$this->id_field]] = $row[$this->title_field];
            }
        }

        return $result_updated;
	
	}

}