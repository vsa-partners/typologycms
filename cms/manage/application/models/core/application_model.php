<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


/**
 * APPLICATION MODEL 
 *
 * @author      Typology CMS / Louis D Walch (lwalch@vsapartners.com)
 * @link        http://www.vsapartners.com
 *
 */
 

 
 /**
 * Crud
 *
 */

// ------------------------------------------------------------------------

class Application_model extends Model {

	// Module specific variables set these in extended

	var $debug				= FALSE;

	var $table;
	var $id_field;
	var $title_field		= 'title';
	var $sort_field 		= 'sort';
	var $sort_dir			= 'asc';

	var $allow_delete		= TRUE;
	var $allow_update		= TRUE;
	
	var $json_fields		= array('options');

	var $select_set 		= array();

	var $auto_join			= array();

	var $date_field 		= array(
								'insert' 	=> 'create_date'
								, 'update'	=> 'update_date'
								);	

	var $relations			= array();
	var $relations_model;

	var $cached				= FALSE;
	
	var $reset_vars			= array('_req_type','_req_do_autojoin','_req_limit','_req_relations','_req_where_fields','_req_return_first','_req_query_fields');
	
	var $db_fields 				= array();
	var $default_query_fields	= array();

	// CURRENT REQUEST PARAMS : Reset after every query

	var $_req_do_reset		= TRUE;
	var $_req_do_autojoin	= TRUE;
	var $_req_type			= FALSE;
	var $_req_limit			= FALSE;
	var $_req_select_set	= FALSE;
	var $_req_relations		= FALSE;
	var $_req_where_fields	= FALSE;
	var $_req_select_fields	= FALSE;
	var $_req_return_first	= FALSE;
	var $_req_query_fields	= FALSE;


/*
	var $search_fields		= array();								// Must match table's FULLTEXT index
*/

    // ------------------------------------------------------------------------


    function __construct() {

			parent::__construct();
	
			// Prevent calling this class directly. Must be extended.
			if (empty($this->table)) log_message('error', __METHOD__ . '- Missing table param.');

			if (empty($this->id_field)) $this->id_field = $this->table . '_id';

			$this->load->database();

			$this->_joinRelationsModel();

			// Load table meta data before we do anything
			$this->_loadTableFields();
		        
    }

    // ------------------------------------------------------------------------

	public function __call($method, $arguments) {

			// Other possible additions:
			// deleteBy('where_value)
			// updateByTitle('where_value', field_array)
			// 


			$get_trigger = 'getBy';

			if (strpos($method, $get_trigger) === 0) {

				$get_field 		= strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', substr($method, strlen($get_trigger))));

				if (in_array($get_field, $this->db_fields)) {
				
					$ss = (!empty($arguments[1])) ? $arguments[1] : FALSE;
				
					return $this->get(array($get_field=>$arguments[0]), $ss);
					
				} else {
					trigger_error('Unknown database field "'.$get_field.'". ' . __CLASS__ . '::' . $method . '()', E_USER_ERROR);
				}

			} else {
				trigger_error('Call to undefined method ' . __CLASS__ . '::' . $method . '()', E_USER_ERROR);
			}
	
	}


    // ------------------------------------------------------------------------
	// GET

	public function get($fields=array(), $select_set=FALSE) {

			// Allow for loading relations through method, shorter
			if ($select_set) $this->selectSet($select_set);

			$this->_getMatchingFields($fields);

			// Create query
			$this->_addQueryWhere();
			$this->_addQuerySort();			
			$this->_addAutoJoin();			

			$this->db->select($this->_req_select_fields, FALSE);
			$db_result = $this->db->get($this->table);

			if ($this->debug) log_message('debug', __METHOD__ . ' (' . $this->table . ') ' . preg_replace('#\r?\n#', ' ', $this->db->last_query()));

			$result 			= $db_result->result_array();			
			$result_updated 	= array();

			// Make module specific updates to result array
			foreach ($result as $row) {
				
				// Transform jsonized fields into array
				if (count($this->json_fields)) {
					foreach($this->json_fields as $_field) {
						if (!empty($row[$_field])) {
							$row[$_field] = json_decode($row[$_field], TRUE);
						}
					}
				}					
				
				// Run through model specific method.
				$row = $this->_processGetRow($row);
	
				$result_updated[] = $row;

			}

			// Free memory associated with query objecct, don't need it anymore.
			$db_result->free_result();
			
			// Get model relations
			if ($this->_req_relations) {
				$this->relations_model->get($result_updated, $this->_req_relations);
			}
			
			// Reset for next query
			$this->_reset();

			return $result_updated;
			
	}

