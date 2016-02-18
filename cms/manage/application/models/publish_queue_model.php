<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Typology CMS
 *
 * @author      VSA Partners / Louis D Walch (lwalch@vsapartners.com)
 * @link        http://www.vsapartners.com
 *
 */
 
 
require_once(APPPATH . 'models/core/application_model.php');

class Publish_queue_model extends Application_model {

	var $table 		= 'publish_queue';
	var $id_field 	= 'queue_id';
 	var $db_fields  = array('queue_id','module','module_id','title','queue_date','queue_type','object','approver_id');

	var $sort_field	= 'queue_date';
	var $sort_dir	= 'asc';

	var $select_set = array(
						'report'			=> array('queue_id', 'module', 'module_id', 'title', 'queue_date', 'queue_type', 'approver_id')
						);

	var $relations	= array(
						'approver'	 		=> array('type' => 'has_one', 'id_field' => 'approver_id', 'model' => 'user', 'model_params' => array('SELECT_SET'=>'basic'))
						);			
					
				
	var $now_date	= 0;


	protected function _processGetRow($row=array()) {

			if (!empty($row['queue_date'])) {
			
				if (date('U', strtotime($row['queue_date'])) < $this->now_date) {
					$row['queue_date_display'] = 'Next Pubish';
				} else {
					$row['queue_date_display'] = date(DATE_DISPLAY_FORMAT, strtotime($row['queue_date']));
				}
			
			}	

			return $row;
	}


	public function getJobs($module=null) {
			
			$this->now_date = date('U');
			
			if (is_null($module)) {
				$data = $this->related('approver')->selectSet('report')->limit(100)->get();
			} else {
				$data = $this->related('approver')->selectSet('report')->limit(100)->getByModule($module);
			}
			
			return $data;
			
	}


	public function getByModule($module=null, $module_id=null) {	
			$params = (!is_null($module_id)) ? array('module'=>$module, 'module_id'=>$module_id) : array('module'=>$module);
			return $this->get($params);	
	}

	public function maintenance($action=null) {

			$this->authentication->requirePermission('maintenance');

			$publish_fields = array(
				'queue_date'	=> date(DATE_DB_FORMAT)
				, 'queue_type'	=> 'maintenance'
				, 'module'		=> 'maintenance'
				, 'module_id'	=> '0'
				, 'approver_id'	=> $this->authentication->get('user_id')
				, 'title'		=> $action
				);				
				
			return $this->update($publish_fields);
	
	}

	public function sort($module=null, $module_id=null, $fields=null) {
			return $this->publish($module, $module_id, $fields, 'sort');	
	}
	public function move($module=null, $module_id=null, $fields=null) {
			return $this->publish($module, $module_id, $fields, 'move');	
	}

	public function publish($module=null, $module_id=null, $fields=null, $queue_type='publish') {

			if (is_null($module) || is_null($module_id) || is_null($fields)) show_error('Error publishing: Missing required variables.');

			$queue_date 	= (!empty($fields['queue_date'])) ? $fields['queue_date'] : EMPTY_DATE;

			$publish_fields = array(
				'queue_date'	=> $queue_date
				, 'queue_type'	=> $queue_type
				, 'object'		=> (count($fields)) ? base64_encode(serialize($fields)) : ''
				, 'module'		=> $module
				, 'module_id'	=> $module_id
				, 'approver_id'	=> (!empty($fields['approver_id'])) ? $fields['approver_id'] : $this->authentication->get('user_id')
				, 'title'		=> (!empty($fields['title'])) ? $fields['title'] : ''
				);				
				
			// Delete any outstanding queues that are scheduled before this one
			$this->db->query('DELETE FROM '.$this->table.' WHERE module = "'.$module.'" AND module_id = "'.$module_id.'" AND queue_type = "'.$queue_type.'" AND queue_date = "'.$queue_date.'" ');
			
			return $this->update($publish_fields);
	
	}

	public function delete($module=null, $module_id=null) {

			$publish_fields = array(
				'queue_date'	=> EMPTY_DATE
				, 'queue_type'	=> 'delete'
				, 'module'		=> $module
				, 'module_id'	=> $module_id
				);				
	
			parent::update($publish_fields);

			return TRUE;	
	
	}


	public function deleteItemQueues($module=null, $module_id=null) {

			if (is_null($module) || is_null($module_id)) show_error('Error deleting: Missing required variables.');

			$this->db->delete($this->table, array(
				'module' 		=> $module
				, 'module_id'	=> $module_id
				));

	
	}

	public function deleteQueue($id) {
			return parent::delete($id);	
	}


}