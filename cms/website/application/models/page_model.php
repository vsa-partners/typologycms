<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Typology CMS
 *
 * @author      VSA Partners / Louis D Walch (lwalch@vsapartners.com)
 * @link        http://www.vsapartners.com
 *
 */
 
 
require_once(APPPATH . 'models/core/application_model.php');

class Page_model extends Application_model {

    var $table      = 'page';
    var $id_field   = 'page_id';


    // This will be joined to any get requests. Use caution when implementing this, could cause field name conflicts.
    // Updated in __construct below.
    var $auto_join  = array();

    var $select_set = array(
                        'basic'             => array('page_id', 'title', 'type', 'update_date', 'parent_id', 'path')
                        , 'navigation'      => array('page_id', 'title', 'path', 'file_name', 'update_date', 'type', 'options', 'parent_id', 'template_id', 'sort')
                        , 'sort'            => array('page_id', 'title', 'sort', 'type', 'update_date', 'options', 'template_id')
                        , '_default'        => array('page_id', 'title', 'sort', 'type', 'parent_id', 'template_id', 'file_name', 'file_title', 'path', 'update_date', 'content_start_date', 'content_end_date', 'options'
                        , 'content', 'meta_image', 'meta_description', 'meta_title', 'meta_image', 'tracking_js', 'attribute_values', 'import_id', 'source_id')
                        );

    var $json_fields = array('tracking_js', 'options', 'template_options', 'template_attributes', 'attribute_values');
    
    // ------------------------------------------------------------------------

    function __construct() {    
    
            $this->ADMIN_CONF   = CI()->loadConfig('manage');
            
            CI()->load->model('page_attribute_model');
            
            if (!empty($this->ADMIN_CONF['publish']['live_domain']) && ($this->ADMIN_CONF['publish']['live_domain'] == $_SERVER['SERVER_NAME']) && ($this->ADMIN_CONF['publish']['publish_method'] == 'local_table')) {
                // If live and publish method is 'local_table' then we need to switch to another table 
                $this->table = 'page_published';
            } else if ($this->ADMIN_CONF['publish']['staging_shows'] == 'published') {
                // Check if staging should show draft content or not
                $this->table = 'page_published';
            }
            
            // Set up auto join. Must be done after the table name has been set.
            $this->auto_join    = array(
                        'table'             => 'template'
                        , 'conditions'      => $this->table.'.template_id = template.template_id'
                        , 'method'          => 'LEFT'
                        , 'fields'          => 'template.template_file_name, template.template_html_xsl_path, template.template_xml_xsl_path, template.template_options, template.template_attributes'
                        );
                
            parent::__construct();
            
    }    

    // ------------------------------------------------------------------------
    // CUSTOM METHODS

    public function getByAttribute($attributes, $select_set=false) {
    
        //if (!count($attributes)) return false;
        if (empty($attributes['parent_id']) && empty($attributes['template_id'])) return false;
    
        $i = 1;
        
        if ( ! empty($attributes['template_id'])) {
            $fields = array('template_id' => $attributes['template_id']); 
            unset($attributes['template_id']);
        } else {
            $fields = array('parent_id' => $attributes['parent_id']); 
            unset($attributes['parent_id']);
        }
       

        $wheres = array();
        $joins  = array();
        
        foreach($attributes as $name => $value) {

            // Only search when the attribute search is populated 
            if (!is_array($value) && !strlen($value)) continue;
        
            $this->db->join('page_attributejoin AS paj'.$i, 'paj'.$i.'.page_id = page.page_id');
            $this->db->join('page_attributevalue AS val'.$i, 'val'.$i.'.page_attributevalue_id = paj'.$i.'.page_attributevalue_id');
            $this->db->join('page_attributegroup AS grp'.$i, 'grp'.$i.'.page_attributegroup_id = paj'.$i.'.page_attributegroup_id');
                
            if (is_array($value)) {
                // Searching for multiple values for this attribute
                $values = array();
                foreach ($value as $v) {
                    $values[] = "val".$i.".value_key='".$v."'";
                }
                $this->db->where("(grp".$i.".group_key = '".$name."' AND (".implode(' OR ', $values)."))");
               // echo "(grp".$i.".group_key = '".$name."' AND (".impode(' OR ', $values)."))";
            } else {
                // Single value
                $this->db->where("(grp".$i.".group_key = '".$name."' AND val".$i.".value_key='".$value."')");
                //echo "(grp".$i.".group_key = '".$name."' AND val".$i.".value_key='".$value."')";
            }
        
            $i++;
        
        }
       
        /*
        foreach($attributes as $name => $value) {
    
            // Only search when the attribute search is populated 
            if (!strlen($value)) continue;
            
            $this->db->join('page_attributejoin AS paj'.$i, 'paj'.$i.'.page_id = page.page_id');
            $this->db->join('page_attributevalue AS val'.$i, 'val'.$i.'.page_attributevalue_id = paj'.$i.'.page_attributevalue_id');
            $this->db->join('page_attributegroup AS grp'.$i, 'grp'.$i.'.page_attributegroup_id = paj'.$i.'.page_attributegroup_id');
                    
            $this->db->where("(grp".$i.".group_key = '".$name."' AND val".$i.".value_key='".$value."')");
        
            $i++;
            
        }
        */

        $return = $this->get($fields, $select_set);

        return $return; 
    
    }
    
    public function getByPath($path, $select_set=false) {
            $fields = array('path' => rtrim(strtolower($path), '/'));
            return $this->get($fields, $select_set);
    }


