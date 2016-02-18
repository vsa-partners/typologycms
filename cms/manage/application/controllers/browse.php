<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Typology CMS
 *
 * @author      VSA Partners / Louis D Walch (lwalch@vsapartners.com)
 * @link        http://www.vsapartners.com
 *
 */
 
 
require_once(APPPATH . 'controllers/manage.php');

class Browse extends Manage {

	var $disk_paths		= array();
	var $destination	= null;
	var $mode			= 'list';

	// ------------------------------------------------------------------------

	public function __construct() {
			parent::__construct();

			$this->MODULE_CONF 	= $this->loadConfig($this->module);

			$this->_setDiskPaths();

			$this->layout->setLayout('plain');


	}

	// ------------------------------------------------------------------------
	
	public function _remap($mode=null) {
	
		if (!is_null($mode)) $this->mode = $mode;
	
		$files 		= $this->_loadFiles();
		$content 	= $this->load->view('browse/list', array('files' => $files), TRUE);

		if ($this->input->get_post('destination')) $this->destination = $this->input->get_post('destination');

		$this->layout->appendTitle($this->disk_paths['http']);

		switch ($mode) {
		
			case 'picker':
				$this->layout->setBodyClass('popup');	
				$this->load->view('browse/popup', array('content' => $content));
				break;
			default:
				$this->layout->showData($content);
				break;
		}

	}


	protected function _loadFiles($path=null) {
		
			$files = array();

			if ($handle = opendir($this->disk_paths['server'])) {
			
				$i = 0;
			
				// LOOP THROUGH ALL FILES/DIRS
				while(false !== ($file = readdir($handle))) {
				
					settype($file,"string");

					// IGNORE HIDDEN FILES
					if (preg_match("/^\./", $file)) continue;

					$server_path 				= $this->disk_paths['server'] . $file;

					$files[$i] 					= array();
					
					$files[$i]['name'] 			= $file;
					$files[$i]['sort_name'] 	= strtolower($file);
					$files[$i]['type'] 			= (is_dir($this->disk_paths['server'].'/'.$file.'/')) ? 'dir' : 'file';
					$files[$i]['paths']			= array(
														'server' 		=> $server_path
														, 'http'		=> $this->disk_paths['http'] . $file
														, 'application'	=> $this->disk_paths['application'] . $file
													);
					$files[$i]['size'] 			= $this->_formatFileSize(filesize($server_path));					
					$files[$i]['date'] 			= date('m-d-Y H:i', filemtime($server_path));
					$files[$i]['timestamp'] 	= date('U', filemtime($server_path));
					
					$i++;
	
				}
				closedir($handle);
			
			}
			
			return $this->_sortFileArray($files);
			
	
	}


	protected function _setDiskPaths() {

			$docroot		= rtrim(DOCROOT, '/');
			$sitepath		= rtrim(SITEPATH, '/');

			// Browse root. Not allowed to load directories outside this
			$path			= $sitepath;

			if ($this->input->get_post('path')) {
				
				// Requesting a specific location, validate first
				$req_path 	= rtrim($this->input->get_post('path'), '/');

				// Remove file name if path has it (when passed in from admin field)
				if (strpos($req_path, '.')) $req_path = dirname($req_path);

				// Remove doc root
				if (substr($req_path, 0, strlen(DOCROOT)) == DOCROOT)
					$req_path = substr($req_path, strlen(DOCROOT));
				
				// Remove sitepath (application root)
				if (substr($req_path, 0, strlen($sitepath)) == $sitepath)
					$req_path = substr($req_path, strlen($sitepath));
				
				// Does the directory actually exist?
				if (is_dir($docroot . $sitepath . $req_path))
					$path = $req_path;
				
			}

			$path = rtrim($path, '/');

			// Server location, relative to server root
			$this->disk_paths['server'] 		= $docroot . $sitepath . $path . '/';

			// HTTP location, relative to document root
			$this->disk_paths['http'] 			=  $sitepath . $path . '/';

			// HTTP location, relative to application root
			$this->disk_paths['application']	=  $path . '/';

			$parent = explode('/', $this->disk_paths['http']);

			if (count($parent) > 2) {
				array_pop($parent);
				array_pop($parent);
				$this->disk_paths['parent']		= implode('/', $parent).'/';
			} else {
				$this->disk_paths['parent']		= '';
			}
					
			return true;
	
	}	
	
	
	

	// ------------------------------------------------------------------------
	// UTILITY METHODS

	protected function _formatFileSize($size) {
			$count = 0;
			$format = array("B","KB","MB","GB","TB","PB","EB","ZB","YB");
			while(($size/1024)>1 && $count<8) {
				$size=$size/1024;
				$count++;
			}
			$return = number_format($size,0,'','.')." ".$format[$count];
			return $return;
	}
	
	protected function _sortFileArray($array=array()) {
	
	 		if (count($array)) {
		        
		        $sort_array = array();
		        
				foreach($array as $row) $sort_array[] = $row['sort_name'];
				
				//sort arrays after loop
				array_multisort($sort_array, SORT_ASC, $array);

	 		}
	
			return $array;
				
	}


	
}