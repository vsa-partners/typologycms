<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Typology CMS
 *
 * @author      VSA Partners / Louis D Walch (lwalch@vsapartners.com)
 * @link        http://www.vsapartners.com
 *
 */
 
 
require_once(APPPATH . 'controllers/manage.php');

class Util extends Manage {

	var	$has_module_config	= TRUE;

	// ------------------------------------------------------------------------

	function __construct() {
			parent::__construct();

			$this->load->library('site_maintenance');

	}


	// ------------------------------------------------------------------------

	function info() {
		
			// TODO: Add check if user is super admin
			
			phpinfo();
			//exit();
			
	
	}

	// ------------------------------------------------------------------------

	function jobs($action='list') {
	
			CI()->load->model('publish_queue_model');


			if ($this->ADMIN_CONF['publish']['publish_method'] == 'local_table') {
				$data 	= '<div class="content_header"><h2>Publish Queue</h2></div>'
						. '<p>Your publish configuration is set to "Local Table" mode. This means publishes are immediate and this script is not needed.</p>';
				$this->layout->show($data);	
				return;
			}

			
			if ($action == 'delete') {
			
				if ($queue_id = $this->uri->segment(4)) {
					$this->publish_queue_model->deleteQueue($queue_id);				
				}
				
				$this->layout->setMessage('Queue Deleted');
				
			}

			$jobs = $this->publish_queue_model->getJobs();
			$this->load->view('util/jobs', array('jobs'=>$jobs));	
	
	}

	function publish() {

			if ($this->ADMIN_CONF['publish']['publish_method'] == 'local_table') {
				$data 	= '<div class="content_header"><h2>Publish</h2></div>'
						. '<p>Your publish configuration is set to "Local Table" mode. This means publishes are immediate and this script is not needed.</p>';
				$this->layout->show($data);	
				return;
			}


			$data 	= '<div class="content_header"><h2>PUBLISH</h2></div>'
				   	. '<iframe src="'.zonepath('scripts/db_publish.php?verbose=1').'" style="background-color: #fff; width: 680px; height: 400px;">'
				   	. '</iframe>';

			$this->layout->show($data);	
	
	}


	// ------------------------------------------------------------------------


	function web_clearRemoteSiteCache() {

			$this->load->model('publish_queue_model');
			$this->publish_queue_model->maintenance('clearSiteCache');

			// Add controller to wrapper title			
			$this->layout->appendTitle('Clear Remote Site Cache');			

			$view_data 	= '';
			
			$view_data 	= '<div class="content_header"><h2>CLEAR CONTENT CACHE</h2></div>'
				   		. '<p>Job has been added to the publish queue and will be performed at next sync.</p>';
			
			$this->layout->show($view_data);	
	

	}

	function web_clearRemoteAssetCache() {

			$this->load->model('publish_queue_model');
			$this->publish_queue_model->maintenance('clearAssetCache');

			// Add controller to wrapper title			
			$this->layout->appendTitle('Clear Remote Asset Cache');			

			$view_data 	= '';
			
			$view_data 	= '<div class="content_header"><h2>CLEAR ASSET CACHE</h2></div>'
				   		. '<p>Job has been added to the publish queue and will be performed at next sync.</p>';
			
			$this->layout->show($view_data);	
	}

	function web_clearLocalSiteCache() {

			// Add controller to wrapper title			
			$this->layout->appendTitle('Clear Local Site Cache');			

			$view_data 	= '';
			
			$view_data 	= '<div class="content_header"><h2>CLEAR CONTENT CACHE</h2></div>'
				   		. '<p>Complete.</p>'
				   		. '<pre>' . $this->site_maintenance->clearSiteCache() .'</pre>';
			
			$this->layout->show($view_data);	


	}

	function web_clearLocalAssetCache() {

			// Add controller to wrapper title			
			$this->layout->appendTitle('Clear Local Asset Cache');			

			$view_data 	= '';
			
			$view_data 	= '<div class="content_header"><h2>CLEAR ASSET CACHE</h2></div>'
				   		. '<p>Complete.</p>'
				   		. '<pre>' . $this->site_maintenance->clearAssetCache('all', 'website') . '</pre>';
			
			$this->layout->show($view_data);	



	}



	// ------------------------------------------------------------------------

	function backupDB() {

		$this->load->dbutil();
		
		$backup =& $this->dbutil->backup(array('format' => 'zip'));

		// Load the download helper and send the file to your desktop
		$this->load->helper('download');
		force_download('db_backup_'.date('Y-m-d').'.zip', $backup); 	
	
	}


}