    public function getByParentId($id, $params=array()) {

            if (strpos($id, ',')) {
                // Selecting more then one item
                $id = explode(',',$id);
                $id = array_unique(array_map("trim", $id));         
            }

            $fields = array('parent_id' => $id);
            return $this->get($fields, $params);
            
    }

    public function getForSitemap() { 

            // Special for Studios projects 
            $this->db->not_like('content', '<public><![CDATA[no]');     

            return $this->selectSet('navigation')->get(array());

    }
    
    // ------------------------------------------------------------------------
    // GET

    // Make model specific updates to result array
    protected function _processGetRow($row=array()) {

            // Add the parent path, its nice to have.
            if (!empty($row['path']) && !empty($row['file_name'])) {
                $row['parent_path']         = rtrim(strtolower($row['path']), strtolower($row['file_name']));
                $section_path               = trim($row['path'], '/');
                $section_path               = substr($section_path, 0, strpos($section_path, '/'));
                $row['section_path']        = $section_path;                    
            }

            if (empty($row['file_title']) && !empty($row['title'])) {
                $row['file_title']  = $row['title'];
            }

            foreach (array('content_start_date', 'content_end_date') as $date_field) {

                if (!empty($row[$date_field]) && ($row[$date_field] > 1)) {
                    $content_date = strtotime($row[$date_field]);
                    $row[$date_field.'_detail'] = array(
                        'weekday'       => date('l', $content_date)
                        , 'month'       => date('F', $content_date)
                        , 'month_abv'   => date('M', $content_date)
                        , 'year'        => date('Y', $content_date)
                        , 'day'         => date('j', $content_date)                     
                        , 'time'        => date('g:i A', $content_date)                 
                        , 'unix'        => $content_date
                        );
                }
            
            }

            if (!empty($row['type']) && in_array($row['type'], array('mirror_section', 'mirror_page', 'mirror_calendar', 'mirror_calendar_event'))) {

                // If this is a mirror page, need to load source inforamtion
                $row = $this->_processGetRow_mirror($row);

            } else if (!in_array($this->_req_select_set, array('sort', 'basic', 'navigation'))) {

                // Get page attributes. We don't need to do this if it's a mirror page because they will come in through the source
                $row = $this->_processGetRow_attributes($row);
            
            }

            return $row;
    
    }
    

    protected function _processGetRow_attributes($row=array()) {

            // If this has page attributes we need to get it's values and list of available options
            if (!empty($row['template_id']) && !empty($row['template_attributes']) && count($row['template_attributes'])) {
            
                foreach ($row['template_attributes'] as $group_id => $item) {               

                    $tmp                = array();
                    $node_name          = strtolower(preg_replace("/[^a-z\-_\d]/i", "", underscore($item['title'])));
                    $tmp['title']       = $item['title'];

                    if (is_array($row['attribute_values']) && array_key_exists('group_'.$group_id, $row['attribute_values'])) {
                        $tmp['values']   = CI()->page_attributejoin_model->getAttributePageValues($row['attribute_values']['group_'.$group_id], $group_id);
                    } else {
                        $tmp['values']   = array();
                    }

                    $row['attributes'][$node_name] = $tmp;
            
                }

                // House keeping
                if (!empty($row['attribute_values'])) unset($row['attribute_values']);
        
            }

            return $row;

    }

    protected function _processGetRow_mirror($row=array()) {

            // If template or content
            if (!empty($row['source_id']) && ($row['source_id'] > 0)) {

                // Backup settings for sub query
                $prev_reset                 = $this->_req_do_reset;
                $prev_first                 = $this->_req_return_first;
                $prev_set                   = $this->_req_select_set;
                $this->_req_do_reset        = FALSE;

                // Fetch source page from database
                $source = CI()->page_model->first()->selectSet($this->_req_select_set)->getById($row['source_id']);

                // Restore settings
                $this->_req_do_reset        = $prev_reset;
                $this->_req_return_first    = $prev_first;
                $this->_req_select_set      = $prev_set;

                $migrate = array('content','template_id','template_title','template_file_name','template_path','template_type','template_options','template_parent_id','template_html_xsl_path','template_xml_xsl_path','template_sort','template_create_date','template_update_date','template_cache_time', 'attributes', 'template_attributes', 'import_id', 'content_start_date', 'content_end_date');
                
                foreach ($migrate as $field) { if (isset($source[$field])) $row[$field] = $source[$field]; }
                
            }

            return $row;

    }
    
    
    public function get($fields, $select_set=false) {

            // Selecting more then one item
            if (!empty($fields[$this->id_field]) && is_array($fields[$this->id_field])) {
                $fields[$this->id_field] = array_unique(array_map("trim", $fields[$this->id_field]));           
            } else if (!empty($fields[$this->id_field]) && strpos($fields[$this->id_field], ',')) {
                $fields[$this->id_field] = explode(',',$fields[$this->id_field]);
                $fields[$this->id_field] = array_unique(array_map("trim", $fields[$this->id_field]));           
            }

            if (!empty($fields['template_id']) && strpos($fields['template_id'], ',')) {
                // Selecting more then one item
                $fields['template_id'] = explode(',',$fields['template_id']);
                $fields['template_id'] = array_unique(array_map("trim", $fields['template_id']));
            }

            
            if (in_array('status', $this->db_fields)) {
                // Do not get deleted items
                $this->param('WHERE', array($this->table . '.status <',90));
            }


            return parent::get($fields, $select_set);
        
    }

}