	public function getById($id, $select_set=FALSE) {

			$this->_req_do_reset = FALSE;

			if ($id < 1 ) {

				// New entry, get empty fields
				$return = $this->getEmptyItem();

			} else {
			
				if (is_array($id)) {
					$id = array_unique(array_map("trim", $id));			
				} else if (strpos($id, ',')) {
					// Selecting more then one item
					$id = explode(',',$id);
					$id = array_unique(array_map("trim", $id));			
				}
				$return = $this->get(array($this->id_field => $id));
			
			}

			if ($this->_req_return_first & (!empty($return[0]))) $return = $return[0];

			$this->_req_do_reset = TRUE;

			// Reset for next query
			$this->_reset();

			return $return;
			
	}

	public function getEmptyItem() {

			$item 	= $this->db->list_fields($this->table);
			$item	= array_map('clear_array_values', array_flip($item));
		
			$item[$this->id_field] = -1;
			
			// Get auto joined fields
			if (count($this->auto_join)) {

				CI()->load->model($this->auto_join['table'] . '_model');
				$auto_join_fields = CI()->{$this->auto_join['table'] . '_model'}->getEmptyItem();
				
				$item = array_merge($item, $auto_join_fields);
			
			}
			
			// Get any related groups, to avoid errors. Will not get group fields however.
			if ($this->_req_relations) {
				foreach ($this->relations as $key => $val) {
					$item['related_' . $key] = array();
				}			
			}

			// Reset for next query
			$this->_reset();

			return $item;
	}
	
	public function getDuplicate($id) {

			// New entry, get empty fields
			if ($id < 1 ) return $this->getEmptyItem();

			$item = $this->first()->getById($id);

			$item[$this->id_field] = -1;
			
			return $item;

	}

	public function getForDropdown($fields=array()) {

			$result			= $this->selectSet('basic')->get($fields);
			$result_updated = array();
			
			// Add default option
			$result_updated[''] = '( Select '.ucwords($this->table).' )';
		
			foreach ($result as $row) {
				$result_updated[$row[$this->id_field]] = $row[$this->title_field];
			}

			return $result_updated;

	}

	public function getUpdated($limit=100, $get_relations=FALSE) {

			// Check if there is a field sert
			// Check if the field exists in table_fields
			// Call model
			
			if (empty($this->date_field['update'])) return FALSE;
			
			$this->_getMatchingFields(array($this->date_field['update']));

			if ($this->_req_query_fields[0] == $this->date_field['update']) {

				$this->db->order_by($this->date_field['update'], 'desc');
				$this->db->limit($limit);
				$result = $this->db->get($this->table);
				
				return $this->get();
				
			}
	
	}
	
	
	// Blank by default, override to make transformations
	protected function _processGetRow($row=array()) {
			return $row;
	}

	// ------------------------------------------------------------------------
	// UPDATE
	
	// Presently you can only update by item id. Otherwise it will create a new item. 
	// Possible addtion of updateByFoo would fix this.

	public function update($fields=array()) {
	
			if (!count($fields)) return FALSE;


			// Run through model specific method.
            $fields = $this->_processUpdateRow($fields);

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

			// New entry, pass to insert
			if (!array_key_exists($this->id_field, $this->_req_query_fields) || $this->_req_query_fields[$this->id_field] < 1 ) {
				if ($this->debug) log_message('debug', __METHOD__ . ' ('.$this->table.') No module id field, sending request to _insert.');
				return $this->insert($fields);				
			}

			// Make sure this model allows items to be updated
			if (!$this->allow_update) show_error(__METHOD__ . ': Action denied. You are trying to call a disabled model action. (Update)');		
	
			// Create query	 & run
			$this->_addQueryValues();
			$this->_addQueryDateValue('update');

			$this->_addQueryWhere(array($this->id_field => $this->_req_query_fields[$this->id_field]));

			$this->db->update($this->table);

			$update_item = $this->_req_query_fields;

			$this->_clearCache($update_item[$this->id_field]);

			/*
			TODO: 
			If there are problems with using this model after an update, add a call to _reset().
			Afterwards make sure there are no models using parent::update() which need _req_query_fields etc.
			*/
			
			return $update_item;

	}
	
