<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

exit('DISABLED');


	error_reporting(E_ALL);
	ini_set('display_errors', 'On'); 
	ini_set('memory_limit', '64M');
	set_time_limit(0);
	
/**
 * Typology CMS
 *
 * @author      VSA Partners / Louis D Walch (lwalch@vsapartners.com)
 * @link        http://www.vsapartners.com
 *
 */
 
 
require_once(APPPATH . 'controllers/manage.php');

class ImageProcessor extends Manage {

	function __construct(){
		
		parent::__construct();
		
		if($this->input->get("directory_path") == "" || $this->input->get("parent_id") == ""){
			exit("Please enter a directory_path and a parent_id");
		}
		
		$this->load->model("file_model");
		$this->load->helper("directory");

		$output = '';

		$year 	= $this->input->get('year');
		$path	= $this->input->get("directory_path");
		
		$files = directory_map($path);

		foreach ($files as $key => $value){
		
			$collection_xml 			= array();
			$collection_xml[]			= '<data>';
			$collection_xml[]			= '<title>'.$key.'</title>';
			$collection_xml[]			= '<subtitle>'.$year.'</subtitle>';
			$collection_xml[]			= '<thumbnail/>';
			$collection_xml[]			= '<images>';
		
			//make a folder
			$fields = array();
			$fields['title']			= $key;
			$fields['type']				= "collection";
			$fields['parent_id']		= $this->input->get("parent_id");
			
			$upload_result 				= $this->file_model->update($fields);
			
			$folder_id 					= $upload_result["file_id"];
			
			//add files to folder
			
			$i = 0;
			while($i<sizeof($value)) {
			
				$raw_file_name = $value[$i];
				
				$pretty_file_name = $key . "_" . $value[$i];
				
				$path = $this->input->get("directory_path") . "/" . $key . "/" . $raw_file_name ;
				
				if (is_dir($path)) continue;
				if (!strpos($raw_file_name, '.jpg')) continue;
				
				$imagedata = getimagesize($path);
				
				$fields = array();
			
				// Add tmp_file values to fields before update
				$fields['file_name']		= $pretty_file_name;
				$fields['title']			= $pretty_file_name;
				$fields['is_image']			= 1;
				$fields['mime']				= $imagedata["mime"];
				$fields['type']				= "file";
				$fields['parent_id']		= $folder_id;
				$fields['ext']				= substr($raw_file_name,sizeof($raw_file_name)-5);
				$fields['options'] 			= array(
												'image_size_str' 	=> "width=\"" . $imagedata[0] . "\" height=\""  . $imagedata[1] . "\""
												, 'image_width'		=> $imagedata[0]
												, 'image_height'	=> $imagedata[1]
												);
				
				// UPDATE DATABASE
				$upload_result 				= $this->file_model->update($fields);

				$collection_xml[] 			= '<image file_path="" file_title="" file_id="'. $upload_result['file_id'] .'"/>';

				// Make sure the id directory exists
				$this->file_model->getIdDirectory($upload_result['server_path']);
	
				if (file_exists($path)) {
	
					// Move file (we need the id from insert)
					rename($path, $upload_result['server_path']);
								
				}

				$i++;
			}

			echo '<p>'.$key.'</p>';
			
			$collection_xml[]			= '</images>';
			$collection_xml[]			= '</data>';
			
			$output .= $key
					. chr(10)
					. implode(chr(10), $collection_xml)
					. chr(10)
					. chr(10);
							
		}

		mail('lwalch@vsapartners.com', 'IMG Import', $output);
		echo 'DONE!';

	
    }
    
    
}
