<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Typology CMS
 *
 * @author      VSA Partners / Louis D Walch (lwalch@vsapartners.com)
 * @link        http://www.vsapartners.com
 *
 */
 
 
require_once(APPPATH . 'models/core/application_model.php');

class Page_versions_model extends Application_model {

	var $table 		= 'page_versions';
	var $id_field 	= 'version_id';
	var $db_fields	= array('version_id','page_id','title','status','sort','module','type','parent_id','template_id','editor_id','approver_id','file_name','file_title','path','create_date','update_date','approve_date','queue_date','options','content','meta_description','meta_title','meta_image','tracking_omniture','tracking_other','attribute_values');

	var $sort_field	= 'version_id';
	var $sort_dir	= 'DESC';

	var $json_fields = array('tracking_js', 'options', 'attribute_values');

	var $relations	= array(
						'editor'	 			=> array('type' => 'has_one', 'id_field' => 'editor_id', 'model' => 'user', 'model_params' => array('SELECT_SET'=>'basic'))
						);								


	public function add($fields=null) {
		
			$fields[$this->id_field] 	= -1;

			// Remove dated fields
			unset($fields['update_date']);
			unset($fields['date_insert']);
			
			return $this->update($fields);
	
	}


	public function revertTo($id=null) {

			$this->authentication->requirePermission('global_publish');	

			$version_data = $this->first()->getById($id);
			
			unset($version_data[$this->id_field]);
			
			$this->output->enable_profiler(TRUE);

			$version_data['options'] = json_decode($version_data['options'], true);
			
			CI()->page_model->update($version_data);		

			// Record action log
			$this->activity->log('page', $version_data['page_id'], 'Reverted page to version #'.$id);
				
			return TRUE;
	
	}


}