	// Blank by default, override to make transformations
	protected function _processUpdateRow($row=array()) {
			return $row;
	}


    // ------------------------------------------------------------------------
	// INSERT

	public function insert($fields,$params=array()) {
	
			// Remove id_field from insert fields, table will generate it
			if (array_key_exists($this->id_field, $fields)) unset($fields[$this->id_field]);

			// Get fields, this needs to happen after any field transformations
			$this->_getMatchingFields($fields);
	
			// Create query	 & run
			$this->_addQueryValues();
			$this->_addQueryDateValue('update');
			$this->_addQueryDateValue('insert');

			$this->db->insert($this->table);

			// Make item to return.
			$return 					= $this->_req_query_fields;
			$return[$this->id_field]	= $this->db->insert_id(); 

			return $return;

	}

	// ------------------------------------------------------------------------
	// SEARCH

	/*
	public function search($string,$params=array(),$update_relations=FALSE) {
			
			if (empty($this->search_fields) || !count($this->search_fields)) return false;
			if (empty($string)) return false;
			
			$fields = (is_array($this->search_fields)) ? explode(',', $this->search_fields) : $this->search_fields;

			$this->db->where('MATCH ('.$fields.') AGAINST ("'.$string.'")', NULL, FALSE);

			return $this->get(NULL, $params, $relations);

	}
	*/

	// ------------------------------------------------------------------------
	// DELETE

	public function delete($one, $two=NULL) {
		
			// Make sure this model allows items to be deleted
			if (!$this->allow_delete) show_error(__METHOD__ . ': Action denied. You are trying to call a disabled model action. (Delete)');		

			if (empty($one)) return FALSE;
			
			if (empty($two)) {

				// Delete by id				
				$this->db->limit(1);
				$this->db->delete($this->table, array($this->id_field => $one)); 

				$this->_clearCache($one);

			} else {
			
				// Delete by other critera
				$this->db->delete($this->table, array($one => $two));
				$this->_clearCache($two);
			}

			if ($this->debug) log_message('debug', __METHOD__ . ' (' . $this->table . ') ' . preg_replace('#\r?\n#', ' ', $this->db->last_query()));
				
			return $this->db->affected_rows();
			
	}


	// ------------------------------------------------------------------------
	// COUNT

	public function count($fields,$params=array()) {

			$this->_getMatchingFields($fields);
	
			// Create query	
			$this->_addQueryWhere();

			$this->db->from($this->table);
			$return = $this->db->count_all_results();

			if ($this->debug) log_message('debug', __METHOD__ . ' (' . $this->table . ') ' . preg_replace('#\r?\n#', ' ', $this->db->last_query()));

			return $return;

	}


	// ------------------------------------------------------------------------
	// CLEAR CACHE (On Update)
	
	protected function _clearCache($id=NULL, $module=false) {

			if (!$this->cached) return FALSE;

			CI()->load->model('cache_model', 'cache');
			CI()->cache->clear(($module) ? $module : $this->table, $id);

			return TRUE;
	
	}


	// ------------------------------------------------------------------------

	protected function _loadTableFields() {

		if (!count($this->db_fields)) {
			$this->db_fields 			= CI()->db->list_fields($this->table);
		}
			
		foreach ($this->db_fields as $name) $this->default_query_fields[] = $this->table .'.'. $name;
			
		$this->selectSet('_default');			

		if (empty($this->db_fields) || !count($this->db_fields)) show_error('ERROR: '.$this->model.': No fields found');

	}

	// ------------------------------------------------------------------------
	// LINK RELATIONS MODEL

	protected function _joinRelationsModel() {

			if (!count($this->_req_relations)) return FALSE;

			require_once(APPPATH . 'models/core/application_relations_model.php');
			
			$this->relations_model = new Application_relations_model($this);
			
			return TRUE;
	
	}
	
