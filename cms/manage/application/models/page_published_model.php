<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Typology CMS
 *
 * @author      VSA Partners / Louis D Walch (lwalch@vsapartners.com)
 * @link        http://www.vsapartners.com
 *
 */
 
 
require_once(APPPATH . 'models/core/application_model.php');

class Page_published_model extends Application_model {

	var $table 			= 'page_published';
	var $id_field		= 'page_id';
	var $db_fields		= array('page_id','title','status','sort','module','type','parent_id','template_id','editor_id','approver_id','file_name','file_title','path','create_date','update_date','approve_date','publish_date','queue_date','content_start_date','content_end_date','options','content','meta_keywords','meta_description','tracking_js','tracking_omniture','tracking_other','meta_title','attribute_values');

	var $json_fields = array('tracking_js', 'options', 'attribute_values');


	public function update($fields=array()) {

		$exists = $this->db->select($this->id_field)->where($this->id_field, $fields[$this->id_field])->get($this->table)->row_array();
		
		if (count($exists)) {

			parent::update($fields);

		} else {
		
			// This is an almost mirror of parent::insert, but we don't strip id_field

			// Transform array fields into json
			if (count($this->json_fields)) {
				foreach($this->json_fields as $_field) {
					if (isset($fields[$_field])) {
						$fields[$_field] = json_encode($fields[$_field]);
					}
				}
			}					

			// Get fields, this needs to happen after any field transformations
			$this->_getMatchingFields($fields);
	
			// Create query	 & run
			$this->_addQueryValues();
			$this->_addQueryDateValue('update');
			$this->_addQueryDateValue('insert');

			$this->db->insert($this->table);

		}
		
	}


}