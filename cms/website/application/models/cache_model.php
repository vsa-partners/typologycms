<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Typology CMS
 *
 * @author      VSA Partners / Louis D Walch (lwalch@vsapartners.com)
 * @link        http://www.vsapartners.com
 *
 */
 
 
require_once(APPPATH . 'models/core/application_model.php');

class Cache_model extends Application_model {

	var $table			= 'cache';
	var $id_field		= 'cahe_id';
    var $db_fields  	= array('params','format','module_id','module','expire_date','data','headers');

	var $json_fields	= array('headers');

	var $_cache_item;
	
	// ------------------------------------------------------------------------	
	// CACHE CHECKERS

	public function isCached($module=null, $params=array(), $module_id=null) {

			$this->_cache_item = null;
			
			if (!count($params) || is_null($module)) return false;

			if (!$this->SITE_CONF['cache_enabled']) {
				return false;
				log_message('debug', ' ** Site cache disabled in configuration settings');			
			}

			$fields['module'] 		= $module;
			$fields['params'] 		= $this->_getCacheParams($params);
			$fields['format']		= (!empty($params['format'])) ? $params['format'] : $this->layout->getFormat();

			if ($this->input->get($this->SITE_CONF['cache_recache_param'])) {

				// Forcing recache, clear out any existing records first.
				$this->db->delete($this->table, $fields);				
				log_message('debug', ' ** Refresh cache manual override');			
				return false;
				
			}

			if (!is_null($module_id)) {
				$fields['module_id']	= $module_id;
			} 
			
			$result = array();
			$result = $this->get($fields);

			// Nothing returned
			if (!count($result)) return false;
			
			$this->_cache_item = $result[0];

			return true;
	
	}

	public function getCacheData() {
			
			// Restore cache headers?				
			if (is_array($this->_cache_item['headers']) && count($this->_cache_item['headers'])) {
				foreach($this->_cache_item['headers'] as $header) {
					$this->output->set_header($header[0], $header[1]);
				}
			}

			return $this->_cache_item['data'];

	}


	// ------------------------------------------------------------------------	
	// SAVE

	public function save($data=null, $module=null, $params=array(), $module_id=null,  $period=null) {

			if (!$this->SITE_CONF['cache_enabled'] || !count($params) || is_null($module) || is_null($data)) {
				return false;
			}
		
			if (empty($period)) $period = $this->SITE_CONF['cache_default_time'];

			$fields 					= array();
			$fields[$this->id_field]	= -1;	// Force model to insert
			$fields['module'] 			= $module;
			$fields['format']			= (!empty($params['format'])) ? $params['format'] : $this->layout->getFormat();
			$fields['data'] 			= $data;
			$fields['params'] 			= $this->_getCacheParams($params);
			$fields['expire_date']		= date(DATE_DB_FORMAT, strtotime('+'.$period));
			$fields['headers']			= $this->output->headers;
			
			if(!is_null($module_id))
				$fields['module_id']	= $module_id;

			$this->update($fields);
			 
			return $fields['expire_date'];
	
	}


	// ------------------------------------------------------------------------	
	
	public function _getCacheParams($params=array()) {

			// Global params
			if (CI()->is_ajax === TRUE) {
				$params['ajax'] = 1;
			}

			$possible = $this->SITE_CONF['cache_params'];

			foreach ($possible as $check) {
				if ($this->input->get_post($check)) $params[$check] = $this->input->get_post($check);
			}
			
			return $this->_formatParams($params);		
	
	}

	protected function _formatParams($params) {	
	
			if (!is_array($params)) return $params;
			ksort($params);
			return json_encode($params);

	}

	// ------------------------------------------------------------------------	
	// CLEAR

	public function clear($module=null, $module_id=null) {

			if (is_null($module)) show_error('Error clearing cache. Invalid module specified ('.$module.').');

			$this->db->where('module', $module);
			if (!is_null($module_id)) $this->db->where('module_id', $module_id);
			return $this->db->delete($this->table);

	}


	public function clearAll() {

			$query = "TRUNCATE TABLE ".$this->table.";";
    		$this->db->query($query);
   			return true;

	}

	public function purgeOld() {

			$this->load->dbutil();

			$query = "DELETE FROM ".$this->table." WHERE expire_date <= NOW();";
    		$this->db->query($query);
    		$this->dbutil->optimize_table($this->table);

   			return true;

	}

    // ------------------------------------------------------------------------
	// EXTENDED METHODS

	public function update($fields) {

			// Before we insert, clean up old/expired records
			if (!empty($fields['id']))	$this->clear($fields['page_id']);

			parent::update($fields);
	
	}


	public function get($fields,$select_set=FALSE) {

			if (!empty($fields['params']) && is_array($fields['params'])) 
				$fields['params'] = $this->_formatParams($fields['params']);

			$this->db->where('expire_date >= ', 'NOW()', FALSE);
			
			return parent::get($fields, $select_set);

	}

}