	// ------------------------------------------------------------------------
	// DEBUGGING
	
	public function lastQuery() {

			return '(' . $this->table . ') ' . preg_replace('#\r?\n#', ' ', $this->db->last_query());		
		
	}


	// ------------------------------------------------------------------------
	// CLASS HELPERS

	protected function _getMatchingFields($fields=null) {

//pr($fields, '_getMatchingFields: fields');

			$this->_req_query_fields = array();
			
//pr($this->db_fields, '_getMatchingFields: db_fields');

			if (!is_null($fields)) {
				foreach ($fields as $k => $v) {				
					if (!is_array($k) && in_array($k,$this->db_fields)) $this->_req_query_fields[$k] = $v;
				}
			} else {
				$this->_req_query_fields = $this->db_fields;
			}

			return TRUE;

	}

	protected function _getSelectFields($fields=array()) {
	
			$this->_req_select_fields = array();
		
			foreach ($fields as $field){
				$this->_req_select_fields[] = !strpos($field, '.')  ? $this->table.'.'.$field : $field;
			}

	}	


	protected function _tableStatus($in_table=null) {
	
			$table	= (is_null($in_table)) ? $this->table : $in_table;
			$db		= $this->db->database;			

			$query 	= $this->db->query("SHOW TABLE STATUS FROM ".$db." LIKE '".$table."'");
			
			return $query->row_array();
	
	}

	// ------------------------------------------------------------------------
	// QUERY HELPERS

	protected function _addQueryParam($name=null, $value=null) {

			switch (strtoupper($name)) {
			
				case 'LIMIT':
					$this->_addQueryParamLimit($value);
					break;

				case 'SELECT':
					$this->_addQueryParamSelects($value);
					break;

				case 'JOIN':
					$this->_addQueryParamJoin($value);
					break;

				case 'WHERE':
					$this->_addQueryParamWhere($value);
					break;

				case 'ORDER':
					$this->_addQueryParamOrder($value);
					break;

				default: break;
			
			}
	
	}

	protected function _addQueryParamJoin($params) {
			if (!is_array($params)) return FALSE;
			$this->db->join(
				$params['table']
				, $params['conditions']
				, !empty($params['method']) ? $params['method'] : null
				);
			return TRUE;			
	}

	/*
	protected function _addQueryParamSelectSet($set) {
			
			if (array_key_exists($set, $this->select_set)) {		
				foreach ($this->select_set[$set] as $col) {
					$this->_addQueryParamSelects($col);			
				}
				return TRUE;			
			} else {
				return FALSE;
			}
	}


	protected function _addQueryParamSelects($params) {
			if (is_array($params)) {
				$tmp = implode(',', $params);
			} else {
				$tmp = $params;
			}
	
			$this->db->select($tmp, FALSE);
			return TRUE;	
	}
	*/

	protected function _addQueryParamLimit($params) {
	
			if (empty($params)) return;
	
			if (is_array($params)) {
				$this->db->limit($params[0], $params[1]);
			} else {
				$this->db->limit($params);
			}
			return TRUE;	
	}

	protected function _addQueryParamOrder($params) {
			if (is_array($params)) {
				$this->db->order_by($params[0], $params[1]);
			} else {
				$this->db->order_by($params);
			}
			return TRUE;	
	}

	protected function _addQueryParamWhere($params) {
	
			if (is_array($params[1])) {
				$this->db->where_in($params[0], $params[1]);
			} else {
				$this->db->where($params[0], $params[1]);			
			}
			return TRUE;	
	}

	protected function _addQueryValues() {	
			foreach ($this->_req_query_fields as $k => $v) {
				if (is_array($v) && strpos($k, '_json')) $v = json_encode($v);
				$this->db->set($k, $v); 
			}
	}

	protected function _addQueryDateValue($method) {
		
			// Is date_field an array, if so match the method against it
			if (is_array($this->date_field)) {
				$field = $this->date_field[$method];
			} else {
				$field = $this->date_field;
			}
	
			if (!empty($field) && in_array($field, $this->db_fields))
				$this->db->set($field, 'NOW()', FALSE);
	}


