<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Typology CMS
 *
 * @author      VSA Partners / Louis D Walch (lwalch@vsapartners.com)
 * @link        http://www.vsapartners.com
 *
 */
 
 
require_once(APPPATH . 'models/core/application_model.php');

class Page_attributevalue_model extends Application_model {

	var $table 		= 'page_attributevalue';
	var $id_field	= 'page_attributevalue_id';
    var $db_fields  = array('page_attributevalue_id','page_attributegroup_id','value_title','value_key','value_short_title', 'value_image', 'value_text','type');


    public function update($fields) {
   
            $update_item = parent::update($fields);

            // Everytime you update a value, let's publish it.
            CI()->load->model('publish_queue_model');
            CI()->publish_queue_model->publish($this->table, $update_item[$this->id_field], $update_item);  

            return $update_item;            
            
    }


    public function getGroupValues($id) {
        return $this->db->order_by('value_title')->get_where('page_attributevalue', array('page_attributegroup_id' => $id))->result_array();
    }



    public function addAttributeValue($group_id, $value) {
    
        $fields = array(
            'page_attributegroup_id'    => $group_id
            , 'value_title'             => $value
            , 'value_key'               => strtolower(preg_replace("/[^a-z\-_\d]/i", "", underscore($value)))
            );
    
        $data  = $this->db->insert($this->table, $fields);

        return $this->db->insert_id();
    
    }


	public function getAttributeOptionsByGroup($id, $title=false) {
	           
        $return     = array();
        $data       = $this->db->order_by('value_title')->get_where('page_attributevalue', array('page_attributegroup_id' => $id));
        
        if ($title) {
            $return[0] = '( Select '.$title.' )';
        }
        
        if ($data->num_rows() > 0) {
            foreach ($data->result() as $row) {
                $return[$row->page_attributevalue_id] = $row->value_title;
            }
        } else {
            return array();
        }
        
        
        return $return;
	
	}


}