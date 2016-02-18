<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Typology CMS
 *
 * @author      VSA Partners / Louis D Walch (lwalch@vsapartners.com)
 * @link        http://www.vsapartners.com
 *
 */
 

require_once(APPPATH . 'models/page_model.php');

class Page_calendar_model extends Page_model {

	var $table 		= 'page';
	var $id_field	= 'page_id';


	var $sort_field = 'content_start_date';
	var $sort_dir	= 'desc';
 
 	var $select_set = array(
						'basic' 			=> array('page_id', 'title', 'type', 'update_date', 'parent_id', 'path')
						, 'navigation'		=> array('page_id', 'title', 'path', 'file_name', 'update_date', 'type', 'options', 'status', 'parent_id', 'module', 'template_id', 'content_start_date', 'content_end_date')
						, 'xml'				=> array('page_id', 'title', 'path', 'file_name', 'type', 'options', 'parent_id', 'data', 'module', 'template_id')
						, 'content'			=> array('page_id','title','sort','type','parent_id','template_id','file_name','file_title','path','approve_date','publish_date','options','content','meta_keywords','meta_description','meta_title','template_id','template_file_name','template_html_xsl_path','template_xml_xsl_path','template_options')
						, 'sort'			=> array('page_id', 'title', 'sort', 'type', 'update_date', 'options', 'template_id')
						);

 
	public function update($fields, $publish_mothod='draft') {
	
	        // Additional updates which need to be made to event items only
            if (in_array($fields['type'], array('page_calendar_event', 'mirror_calendar_event_source'))) {

                $date_fields = array('content_start_date', 'content_end_date');
            
                foreach ($date_fields as $date_field) {
    
                    if (!empty($fields[$date_field.'_day'])) {
                
                        $content_date = $fields[$date_field.'_day'];
                    
                        if (!empty($fields[$date_field.'_time'])) {
    
                            $hour 	= $fields[$date_field.'_time'][0];
                            $mins 	= !empty($fields[$date_field.'_time'][1]) ? $fields[$date_field.'_time'][1] : '00';
                        
                            if ($fields[$date_field.'_time'][2] == 'pm') {
                                $hour = $hour + 12;
                            }
    
                            $content_date .= ' '.$hour.':'.$mins.':00';				
                            
                        } else {
                            $content_date .= ' 00:00:00';				
                        }
                    
                        $fields[$date_field] = $content_date;    
                
                    }
            
                }
                
                $fields['file_title']	= $fields['title'];
                $fields['file_name']	= strtolower(preg_replace("/[^a-z\-_\d]/i", "", underscore($fields['file_title'])));

			}

			// Do update (through page model)
			$update_item = parent::update($fields, $publish_mothod);

			// When you update a mirror source let's see if a destination should be automatically set up.
			// TODO: This should be moved to the create method after CHW implementation.
			if (!empty($update_item['parent_id']) && !empty($update_item['type']) && ($update_item['type'] == 'mirror_calendar_event_source')) {
				$parent = CI()->page_model->getById($update_item['parent_id'], 'navigation');
				// We only do this if this is inside a source. Parent source must always be set up manually.
				if (($parent[0]['type'] == 'mirror_calendar_source')) {

					$mirror_parent 	= CI()->page_model->get(array('source_id'=>$parent[0]['page_id']), 'navigation');

					foreach ($mirror_parent as $mp) {

						// Is there a copy of this already in there?
						$mirror_item 	= CI()->page_model->get(array('parent_id'=>$mp['page_id'], 'source_id'=>$update_item['page_id'] ), 'navigation');

						if (!count($mirror_item)) {
							// Create it
							$mirror_create = array(
								'title'			=> $update_item['title']
								, 'module'		=> 'page_calendar'
								, 'type'		=> 'mirror_calendar_event'
								, 'parent_path' => $mp['path']
								, 'parent_id'	=> $mp['page_id']
								, 'source_id'	=> $update_item['page_id']
								, 'template_id'	=> $update_item['template_id']
								, 'page_id'		=> -1

								// , 'file_name'	=> $update_item['file_name']
								// , 'path'	=> $update_item['path']

								);
							CI()->page_calendar_model->update($mirror_create);
						}
					}

				}
			}

			return $update_item;

	}
	

	// Make model specific updates to result array
	protected function _processGetRow($row=array()) {
	        
	        $row = parent::_processGetRow($row); 
	
			foreach (array('content_start_date', 'content_end_date') as $date_field) {

				if (!empty($row[$date_field]) && ($row[$date_field] > 1)) {
	
					$content_date 				= explode(' ', $row[$date_field]);
					$content_time 				= explode(':', $content_date[1]);
					
					if ($content_time[0] > 12) {
						$content_time_1				= ($content_time[0] - 12);
						$content_time_3				= 'pm';
					} else {
						$content_time_1				= $content_time[0];
						$content_time_3				= 'am';
					}
					$content_time_2				= $content_time[1];
					
					$row[$date_field.'_day']	= $content_date[0];
					$row[$date_field.'_time']	= array($content_time_1, $content_time_2, $content_time_3);
					
				} else {
					$row[$date_field.'_day']	= '';
					$row[$date_field.'_time']	= array('', '', '');
				}
			
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
			
			return $row;

	}

	
}