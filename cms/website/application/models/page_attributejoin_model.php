<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Typology CMS
 *
 * @author      VSA Partners / Louis D Walch (lwalch@vsapartners.com)
 * @link        http://www.vsapartners.com
 *
 */
 
 
require_once(APPPATH . 'models/core/application_model.php');

class Page_attributejoin_model extends Application_model {

	var $table 		        = 'page_attributejoin';
	var $id_field	        = 'page_attributejoin_id';
    var $db_fields          = array('page_attributejoin_id','page_id','template_id','parent_id','page_attributegroup_id','page_attributevalue_id');

	var $publish_mothod     = '';

    var $value_cache        = array();


    public function getAttributeGroupValues($group_id) {

        if (is_array($this->value_cache) && !array_key_exists($group_id, $this->value_cache)) {

            $values = $this->db->get_where('page_attributevalue', array('page_attributegroup_id' => $group_id));
            $values = ($values->num_rows() > 0) ? ($values->result_array()) : array();
            $tmp    = array();

            foreach($values as $value) {
                $tmp[$value['page_attributevalue_id']] = $value;
            }

            $this->value_cache[$group_id] = $tmp; 

        }

        return $this->value_cache[$group_id];

    }

    public function getAttributePageValues($values, $group_id) {

        if (!count($values)) return array();

        $all    = $this->getAttributeGroupValues($group_id);
        $return = array();

        foreach ($values as $value) {
            if (is_array($all) && array_key_exists($value['page_attributevalue_id'], $all)) {
                $return[] = $all[$value['page_attributevalue_id']];
            }
        }

        return $return;

        /*

        $return     = array();
        $data       = $this->db

            ->select('page_attributevalue.value_title,page_attributevalue.value_short_title,page_attributevalue.value_text,page_attributevalue.value_image, page_attributevalue.value_key, page_attributevalue.page_attributevalue_id')

            ->join('page_attributevalue', 'page_attributevalue.page_attributevalue_id = page_attributejoin.page_attributevalue_id')
            ->get_where('page_attributejoin', array('page_attributejoin.page_attributegroup_id' => $group_id, 'page_attributejoin.page_id' => $page_id));
        
        if ($data->num_rows() > 0) {
            return $data->result_array();
        } else {
            return array();
        }
        */

    }
    
    
}