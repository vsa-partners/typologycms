<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Typology CMS
 *
 * @author      VSA Partners / Louis D Walch (lwalch@vsapartners.com)
 * @link        http://www.vsapartners.com
 *
 */
 
 
require_once(APPPATH . 'models/core/application_model.php');

class Config_model extends Application_model {

	var $table 			= 'config';
	var $id_field 		= 'config_id';
	var $db_fields 		= array('config_id','zone','zone_group','name','value','options');
	var $sort_field		= array('zone', 'zone_group', 'key');


	
    // ------------------------------------------------------------------------
	// CUSTOM METHODS

    function getZones() {

			$result = $this->db->select('zone')->group_by('zone')->distinct()->get($this->table);
			
			return $result->result_array();

	}

    // ------------------------------------------------------------------------
	// EXTENDED METHODS

	public function update($fields) {
	
			if (!empty($fields['value']) && is_array($fields['value'])) {
				$fields['value'] = json_encode($fields['value']);
			}

			$update_fields = parent::update($fields);

			if ($this->ADMIN_CONF['publish']['publish_method'] != 'local_table') {
				// Queue template for publish
				CI()->load->model('publish_queue_model');
				CI()->publish_queue_model->publish($this->table, $update_fields[$this->id_field], $update_fields);
			}
			
			return $update_fields;

	}


	// Make model specific updates to result array
	protected function _processGetRow($row=array()) {

			if (!empty($row['options']['multi']) && ($row['options']['multi'] == 'yes')) {
		
				// Make sure its a json string
				if ((substr($row['value'], 0, 1) == '[') && (substr($row['value'], -1) == ']')) {
					$row['value'] = json_decode($row['value'], true);
				}
		
			}					
			
			return $row;

	}


}