	protected function _addQueryWhere($params=array()) {

			// Were custom params passed in to match against, if so use them.
			$fields = count($params) ? $params : $this->_req_query_fields;
	
			foreach ($fields as $k => $v) {

				if (is_array($v)) {
					$this->db->where_in($this->table . '.' . $k, $v);
				} else {
					$this->db->where($this->table . '.' . $k, $v);			
				}

			}
	
	}

	protected function _addQuerySort($sort_field=NULL) {

			if (empty($this->sort_field)) return;

			if (empty($sort_field)) $sort_field = $this->sort_field;
	
			if (is_array($sort_field)) {
				foreach($sort_field as $field){
					$this->_addQuerySort($field);
				}
			} else if (in_array($sort_field, $this->db_fields)) {
				$this->db->order_by($this->table.'.'.$sort_field, $this->sort_dir);
			} else {
				return FALSE;
			}
				
	}	
	
	protected function _addAutoJoin() {

			if (!$this->_req_do_autojoin) return;
			if (!count($this->auto_join)) return;

			// Make sure the id is in select_fields
			if (in_array($this->table.'.'.$this->auto_join['table'].'_id', $this->_req_select_fields)) {
                
                if (!empty($this->auto_join['fields']) && is_array($this->auto_join['fields'])) {
                    if (!empty($this->_req_select_set) && array_key_exists($this->_req_select_set, $this->auto_join['fields'])) {
                        $this->_req_select_fields[] = $this->auto_join['fields'][$this->_req_select_set];
                    } else {
                        $this->_req_select_fields[] = $this->auto_join['fields']['_default'];
                    }
                } else if (!empty($this->auto_join['fields'])) {
                    $this->_req_select_fields[] = $this->auto_join['fields'];                
                } else {
                    $this->_req_select_fields[] = $this->auto_join['table'] . '.*';
                }
                
				$this->_addQueryParamJoin($this->auto_join);
			}
	
	}
	
	protected function _reset(){

			if (!$this->_req_do_reset) return;
	
			if ($this->_req_select_set && ($this->_req_select_set != '_default')) {
				$this->_req_select_set  = FALSE;
				$this->selectSet('_default');			
			}
	
			
			foreach ($this->reset_vars as $var) {
				$this->{$var} = FALSE;
			}
	
// Turned this off because it was breaking _default select set. Might want to kick it back on thou...	
//			$this->_req_select_fields = $this->default_query_fields;

			$this->_req_select_fields = $this->db_fields;
			
	}
	

    // ------------------------------------------------------------------------
	// CHAINED MINI HELPERS
	
	public function related($relations=FALSE) {

			$this->_req_relations = $relations;

			// Return self, allow method chaining.
			return $this;
	
	}

	public function param($param=false, $value=null) {
	
			if (is_array($param)) {
				foreach ($param as $n => $v) $this->_addQueryParam($n, $v);
			} else if ($param){
				$this->_req_select_set = FALSE;
				$this->_addQueryParam($param, $value);
			}
			
			// Return self, allow method chaining.
			return $this;
	
	}


	public function first($first=TRUE) {
	
			if ($first) {
				$this->db->limit(1);
				$this->_req_return_first = TRUE;
			}

			// Return self, allow method chaining.
			return $this;
	
	}

	public function doAutoJoin($set=true) {
			$this->_req_do_autojoin = $set;
			// Return self, allow method chaining.
			return $this;
	}

	public function selectSet($set=false) {

			if (strlen($set) && count($this->select_set) && array_key_exists($set, $this->select_set)) {

				// Select set exists, only get those fields
				$this->_req_select_set		= $set;
				$this->_getSelectFields($this->select_set[$set]);

			} else {
			
				// Invalid select set, get all fields
				$this->_getSelectFields($this->db_fields);
			
			}
			
			// Return self, allow method chaining.
			return $this;
	
	}	

	public function limit($num=1) {
	
			$this->_req_limit		= $num;
			
			$this->db->limit($num);
			
			// Return self, allow method chaining.
			return $this;
	
	}

	public function sort($field=null) {
	
			if (!is_null($field)) $this->_addQuerySort($field);

			// Return self, allow method chaining.
			return $this;

	}	

}