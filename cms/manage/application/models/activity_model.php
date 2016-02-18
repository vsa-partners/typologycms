<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Typology CMS
 *
 * @author      VSA Partners / Louis D Walch (lwalch@vsapartners.com)
 * @link        http://www.vsapartners.com
 *
 */
 
 
require_once(APPPATH . 'models/core/application_model.php');

class Activity_model extends Application_model {

	var $table 			= 'activity';
	var $id_field 		= 'activity_id';
	var $db_fields 		= array('activity_id','module','module_id','user_id','description','date','ip_address','type');
	var $sort_field		= 'date';
	var $sort_dir		= 'desc';

	var $allow_delete	= FALSE;
	var $allow_update	= FALSE;

	var $date_field 	= array(
							'insert' 			=> 'date'
							, 'update'			=> null
							);

	var $select_set		= array(
							'default' 			=> array('*', 'user_id')
							);

	var $auto_join		= array(
							'table' 			=> 'user'
							, 'conditions'		=> 'activity.user_id = user.user_id'
							, 'method'			=> 'LEFT'
							, 'fields'			=> 'user.user'
							);
							
	var $relations		= array(
							'page' 				=> array('type' => 'dynamic', 'id_field' => 'module_id', 'match_field' => 'module', 'model_params' => array('SELECT_SET'=>'basic'))
							, 'user' 			=> array('type' => 'dynamic', 'id_field' => 'module_id', 'match_field' => 'module', 'model_params' => array('SELECT_SET'=>'basic'))
							);
	
	var $types			= array('log', 'error');

    // ------------------------------------------------------------------------
	
	
    function log($module=null, $module_id=null, $desc=null, $type='log') {

			// Check the table, so we can place the log calls in Application Model
			if ($module == $this->table) return;

			$fields = array(
				$this->id_field	=> -1
				, 'module'		=> $module
				, 'module_id'	=> $module_id
				, 'description'	=> $desc
				, 'type'		=> $type
				, 'user_id' 	=> CI()->authentication->get('user_id')
				, 'ip_address' 	=> $_SERVER["REMOTE_ADDR"]
				);
				
			return $this->insert($fields);

    }    


    // ------------------------------------------------------------------------
	// EXTENDED METHODS

	public function get($fields=null, $select_set=false) {
		
			// Always set the select_set so we can specify which fields from auto_join'd table to fetch
			$this->selectSet('basic');
			
			return parent::get($fields, $select_set);

			
	}

	
}
