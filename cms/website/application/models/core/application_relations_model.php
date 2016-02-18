<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Typology CMS
 *
 * @author      VSA Partners / Louis D Walch (lwalch@vsapartners.com)
 * @link        http://www.vsapartners.com
 *
 */
 
  
 /**
 * NEVER CALL THIS CLASS DIRECTLY. 
 * This is a dependant of the application_model.
 *
 */

// ------------------------------------------------------------------------

class Application_relations_model {

	var $CI;
	var $model;
	
	// Local versions of the parent model's vars
	var $relations;
	var $table;
	var $id_field;

	var $_req_result_cache = array();

    // ------------------------------------------------------------------------

    function __construct($model=null) {

			if (!is_null($model)) $this->model = $model;
			
			$this->relations 	= $this->model->relations;
			$this->table 		= $this->model->table;
			$this->id_field 	= $this->model->id_field;

    }


	// ------------------------------------------------------------------------
	// GET RELATED ITEMS

	function get(&$items, $params=array(), $relations=true) {	
	
			if (empty($this->relations)) return false;
			if (!$relations) return false;
		
			// Clear request cache
			$this->_req_result_cache = array();
		
			foreach ($this->relations as $relation_key => $relation_params) {

				$related_model 		= !empty($relation_params['model'])  ? $relation_params['model'] : $relation_key ;

				// Check to see if we are only loading certain relations
				if (is_array($relations) && !in_array($related_model, $relations)) continue;

				// Make sure there is a relation get method
				$get_method = 'get_' . $relation_params['type'];				
				
				if (is_callable(array($this, $get_method))) {

					// TODO: Right now we are not passing the model paramns. Not sure if we should be, let's think about that,

					// Call it
					$items = call_user_func_array(array($this, $get_method), array($items, $relation_key, $relation_params));
					
				} else {
					
					show_error('Model Error: Invalid relation type for table '.$this->table.' ('.$relation_params['type'].')');
					
				}

			}
			
			return true;
	
	}
	

	function get_has_one($items, $relation_key=null, $relation_params=null) {	

			$related_model 		= !empty($relation_params['model'])  ? $relation_params['model'] : $relation_key ;
			$related_id_field 	= !empty($relation_params['id_field'])  ? $relation_params['id_field'] : $related_model . '_id' ;
			$model_params		= !empty($relation_params['model_params'])  ? $relation_params['model_params'] : null;

			$select_set			= (!empty($relation_params['model_params']) && !empty($relation_params['model_params']['SELECT_SET'])) ? $relation_params['model_params']['SELECT_SET'] : FALSE;


			// Make sure the model is loaded
			CI()->load->model($related_model.'_model');

			for ($i=0; $i < count($items); $i++) {

				$relation_item = FALSE;

				// Make sure we have an id
				if (empty($items[$i][$related_id_field]) || ($items[$i][$related_id_field] < 1)) {
					$items[$i]['related_' . $relation_key] 	= array();	
				} else {

					// Do we already have a result cached?
					if (array_key_exists($related_model.$items[$i][$related_id_field], $this->_req_result_cache)) {
						// Yes
						$relation_item = $this->_req_result_cache[$related_model.$items[$i][$related_id_field]];
					} else {
						// No
						$relation_item = CI()->{$related_model.'_model'}->selectSet($select_set)->param($model_params)->getById($items[$i][$related_id_field]);
						$this->_req_result_cache[$related_model.$items[$i][$related_id_field]] = $relation_item;
					}

					if (count($relation_item)) $items[$i]['related_' . $relation_key]	= $relation_item[0];

				}
			}
			
			return $items;

	}

	
	function get_dynamic($items, $relation_key=null, $relation_params=null) {	

			$related_id_field 	= !empty($relation_params['id_field'])  ? $relation_params['id_field'] : $related_model . '_id' ;
			$model_params		= !empty($relation_params['model_params'])  ? $relation_params['model_params'] : null;
			$match_field		= $relation_params['match_field'];

			if (empty($match_field)) show_error('No matchfield for dynamic relation');

			for ($i=0; $i < count($items); $i++) {

				$related_model = $items[$i][$match_field].'_model';

				// Make sure the model is loaded
				CI()->load->model($related_model);

				// Make sure we have an id
				if (empty($items[$i][$related_id_field]) || ($items[$i][$related_id_field] < 1)) {
					$items[$i]['related_' . $relation_key] 	= array();	
				} else {

					// Do we already have a result cached?
					if (array_key_exists($related_model.$items[$i][$related_id_field], $this->_req_result_cache)) {
						// Yes
						$relation_item = $this->_req_result_cache[$related_model.$items[$i][$related_id_field]];
					} else {
						// No
						$relation_item 	= CI()->{$related_model}->param($model_params)->first()->getById($items[$i][$related_id_field]);
						$this->_req_result_cache[$related_model.$items[$i][$related_id_field]] = $relation_item;
					}

					if ($relation_item) $items[$i]['related_' . $relation_key]	= $relation_item;

				}
			}
			
			return $items;

	}

