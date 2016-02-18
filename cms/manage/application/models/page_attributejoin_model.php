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


   
    /*
    public function insert($fields, $params=array()) {
            
        $insert_fields = parent::insert($fields, $params);

        if ($this->publish_mothod == 'publish') {
            // Queue for publish
            CI()->load->model('publish_queue_model');
            CI()->publish_queue_model->publish($this->table, $insert_fields[$this->id_field], $insert_fields);		
        }
        
        return $insert_fields;        
            
    }
    */

    public function updatePageAttributes($update, $page, $publish_mothod='draft') {

        // Nothing to update
        if (!is_array($update) || !count($update)) return array();

        // Make sure we have a page to link it to
        if (empty($page['page_id'])) show_error('Can not update attribute, no page supplied.');

        // First we need a list of current values
        $current_raw        =  $this->getByPageId(array($page['page_id']));
        $current            = array();
        foreach ($current_raw as $item) {
            if (!array_key_exists($item['page_attributegroup_id'], $current)) $current[$item['page_attributegroup_id']] = array();
            $current[$item['page_attributegroup_id']][] = $item['page_attributevalue_id'];
        }

        $current_keys       = array_keys($current);
        $update_keys        = array_keys($update);

        $group_delete       = array_diff($current_keys, $update_keys);
        $group_update       = array_merge(array_intersect($current_keys, $update_keys), array_diff($update_keys, $current_keys));

        // What values should we be updating?
        foreach($group_update as $group_id) {

            $value_delete   = (count($current) && !empty($current[$group_id])) ? array_diff($current[$group_id], $update[$group_id]) : array();
            $value_add      = (count($current) && !empty($current[$group_id])) ? array_diff($update[$group_id], $current[$group_id]) : $update[$group_id];


            // Add
            if (count($value_add)) {
                foreach ($value_add as $value) {
                    if (intval($value) > 0) {
                        $fields = array(
                            'page_id'                   => $page['page_id']
                            , 'template_id'             => $page['template_id']
                            , 'parent_id'               => $page['parent_id']
                            , 'page_attributegroup_id'  => $group_id
                            , 'page_attributevalue_id'  => $value
                            );
                        $insert_fields = $this->insert($fields);
                    }
                }
            }

            // Delete
            if (count($value_delete)) {
                foreach ($value_delete as $value) {
                    $this->db->delete($this->table, array('page_attributegroup_id'=>$group_id, 'page_attributevalue_id'=>$value, 'page_id'=>$page['page_id']));
                }
            }

        }

        // TODO: Delete whole groups using the $group_delete variable above!!!!!!!!!!!!!!

        // Get all attribute data for caching
        $attribute_return = array();
        foreach ($update as $group_id => $values) {

            if (count($values)) {
            
                // Query the value data so we can cache it.
                $value_data     = $this->db->where_in('page_attributevalue_id', $values)->get('page_attributevalue');
                $value_data     = $value_data->result_array();
            
                foreach ($value_data as $value) {
                    $attribute_return['group_'.$group_id][] = array(
                        'value_title'              => $value['value_title']
                        , 'value_key'              => $value['value_key']
                        , 'page_attributevalue_id' => $value['page_attributevalue_id']                
                        );
                }
            
            }

        }

        // Is this a publish request, when we need to package attribute data to be sent.
        if ($publish_mothod == 'publish') {

            $publish_data = array(
                'page_id'           => $page['page_id']
                , 'template_id'     => $page['template_id']
                , 'parent_id'       => $page['parent_id']
                , 'joins'           => $this->getByPageId(array($page['page_id']))
                );

            CI()->load->model('publish_queue_model');
            CI()->publish_queue_model->publish($this->table, $page['page_id'], $publish_data);

        }

        return $attribute_return;
    
    }
    
    
    public function getAttributePageValues($page_id, $group_id) {

        $return     = array();
        $data       = $this->db
            ->select('page_attributevalue.value_title, page_attributevalue.value_key, page_attributevalue.page_attributevalue_id')
            ->join('page_attributevalue', 'page_attributevalue.page_attributevalue_id = page_attributejoin.page_attributevalue_id')
            ->get_where('page_attributejoin', array('page_attributejoin.page_attributegroup_id' => $group_id, 'page_attributejoin.page_id' => $page_id));
        
        if ($data->num_rows() > 0) {
            return $data->result_array();
        } else {
            return array();
        }
    
    }
    
    
}