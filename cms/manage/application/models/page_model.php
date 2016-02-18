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

	var $table 		        = 'page';
	var $id_field	        = 'page_id';
	var $db_fields 			= array('page_id','title','status','sort','module','type','parent_id','template_id','editor_id','approver_id','file_name','file_title','path','create_date','update_date','approve_date','publish_date','queue_date','content_start_date','content_end_date','options','content','meta_description','meta_image','tracking_js','tracking_omniture','tracking_other','meta_title','import_id', 'source_id', 'attribute_values');

    var $publish_method     = 'draft';

    private $_cache			= array();

	// This will be joined to any get requests. Use caution when implementing this, could cause field name conflicts.
	var $auto_join	= array(
						'table' 			=> 'template'
						, 'conditions'		=> 'page.template_id = template.template_id'
						, 'fields'          => array(
						    '_default'      => 'template.template_id,template.template_title,template.template_file_name,template.template_options,template.template_attributes,template.template_xml'
						    , 'navigation'  => 'template.template_id,template.template_title,template.template_file_name,template.template_options'
						    , 'sort'        => 'template.template_id,template.template_title,template.template_file_name'
						    )
						, 'method'			=> 'LEFT'
						);

	var $select_set = array(
						'basic' 			=> array('page_id', 'title', 'type', 'update_date', 'parent_id', 'path', 'sort')
						, 'navigation'		=> array('page_id', 'title', 'path', 'file_name', 'update_date', 'type', 'status', 'parent_id', 'module', 'template_id', 'sort')
						, 'xml'				=> array('page_id', 'title', 'path', 'file_name', 'type', 'options', 'parent_id', 'data', 'module', 'template_id')
						, 'content'			=> array('page_id','title','sort','type','parent_id','template_id','file_name','file_title','path','approve_date','publish_date','options','content','meta_image','meta_description','meta_title','template_id','template_file_name','template_html_xsl_path','template_xml_xsl_path','template_options', 'attribute_values')
						, 'import'			=> array('page_id','title','sort','type','parent_id','template_id','file_name','file_title','path','approve_date','publish_date','options','content','meta_title')
						, 'sort'			=> array('page_id', 'title', 'sort', 'type', 'update_date', 'options', 'template_id')
						);

	var $relations	= array(
						'editor'	 			=> array('type' => 'has_one', 'id_field' => 'editor_id', 'model' => 'user', 'model_params' => array('SELECT_SET'=>'basic'))
						, 'approver'	 		=> array('type' => 'has_one', 'id_field' => 'approver_id', 'model' => 'user', 'model_params' => array('SELECT_SET'=>'basic'))
						);		
						
	
	var $json_fields = array('tracking_js', 'options', 'template_options', 'template_attributes', 'attribute_values');


    // ------------------------------------------------------------------------
	// Array cache helpers. 
	// Attempt to save on processing time for repeat queries in same page load, currently only being used for pageselect optimization.

	public function getCache($key) {
		return (array_key_exists($key, $this->_cache)) ? $this->_cache[$key] : FALSE;
	}

	public function setCache($key, $results) {
		if (!$results) $results = array();
		$this->_cache[$key] = $results;		
	}


    // ------------------------------------------------------------------------
	// CUSTOM METHODS

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

			$fields 	= array('parent_id' => $id);
			$results 	= $this->get($fields, $params);

			return $results;

			
	}

	public function getForSitemap() { 
			$fields = array('published'=>1, 'status'=>1);
			return $this->selectSet('navigation')->get($fields);
	}
    
    
    public function getLinks($id) {
    
    
    }
    
    // ------------------------------------------------------------------------
	// GET

	public function get($fields, $select_set=false) {

			if (!empty($fields['template_id']) && strpos($fields['template_id'], ',')) {
				// Selecting more then one item
				$id = explode(',',$fields['template_id']);
				$fields['template_id'] = array_unique(array_map("trim", $id));
			}

			// Do not get deleted items
			if ($this->table == 'page') $this->param('WHERE', array($this->table . '.status <',90));
						
			return parent::get($fields, $select_set);
		
	}

	// Make model specific updates to result array
	protected function _processGetRow($row=array()) {

			// If this is a mirror page, need to load show_source() inforamtion
			if (!empty($row['type']) && (strpos($row['type'], 'mirror_') === 0)) {
			
				// If template or content
				if (!empty($row['source_id']) && ($row['source_id'] > 0))	{

					// Backup settings for sub query
					$prev_reset 				= $this->_req_do_reset;
					$prev_first 				= $this->_req_return_first;
					$this->_req_do_reset 		= FALSE;

					// Fetch source page from database
					$source = CI()->page_model->first()->selectSet($this->_req_select_set)->getById($row['source_id']);
					
					// Restore settings
					$this->_req_do_reset 		= $prev_reset;
					$this->_req_return_first	= $prev_first;

					$migrate = array('content','template_id','template_title','template_file_name','template_path','template_type','template_parent_id','template_html_xsl_path','template_xml_xsl_path','template_options','template_sort','template_create_date','template_update_date','template_cache_time','content_start_date', 'content_end_date');
					
					foreach ($migrate as $field) { if (isset($source[$field])) $row[$field] = $source[$field]; }
					
					// Add the source title for reference
					if (!empty($source['title'])) {
						$row['source_title'] 		= $source['title'];
					}
					if (!empty($source['path'])) {
						$row['source_path'] 		= $source['path'];
					}
					
				}
		
			}
			

			// Add the parent path, its nice to have.
			if (!empty($row['path']) && !empty($row['file_name'])) {
				$row['parent_path'] 			= rtrim(strtolower($row['path']), strtolower($row['file_name']));
			}

			if (empty($row['file_title']) && !empty($row['title'])) {
				$row['file_title']	= $row['title'];
			}


			if (!empty($row['queue_date'])) {
				
				if ($row['queue_date'] != EMPTY_DATE) {
					// Page has a queue date
					$row['queue_date_period'] 	= 'date';
					$queue_date 				= explode(' ', $row['queue_date']);
					$row['queue_date_day']		= $queue_date[0];
					$row['queue_date_time']		= $queue_date[1];
				} else {
					// No queue date - set to now
					$row['queue_date_period'] 	= 'now';
					$row['queue_date_day']		= '';
					$row['queue_date_time']		= '';
				}
			
			}
			
			if (!in_array($this->_req_select_set, array('sort', 'basic'))) {
            
                // Add things which could be empty
                if (empty($row['options']['include_sitemap'])) 				$row['options']['include_sitemap'] = 1;
                if (empty($row['options']['section_pages'])) 				$row['options']['section_pages'] = array();
                if (empty($row['options']['section_pages']['alt_xsl'])) 	$row['options']['section_pages']['alt_xsl'] = null;
                if (empty($row['template_options']['child_sort_method']))	$row['template_options']['child_sort_method'] = null;
                if (empty($row['parent_path'])) 							$row['parent_path'] = '';
                if (empty($row['tracking_js']))                             $row['tracking_js'] = array();
                if (empty($row['tracking_js']['omniture']))                 $row['tracking_js']['omniture'] = '';
                if (empty($row['tracking_js']['misc']))                     $row['tracking_js']['misc'] = '';
            
            }


            if (!in_array($this->_req_select_set, array('sort', 'basic', 'navigation', 'import'))) {

                // If this has page attributes we need to get it's values and list of available options
                if (!empty($row['template_id']) && !empty($row['template_attributes']) && count($row['template_attributes'])) {
                
                    foreach ($row['template_attributes'] as $group_id => $item) {
                    
                        if (!empty($group_id ) && !empty($item['attribute_group_id'])) {
                
                            $tmp                = array();
                            $node_name          = strtolower(preg_replace("/[^a-z\-_\d]/i", "", underscore($item['title'])));
                    
                            $tmp['group_id']    = $item['attribute_group_id'];
                            $tmp['title']       = $item['title'];
                            $tmp['multi']       = $item['multi'];
                            $tmp['can_add']     = $item['can_add'];

                            $tmp['options']     = CI()->page_attributevalue_model->getAttributeOptionsByGroup($group_id, $item['title']);
                            $tmp['values']      = CI()->page_attributejoin_model->getAttributePageValues($row[$this->id_field], $group_id);
                    
                            $row['attributes'][$node_name] = $tmp;
                        
                        }
                
                    }
            
                }
            
            }
			
			return $row;

	}



	public function getDrafts($limit=1000, $today_only=FALSE) {
	
			if ($today_only) {
				$this->db->where("update_date > DATE_SUB(Current_Date(), INTERVAL 24 HOUR)");
			}

			return $this->param('limit', $limit)->get(array('status'=>1), 'public');
	
	}


	public function getPending($limit=100, $select_set=FALSE) {
			
			return $this->limit($limit)->related('editor')->selectSet($select_set)->get(array('status'=>10));	
	
	}

	
	public function getUpdated($limit=50, $select_set=FALSE) {

			// Check if there is a field sert
			// Check if the field exists in table_fields
			// Call model
			
			if (empty($this->date_field['update'])) return FALSE;
			
			$this->_getMatchingFields(array($this->date_field['update']));

			if ($this->_req_query_fields[0] == $this->date_field['update']) {

				$query = 'SELECT
							page.page_id
							, page.title
							, page.update_date
							, page.status
							, page.path
							, user.user
							, activity.description
						FROM (SELECT * FROM activity WHERE module = "'.$this->table.'" ORDER BY date DESC) AS activity 
							JOIN page ON (page.page_id = activity.module_id)
							JOIN user ON (activity.user_id = user.user_id)
						WHERE page.status < 80
						GROUP BY page_id
						ORDER BY activity.date DESC
						LIMIT '.$limit;

				$result = $this->db->query($query);
				$result = $result->result_array();
	
				if ($this->debug) log_message('debug', __METHOD__ . ' (' . $this->table . ') ' . preg_replace('#\r?\n#', ' ', $this->db->last_query()));
	
				$result_updated = array();
	
				// Make module specific updates to result array
				foreach ($result as $row) {
					
					// options: Create array from JSON string
					if (!empty($row['options'])) {
						$row['options'] = json_decode($row['options'], TRUE);
					}
	
					$result_updated[] = $row;
	
				}
	
				// Get model relations
				if ($this->_req_relations) $this->relations_model->get($result_updated, $this->_req_relations);
				
				// Reset for next query
				$this->_reset();
			
				return $result_updated;
			
			}
	
	}

	public function getEmptyItem($params=array()) {

			$item 										= parent::getEmptyItem();
			$item['module']								= $this->module;
			$item['content']							= $this->ADMIN_CONF['xml_empty'];

			if (!empty($params['template_id']))
				$item['template_id'] 					= $params['template_id'];
			if ($this->input->get_post('template_id'))
				$item['template_id'] 					= $this->input->get_post('template_id');


			$item['options']['include_sitemap']	= 1;
			
			if (!empty($params['type']))
				$item['type']		= $params['type'];

			if (!empty($params['title'])) {
				// Title supplied. Lets generate a file name for speedier entry
				$item['title']		= $params['title'];
				$item['file_name']	= strtolower(preg_replace("/[^a-z\d]/i", "", $params['title']));
			}

			if (!empty($params['parent_id'])) {
			
				// Get Parent
				$parent = $this->first()->getById($params['parent_id']);
			
				$item['parent_id']		= $params['parent_id'];
				$item['parent_path']	= rtrim($parent['path'], '/').'/';

			}

			return $item;

	}

    // ------------------------------------------------------------------------
	// UPDATE



	public function insert($fields, $params=array()) {
	
	    if (!empty($this->CONF['items']) && !empty($fields['type']) && array_key_exists($fields['type'], $this->CONF['items']) && !empty($this->CONF['items'][$fields['type']]['module'])) {
	        $fields['module'] = $this->CONF['items'][$fields['type']]['module'];
	    }	

	    return parent::insert($fields, $params);
	
	}

	
	public function update_decline($fields=array()) {

			$update_fields						= array();
			$update_fields[$this->id_field]		= $fields[$this->id_field];			
			$update_fields['status'] 			= '5';
			$update_fields['approve_date'] 		= EMPTY_DATE;
			$update_fields['approver_id'] 		= null;

			if ($update_item = $this->update($update_fields, 'decline')) {

				// Record action log
				$this->activity->log($this->module, $update_item[$this->id_field], 'Deline page.');

				return $update_item;
				
			}


	}

	public function update_draft($update_fields=array()) {

			$update_fields['status'] 			= '1';
			$update_fields['approve_date'] 		= EMPTY_DATE;
			$update_fields['approver_id'] 		= null;

			if ($update_item = $this->update($update_fields, 'draft')) {

				// Record action log
				$this->activity->log($this->module, $update_item[$this->id_field], (($update_fields[$this->id_field] < 1) ? 'Create Page' : 'Update Page'));

				return $update_item;
				
			}
			// TODO: Else error?

	}

	public function update_requestApproval($update_fields=array()) {

			$update_fields['status']			= '10';
			$update_fields['approve_date'] 		= EMPTY_DATE;
			$update_fields['approver_id'] 		= null;

			if ($update_item = $this->update($update_fields, 'request_approval')) {

				// Send email
				if ($this->ADMIN_CONF['workflow']['send_immediate_emails'] === TRUE) {
				
				
				}

				// Record action log
				$this->activity->log($this->module, $update_item[$this->id_field], 'Update Page & Submit for approval');
				
				return $update_item;
				
			}
			// TODO: Else error?
	
	}
	
	public function update_approvePublish($update_fields=array()) {

			$this->authentication->requirePermission('global_publish');

			if (!count($update_fields) || empty($update_fields[$this->id_field])) show_error('Error pubishing item. Invalid input fields supplied.');				

            $this->publish_method = 'publish';

			$update_fields['status'] 			= '20';
			$update_fields['approve_date'] 		= date(DATE_DB_FORMAT);
			$update_fields['publish_date'] 		= date(DATE_DB_FORMAT);
			$update_fields['approver_id'] 		= $this->authentication->get('user_id');
		
			if ($update_item = $this->update($update_fields, 'publish')) {

				// Record action log
				$this->activity->log($this->module, $update_item[$this->id_field], 'Approve / Publish');

				// Send email?

				// Add current version to page history
				CI()->load->model('page_versions_model');
				CI()->page_versions_model->add($update_item);				
				
				if ($this->ADMIN_CONF['publish']['publish_method'] == 'local_table') {
					// Copy to local table
					CI()->load->model('page_published_model');
					CI()->page_published_model->update($update_item);		
				} else {
					// Queue page for publish
					CI()->load->model('publish_queue_model');
					CI()->publish_queue_model->publish($this->table, $update_item[$this->id_field], $update_item);		
				}
				

				return $update_item;
				
			}
			// TODO: Else error?
		
	}

	public function update_unpublish($id=null) {

			$this->authentication->requirePermission('global_publish');

			if (is_null($id)) show_error('Error removing item. Invalid input supplied.');				

			if ($this->ADMIN_CONF['publish']['publish_method'] == 'local_table') {
				// Delete from local table
				CI()->load->model('page_published_model');
				CI()->page_published_model->delete($id);		
			} else {
				CI()->load->model('publish_queue_model');
				// Delete any outstanding queues
				CI()->publish_queue_model->deleteItemQueues($this->module, $id);
				// Queue page for delete on remote
				CI()->publish_queue_model->delete($this->module, $id);
			}


			// Update local version
			$update_fields = array(
				$this->id_field		=> $id
				, 'status'			=> 1
				, 'approve_date' 	=> EMPTY_DATE
				, 'publish_date' 	=> EMPTY_DATE
				, 'queue_date' 		=> EMPTY_DATE
				, 'approver_id' 	=> null
				);

			if ($update_item = $this->update($update_fields)) {

				// Record action log
				$this->activity->log($this->module, $id, 'Unpublish');
			
			} 
			
			return TRUE;
	
	}
	
	
	public function update($fields, $publish_mothod='draft') {
	

			if (!empty($fields['tracking_js']['misc'])) {
				
				$find 		= array('/* <![CDATA[ */', '/* ]]> */', '<![CDATA[', ']]>', '/*', '*/');
				$replace 	= array('','','','','','');
				$fields['tracking_js']['misc'] = str_replace($find, $replace, $fields['tracking_js']['misc']);
			
			}

			// File title (Should happen first)
			if (!empty($fields['file_title']) && empty($fields['file_name'])) {
				$fields['file_name']	= $fields['file_title'];
			} else 	if (!empty($fields['title']) && empty($fields['file_name'])) {
				$fields['file_title']	= $fields['title'];
				$fields['file_name']	= $fields['file_title'];
			}

			// Clean up the file name a bit
			if (!empty($fields['file_name'])) {

	            // Accent characters should be plain text
			    $accent_translation = array('á' => 'a', 'Á' => 'A', 'à' => 'a', 'À' => 'A', 'ă' => 'a', 'Ă' => 'A', 'â' => 'a', 'Â' => 'A', 'å' => 'a', 'Å' => 'A', 'ã' => 'a', 'Ã' => 'A', 'ą' => 'a', 'Ą' => 'A', 'ā' => 'a', 'Ā' => 'A', 'ä' => 'ae', 'Ä' => 'AE', 'æ' => 'ae', 'Æ' => 'AE', 'ḃ' => 'b', 'Ḃ' => 'B', 'ć' => 'c', 'Ć' => 'C', 'ĉ' => 'c', 'Ĉ' => 'C', 'č' => 'c', 'Č' => 'C', 'ċ' => 'c', 'Ċ' => 'C', 'ç' => 'c', 'Ç' => 'C', 'ď' => 'd', 'Ď' => 'D', 'ḋ' => 'd', 'Ḋ' => 'D', 'đ' => 'd', 'Đ' => 'D', 'ð' => 'dh', 'Ð' => 'Dh', 'é' => 'e', 'É' => 'E', 'è' => 'e', 'È' => 'E', 'ĕ' => 'e', 'Ĕ' => 'E', 'ê' => 'e', 'Ê' => 'E', 'ě' => 'e', 'Ě' => 'E', 'ë' => 'e', 'Ë' => 'E', 'ė' => 'e', 'Ė' => 'E', 'ę' => 'e', 'Ę' => 'E', 'ē' => 'e', 'Ē' => 'E', 'ḟ' => 'f', 'Ḟ' => 'F', 'ƒ' => 'f', 'Ƒ' => 'F', 'ğ' => 'g', 'Ğ' => 'G', 'ĝ' => 'g', 'Ĝ' => 'G', 'ġ' => 'g', 'Ġ' => 'G', 'ģ' => 'g', 'Ģ' => 'G', 'ĥ' => 'h', 'Ĥ' => 'H', 'ħ' => 'h', 'Ħ' => 'H', 'í' => 'i', 'Í' => 'I', 'ì' => 'i', 'Ì' => 'I', 'î' => 'i', 'Î' => 'I', 'ï' => 'i', 'Ï' => 'I', 'ĩ' => 'i', 'Ĩ' => 'I', 'į' => 'i', 'Į' => 'I', 'ī' => 'i', 'Ī' => 'I', 'ĵ' => 'j', 'Ĵ' => 'J', 'ķ' => 'k', 'Ķ' => 'K', 'ĺ' => 'l', 'Ĺ' => 'L', 'ľ' => 'l', 'Ľ' => 'L', 'ļ' => 'l', 'Ļ' => 'L', 'ł' => 'l', 'Ł' => 'L', 'ṁ' => 'm', 'Ṁ' => 'M', 'ń' => 'n', 'Ń' => 'N', 'ň' => 'n', 'Ň' => 'N', 'ñ' => 'n', 'Ñ' => 'N', 'ņ' => 'n', 'Ņ' => 'N', 'ó' => 'o', 'Ó' => 'O', 'ò' => 'o', 'Ò' => 'O', 'ô' => 'o', 'Ô' => 'O', 'ő' => 'o', 'Ő' => 'O', 'õ' => 'o', 'Õ' => 'O', 'ø' => 'oe', 'Ø' => 'OE', 'ō' => 'o', 'Ō' => 'O', 'ơ' => 'o', 'Ơ' => 'O', 'ö' => 'oe', 'Ö' => 'OE', 'ṗ' => 'p', 'Ṗ' => 'P', 'ŕ' => 'r', 'Ŕ' => 'R', 'ř' => 'r', 'Ř' => 'R', 'ŗ' => 'r', 'Ŗ' => 'R', 'ś' => 's', 'Ś' => 'S', 'ŝ' => 's', 'Ŝ' => 'S', 'š' => 's', 'Š' => 'S', 'ṡ' => 's', 'Ṡ' => 'S', 'ş' => 's', 'Ş' => 'S', 'ș' => 's', 'Ș' => 'S', 'ß' => 'SS', 'ť' => 't', 'Ť' => 'T', 'ṫ' => 't', 'Ṫ' => 'T', 'ţ' => 't', 'Ţ' => 'T', 'ț' => 't', 'Ț' => 'T', 'ŧ' => 't', 'Ŧ' => 'T', 'ú' => 'u', 'Ú' => 'U', 'ù' => 'u', 'Ù' => 'U', 'ŭ' => 'u', 'Ŭ' => 'U', 'û' => 'u', 'Û' => 'U', 'ů' => 'u', 'Ů' => 'U', 'ű' => 'u', 'Ű' => 'U', 'ũ' => 'u', 'Ũ' => 'U', 'ų' => 'u', 'Ų' => 'U', 'ū' => 'u', 'Ū' => 'U', 'ư' => 'u', 'Ư' => 'U', 'ü' => 'ue', 'Ü' => 'UE', 'ẃ' => 'w', 'Ẃ' => 'W', 'ẁ' => 'w', 'Ẁ' => 'W', 'ŵ' => 'w', 'Ŵ' => 'W', 'ẅ' => 'w', 'Ẅ' => 'W', 'ý' => 'y', 'Ý' => 'Y', 'ỳ' => 'y', 'Ỳ' => 'Y', 'ŷ' => 'y', 'Ŷ' => 'Y', 'ÿ' => 'y', 'Ÿ' => 'Y', 'ź' => 'z', 'Ź' => 'Z', 'ž' => 'z', 'Ž' => 'Z', 'ż' => 'z', 'Ż' => 'Z', 'þ' => 'th', 'Þ' => 'Th', 'µ' => 'u', 'а' => 'a', 'А' => 'a', 'б' => 'b', 'Б' => 'b', 'в' => 'v', 'В' => 'v', 'г' => 'g', 'Г' => 'g', 'д' => 'd', 'Д' => 'd', 'е' => 'e', 'Е' => 'e', 'ё' => 'e', 'Ё' => 'e', 'ж' => 'zh', 'Ж' => 'zh', 'з' => 'z', 'З' => 'z', 'и' => 'i', 'И' => 'i', 'й' => 'j', 'Й' => 'j', 'к' => 'k', 'К' => 'k', 'л' => 'l', 'Л' => 'l', 'м' => 'm', 'М' => 'm', 'н' => 'n', 'Н' => 'n', 'о' => 'o', 'О' => 'o', 'п' => 'p', 'П' => 'p', 'р' => 'r', 'Р' => 'r', 'с' => 's', 'С' => 's', 'т' => 't', 'Т' => 't', 'у' => 'u', 'У' => 'u', 'ф' => 'f', 'Ф' => 'f', 'х' => 'h', 'Х' => 'h', 'ц' => 'c', 'Ц' => 'c', 'ч' => 'ch', 'Ч' => 'ch', 'ш' => 'sh', 'Ш' => 'sh', 'щ' => 'sch', 'Щ' => 'sch', 'ъ' => '', 'Ъ' => '', 'ы' => 'y', 'Ы' => 'y', 'ь' => '', 'Ь' => '', 'э' => 'e', 'Э' => 'e', 'ю' => 'ju', 'Ю' => 'ju', 'я' => 'ja', 'Я' => 'ja');
			    $fields['file_name'] = str_replace(array_keys($accent_translation), array_values($accent_translation), $fields['file_name']);

			    // Lowercase underscore
				$fields['file_name']	= strtolower(preg_replace("/[^a-z\-_\d]/i", "", underscore($fields['file_name'])));

	            // File names are getting cray, let's trim them down
	            if (strlen($fields['file_name']) > 250) $fields['file_name'] = substr($fields['file_name'], 0, 250);

			}

            if (!empty($fields['template_options']['page_id_path']) && ($fields['template_options']['page_id_path'] == 'yes')) {
                
                // This should really be in it's own model
            
                if ($fields[$this->id_field] < 0) {
                    // Add id as file name. If this causes problems just do it on the edit page.
                    $status 			= $this->_tableStatus('page');
                    $tmp_id 			= $status['Auto_increment'];
                } else {
                    $tmp_id				= $fields[$this->id_field];            
                }

                $fields['file_title']	= $fields['title'];
                $fields['file_name']	= $tmp_id.'_'.strtolower(preg_replace("/[^a-z\-_\d]/i", "", underscore($fields['file_title'])));
            
            }
            
            // Make new path
			if (!empty($fields['parent_path']) && !empty($fields['file_name']))
				$fields['path'] = strtolower($fields['parent_path'].'/'.$fields['file_name']);

			if (!empty($fields['path']))
				$fields['path'] = reduce_multiples($fields['path'], '/');

			// Force root always to be slash
			if (!empty($fields['type']) && ($fields['type'] == 'root')) 
				$fields['path'] = '/';


			// data: Create formatted XML from post
			if (!empty($fields['content']) && is_array($fields['content'])) 
				$fields['content'] = CI()->xml_builder->buildContentXML($fields['content']);


			// Queue date
			if (!empty($fields['queue_date_period'])) {
				if (($fields['queue_date_period'] == 'date') && !empty($fields['queue_date_day'])) {
					$fields['queue_date'] = $fields['queue_date_day'] . ' ' . (!empty($fields['queue_date_time']) ? ($fields['queue_date_time'].':00') : '00:01:00');
				} else {
					$fields['queue_date'] = '';
				}
			}


			if (!empty($fields['meta_title']))			$fields['meta_title'] 		= trim($fields['meta_title']);
			if (!empty($fields['meta_image'])) 			$fields['meta_image'] 		= trim($fields['meta_image']);
			if (!empty($fields['meta_description'])) 	$fields['meta_description'] = trim($fields['meta_description']);
            
			// Update child paths
			if (!empty($fields['orig_file_name']) 
				&& ($fields['file_name'] != $fields['orig_file_name'])
				&& (($fields['type'] == 'section') || ($fields['type'] == 'root') || ($fields['type'] == 'page_database'))
				) {
				$this->_updateChildPaths($fields['page_id'], $fields['path']);
			}				

			// Do update (through application model)
			$update_item = parent::update($fields);

			// Update attributes, this needs to happen after :update because we could be inserting a new row
			if (!empty($fields['attribute_values']) && count($fields['attribute_values'])) {
				$update_item['attribute_values'] = CI()->page_attributejoin_model->updatePageAttributes($fields['attribute_values'], $update_item, $publish_mothod);
			}

			// Delete local cache
			$query = 'DELETE FROM cache WHERE module = "page" AND module_id = '.$update_item[$this->id_field];
			$this->db->simple_query($query);


			// When you update a mirror source let's see if a destination should be automatically set up.
			// TODO: This should be moved to the create method after CHW implementation.
			if (!empty($update_item['parent_id']) && !empty($update_item['type']) && in_array($update_item['type'], array('mirror_page_source', 'mirror_section_source'))) {
				$parent = CI()->page_model->getById($update_item['parent_id'], 'navigation');
				// We only do this if this is inside a source. Parent source must always be set up manually.
				if (($parent[0]['type'] == 'mirror_section_source')) {

					$mirror_parent 	= CI()->page_model->get(array('source_id'=>$parent[0]['page_id']), 'navigation');

					foreach ($mirror_parent as $mp) {

						// Is there a copy of this already in there?
						$mirror_item 	= CI()->page_model->get(array('parent_id'=>$mp['page_id'], 'source_id'=>$update_item['page_id'] ), 'navigation');

						if (!count($mirror_item)) {
							// Create it
							$mirror_create = array(
								'title'			=> $update_item['title']
								, 'module'		=> 'page'
								, 'type'		=> ($update_item['type'] == 'mirror_page_source') ? 'mirror_page' : 'mirror_section'
								, 'parent_id'	=> $mp['page_id']
								, 'source_id'	=> $update_item['page_id']
								, 'template_id'	=> $update_item['template_id']
								, 'page_id'		=> -1
								, 'file_name'	=> $update_item['file_name']
								, 'parent_path' => $mp['path']
								);
							CI()->page_model->update($mirror_create);
						}
					}

				}
			}


			// Update sort
			if ($fields[$this->id_field] < 0) {
				// New item added
				$this->_updateSort(array('parent_id' => $update_item['parent_id']));
// !!!			// TODO: Send request to update sibling sort order
			} else if (!empty($fields['title']) && !empty($fields['orig_title']) && ($fields['title'] != $fields['orig_title'])) {
				// Update thesort, but only if aplha
// !!!			// TODO: Send request to update sibling sort order
				$this->_updateSort(array('parent_id'=>$fields['parent_id'], 'method'=>'title'));
			}

			return $update_item;

	}
	
	
	public function _updateSort($params) {

			$parent	 		= $this->get(array('page_id'=>$params['parent_id']), 'sort');
			$parent 		= $parent[0];
			$method		 	= FALSE;

			if ($parent['type'] == 'root') {
				$method 	= 'title';
			} else if (!empty($parent['template_options']['child_sort_method']) 
				&& ($parent['template_options']['child_sort_method'] != 'manual')) {
				$method 	= $parent['template_options']['child_sort_method'];
			}
			
			// If method param passed in, only do sort if it matches the section option. Save a little processing time.
			if (empty($params['method']) || ($method == $params['method'])) {

				$children = $this->getByParentId($params['parent_id'], 'sort');

				if (count($children) > 1) {
					// No sorting if there is only one child

					//log_message('debug', '>> Running _updateSort ('.$method.') to refresh sibling sort orders');

					switch($method) {
						case 'title':
							$this->_updateSort_title($children);						
							break;
						case 'date_asc':
							$this->_updateSort_date($children, SORT_ASC);						
							break;
						case 'date_desc':
							$this->_updateSort_date($children, SORT_DESC);			
							break;
						default:
							return;
							break;
					}
				}
			}
			return;

	}	

	protected function _updateSort_getArray($items, $field='title') {	

			$return = array();

			foreach ($items as $key => $row) {
				$return[$row['page_id']] = strtolower($row[$field]);
			}

			return $return;

	}

	protected function _updateSort_title($items) {	
	
			$sort = $this->_updateSort_getArray($items, 'title');

			asort($sort);

			$i=1;
			foreach ($sort as $page_id => $item) {
				$this->update(array($this->id_field => $page_id, 'sort' => $i));
				$i++;
			}

	}

	protected function _updateSort_date($items, $dir=SORT_ASC) {	
	
			$sort = array();
			foreach ($items as $key => $row) {
				$sort[$key] = strtotime($row['create_date']);
			}
			
			array_multisort($items, SORT_ASC, SORT_STRING,
							$sort, SORT_NUMERIC, $dir);
	
			$i=1;
			foreach ($items as $item) {

				$this->update(array(
					$this->id_field => $item[$this->id_field]
					, 'sort'		=> $i
					)
				);
				$i++;
			}
	
	}


	protected function _updateChildPaths($parent_id, $parent_path) {
	
			CI()->load->model('publish_queue_model');

			$children = array();
			$children = $this->getByParentId($parent_id, 'navigation');

			foreach ($children as $child) {

				$new_path 	= reduce_multiples((($parent_path != '/') ? $parent_path : '') . '/' . $child['file_name'], '/');

				$itemfields = array(
					$this->id_field		=> $child[$this->id_field]
					, 'path'			=> $new_path
					, 'published'		=> 0
					);
				$this->update($itemfields);
				
				if ($child['status'] == 20) {
					CI()->publish_queue_model->move($this->module, $child[$this->id_field], $itemfields);
				}
				
				if ($child['type'] == 'section') $this->_updateChildPaths($child[$this->id_field], $new_path);
			
			}
	}


	public function move($fields) {
	
			// Update child paths
			if ($fields['type'] == 'section') {
				$this->_updateChildPaths($fields['page_id'], $fields['path']);
			}				

			// Do update. Lets skip update above because we don't need any preprocessing
			$update_item = parent::update($fields);

			if ($fields['status'] == 20) {
				CI()->publish_queue_model->move($this->module, $fields[$this->id_field], $fields);
			}
			
			return $update_item;
			
	}


    // ------------------------------------------------------------------------
	// DELETE

	public function delete($id) {

			// We do not actually delete items in this model. Just change their status to deleted.
	
			if (! ($id > 1)) show_error(__METHOD__ . ' Invalid id: '.$id);
	
			$fields						= array();
			$fields[$this->id_field] 	= $id;
			$fields['status'] 			= 99;

			parent::update($fields);

			if ($this->ADMIN_CONF['publish']['publish_method'] == 'local_table') {
				// Delete from local table
				CI()->load->model('page_published_model');
				CI()->page_published_model->delete($id);		
			} else {
				// Queue page for delete on remote
				CI()->load->model('publish_queue_model');
				CI()->publish_queue_model->delete($this->table, $id, $fields);
			}

			log_message('debug', 'Deleted '.$this->id_field.' #'.$id);
	
			$children = $this->selectSet('basic')->get(array('parent_id' => $id));
			if (count($children)) {
				foreach($children as $child) $this->delete($child[$this->id_field]);
			}
			
			return true;

	}
}