	function get_has_many($items, $relation_key=null, $relation_params=null, $params=null) {	

			$related_model 		= !empty($relation_params['model'])  ? $relation_params['model'] : $relation_key ;

			$related_id_field 	= !empty($relation_params['id_field'])  ? $relation_params['id_field'] : $this->id_field ;

			// Make sure the model is loaded
			CI()->load->model($related_model.'_model');

			for ($i=0; $i < count($items); $i++) {
				$relation_item 					= CI()->{$related_model.'_model'}->get(array($related_id_field => $items[$i][$this->id_field]), $params, false);
				$items[$i]['related_' . $relation_key]	= $relation_item;
			}

			return $items;

	}


	function get_many_many($items, $relation_key=null, $relation_params=null, $params=null) {	

			$related_model 		= !empty($relation_params['model'])  ? $relation_params['model'] : $relation_key ;

			$related_id_field 	= !empty($relation_params['id_field'])  ? $relation_params['id_field'] : $related_model . '_id' ;
			$related_link_table	= $this->getJoinTable($related_model, $relation_params);

			if (!empty($relation_params['model_params']) && !empty($relation_params['model_params']['SELECT_SET'])) {
				$params['SELECT_SET'] = $relation_params['model_params']['SELECT_SET'];
			} else {
				$params['SELECT']	= $related_model.'.*';
			}
			
			$params['JOIN']		= array(
									'table' 		=> $related_link_table
									, 'conditions'	=> $related_link_table.'.'.$related_id_field.' = '.$related_model.'.'.$related_id_field
									);

			// Make sure the model is loaded
			CI()->load->model($related_model.'_model');

			for ($i=0; $i < count($items); $i++) {
				$params['WHERE']				= array($this->id_field, $items[$i][$this->id_field]);
				$relation_item 					= CI()->{$related_model.'_model'}->get(array($this->id_field => $items[$i][$this->id_field]), $params, false);

				$items[$i]['related_' . $relation_key]	= $relation_item;
			}

			return $items;

	}


	function getJoinTable($related_model, $relation) {
		
			if (!empty($relation['link_table'])) return $relation['link_table'];
			
			$tables = array($related_model, $this->table);
			sort($tables);
			
			$table = implode('_to_', $tables);

			return $table;

	}	































	// ------------------------------------------------------------------------
	// UPDATE RELATED ITEMS
	
	function _updateRelated($fields) {
	
		if (!empty($this->relations)) {
		
			foreach ($this->relations as $key => $val) {

				switch ($val['type']) {
					
					case 'has_one':
						
						if (!empty($fields[$key]) && count($fields[$key])) {

							$related_item = $fields[$key][0];
						
							// TODO: Do not update if id is negative?
							
							// Make sure it has id
							if (empty($related_item[$val['id_field']])) {
								
								if (!empty($item[$val['id_field']])) {
									// Add id from item
									$related_item[$val['id_field']] = $item[$val['id_field']];
								} else {
									// Something is wrong
									break;
								}
							}

							$update_item = modelUpdateHelper($key, $related_item);

						}						
						
						break;
						
					default:
						
						break;

				}
			
			}		
		
		}	

		return true;
	
	}


    
}