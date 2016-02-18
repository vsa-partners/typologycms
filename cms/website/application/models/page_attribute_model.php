<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Typology CMS
 *
 * @author      VSA Partners / Louis D Walch (lwalch@vsapartners.com)
 * @link        http://www.vsapartners.com
 *
 */
 
 
require_once(APPPATH . 'models/core/application_model.php');

class Page_attribute_model extends Application_model {

    var $table      = 'page_attributejoin';
    var $id_field   = 'page_attributejoin_id';
    var $db_fields  = array('page_attributejoin_id','page_id','template_id','parent_id','page_attributegroup_id','page_attributevalue_id');
    
    
    public function getSectionAttributeOptions($field, $id) {
    
        if (empty($field) || empty($id)) return false;

        $return     = array();
        
        /*
        
        SELECT 
            page_attributejoin.*
            , page_attributegroup.group_title, page_attributegroup.group_key 
            , page_attributevalue.value_title, page_attributevalue.value_key 
        FROM 
            page_attributejoin 
                LEFT JOIN page_attributegroup ON (page_attributejoin.page_attributegroup_id = page_attributegroup.page_attributegroup_id)
                LEFT JOIN page_attributevalue ON (page_attributejoin.page_attributevalue_id = page_attributevalue.page_attributevalue_id)        
        WHERE 
            template_id = '154';                
        
        */

        $this->db->select('page_attributejoin.*'  
            .', page_attributegroup.group_title, page_attributegroup.group_key'

            .', page_attributevalue.value_title, page_attributevalue.value_key, page_attributevalue.value_short_title, page_attributevalue.value_image, page_attributevalue.value_image_id, page_attributevalue.value_text'



            );
        $this->db->from($this->table);
        $this->db->join('page_attributegroup', 'page_attributejoin.page_attributegroup_id = page_attributegroup.page_attributegroup_id');
        $this->db->join('page_attributevalue', 'page_attributejoin.page_attributevalue_id = page_attributevalue.page_attributevalue_id');
        $this->db->where($field, $id); 

        $data = $this->db->get();

        if ($data->num_rows() > 0) {
    
            foreach ($data->result() as $row) {

                if (!array_key_exists($row->group_key, $return)) {
                    $return[$row->group_key] = array(
                        'title' => $row->group_title
                        , 'key' => $row->group_key
                        , 'options' => array()
                    );
                }
                $return[$row->group_key]['options'][$row->value_key] = array(
                    'title' => $row->value_title,
                    'short_title' => $row->value_short_title,
                    'image' => $row->value_image,
                    'image_id' => $row->value_image_id,
                    'text' => $row->value_text
                    );
            }
        
        }
        
        return $return;
        
    
    }


}

