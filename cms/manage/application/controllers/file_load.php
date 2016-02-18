<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Typology CMS
 *
 * @author      VSA Partners / Louis D Walch (lwalch@vsapartners.com)
 * @link        http://www.vsapartners.com
 *
 */
 
 
require_once(APPPATH . 'controllers/application.php');

class File_load extends Application {

	var $zone 			= 'manage';

	var $module 		= 'file';
	var $id_field 		= 'file_id';

	
	var $image_type		= null;
	var $image_h		= null;
	var $image_w		= null;



	// ------------------------------------------------------------------------

	function __construct() {
			parent::__construct();

			$this->MODULE_CONF 	= $this->loadConfig('file');

			$this->load->model('file_model');
			$this->load->helper('file');
			
			ini_set('memory_limit', '100M');

	}


	// ------------------------------------------------------------------------

	public function _remap() {
	
	
			$uri 	= $this->uri->segment_array();
			$id 	= round(floatval($uri[2]));									// Do all the extra stuff to deal with possible extension
			$name	= !empty($uri[3]) ? $uri[3] : null;
	
			if (is_null($id)) $this->_show404();

			// Turn off the output profiler. Just in case.
			$this->output->enable_profiler(FALSE);

			// Load file record from db
			$file 		= $this->file_model->first()->getById($id);
			
			// Is this a real file?
			if (!count($file) || empty($file['server_path']))  $this->_show404(); 
			if ($this->MODULE_CONF['force_name_in_uri'] && ($name != $file['file_name'] . $file['ext'])) $this->_show404(); 

			// Get file path to load, this will generate thumbnail versions
			$load_path = $this->_getFilePath($file);

            // Could this file come from cache?
			$modified = (!empty($_SERVER['HTTP_IF_MODIFIED_SINCE'])) ? $_SERVER['HTTP_IF_MODIFIED_SINCE'] : false;
			
			if ($modified && (strtotime($modified) == filemtime($load_path))) {

				// Client's cache IS current, so we just respond '304 Not Modified'.
				header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($load_path)).' GMT', true, 304);
			
			} else {
			
				// Image not cached or cache outdated, we respond '200 OK' and output the image.
				header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($load_path)).' GMT', true, 200);

				header('Content-Length: '. filesize($load_path));
				header('Content-type: '.$file['mime']);

				//header('Cache-Control: public, max-age='.($this->MODULE_CONF['file_cache_time']*24*60*60));
				//header('Expires: '. date('D, d M Y H:i:s', strtotime('+'.$this->MODULE_CONF['file_cache_time'].' Days')) .' GMT');
				//header('Pragma: cache');

				//readfile($load_path);

				// If we need to handle very large files, could try flushing the output buffer on every loop

				$handle = fopen($load_path, "rb");
				while (!feof($handle)) {
				  echo fread($handle, 8192);
				}
				fclose($handle);
				
			}
			
			// No need to keep going
			exit();

	}


	private function _getFilePath($file=null) {
	
			// If this is not an image just return path in Database
			if ($file['is_image'] != 1) return $file['server_path'];
				
			$file_path 	= $file['server_path'];
			
			$this->_setFileProps($file);
			
			if ($this->input->get('w') || $this->input->get('h')) {
				
				$file_path_params 	= '';
				
				$request_w			= $this->input->get('w');
				$request_h			= $this->input->get('h');
				$request_m			= $this->input->get('m');
				$request_p			= $this->input->get('p');
				
				if ($request_w && !empty($this->MODULE_CONF['max_width']) && ($request_w > $this->MODULE_CONF['max_width']))
					show_error('Invalid request. Resize width above allowed size.');

				if ($request_h && !empty($this->MODULE_CONF['max_height']) && ($request_h > $this->MODULE_CONF['max_height']))
					show_error('Invalid request. Resize height above allowed size.');

				if ($request_w) $file_path_params .= '_w' . $request_w;
				if ($request_h) $file_path_params .= '_h' . $request_h;
				if ($request_m) $file_path_params .= '_m' . $request_m;
				if ($request_p) $file_path_params .= '_p' . $request_p;
				
				$quality = 90;
				
				$new_file_path = $file['base_path'] . $file_path_params . $file['ext'];
				
				// Does the file exist and make sure the original has not been updated since
				if (file_exists($new_file_path) && (filemtime($new_file_path) > filemtime($file_path))) {
								
					// Loading existing file from disk
					log_message('debug', 'Loading file from disk ('.$new_file_path.')');
					return $new_file_path;
	
				} else {
				
					// Create a new file				
					log_message('debug', 'Crating new file ('.$new_file_path.')');

					if (!is_writable(dirname($new_file_path))) {
						log_message('debug', 'Can not write to path: '.$new_file_path);
						exit('ERROR: Can not write to path: '.$new_file_path);
					}

					// Create image resource
					switch ($this->image_type) {
						case 1:
							$src_img = imagecreatefromgif($file_path);
							break;
						case 2:
							$src_img = imagecreatefromjpeg($file_path);
							break;
						case 3:
							$src_img = imagecreatefrompng($file_path);
							break;
						default:
							show_error('Invalid image type. ('.$file['mime'].')');			
							break;
			
					}
					
					$props 		= $this->_getResizeProps($file);
					$dest_img 	= imagecreatetruecolor($props['w'], $props['h']);

					// Resize image
					if ($this->input->get('w') || $this->input->get('h')) {
						imagecopyresampled($dest_img, $src_img, 0, 0, $props['x'], $props['y'], $props['w'], $props['h'], $this->image_w, $this->image_h);						
					}
	

					// Adjust image quality
					if ($request_p) {
						
						// Force serving as GIF
						$this->image_type = 1;
						
						// Greyscale
						imagefilter($dest_img, IMG_FILTER_CONTRAST, -30);
						imagefilter($dest_img, IMG_FILTER_GRAYSCALE);
						
						// Reduce color palette
						// http://www.php.net/manual/en/function.imagetruecolortopalette.php
						// zmorris at zsculpt dot com (17-Aug-2004 06:58)
            			$colors		= 5;
						$colors		= max(min($colors, 256), 2);
						$dither		= TRUE;
						$dest_copy 	= imagecreatetruecolor($props['w'], $props['h']);
						imagecopy($dest_copy, $dest_img, 0, 0, 0, 0, $props['w'], $props['h']);
						imagetruecolortopalette($dest_img, $dither, $colors);
						imagecolormatch($dest_copy, $dest_img);
						imagedestroy($dest_copy);					
					
					}


					switch ($this->image_type) {
						case 1:
							imagegif($dest_img, $new_file_path);
							break;
						case 2:
							imagejpeg($dest_img, $new_file_path, $quality);
							break;
						case 3:
							imagepng($dest_img, $new_file_path);
							break;
						default:
							break;
					}
					
					imagedestroy($dest_img);
					imagedestroy($src_img);
					
					// TODO: chmod
					
				}	
				
				// Return the newly created file path
				return $new_file_path;
			
			}
			
			
			return $file_path;

	}
	
	

	function _setFileProps($file) {
	
		$vals 	= @getimagesize($file['server_path']);
		$types 	= array(1 => 'gif', 2 => 'jpeg', 3 => 'png');
		$mime = (isset($types[$vals['2']])) ? 'image/'.$types[$vals['2']] : 'image/jpg';

		$this->image_w 		= $vals['0'];
		$this->image_h 		= $vals['1'];
		$this->image_type 	= $vals['2'];
		
	}

	function _doImageQuality($img, $depth) {
			
	
	}
	

	private function _getResizeProps($file=null) {
			
			if (is_null($file)) $this->_show404();			
			
			$resize_w	= $this->input->get('w');
			$resize_h	= $this->input->get('h');
			$resize_m	= $this->input->get('m');
			$file_w		= $file['options']['image_width'];
			$file_h		= $file['options']['image_height'];
			$new_x  	= 0;
			$new_y  	= 0;
			$new_w  	= $resize_w;
			$new_h  	= $resize_h;

			// TODO: Add check for file size, if null look it up from asset on server
			if (!$file_w && !file_h) show_error('Unable to resize image, no dimensions found.');
			
			if ($resize_w && $resize_h) {
				
				$w_ratio = $resize_w / $file_w;
				$h_ratio = $resize_h / $file_h;
				
				switch($resize_m) {
					
					// Exact
					case 'e':
						
						/*
							!!!!!
							This is not done yet, CI Image Library sucks and does not handle cropping the correct way. 
							Need to write own GD interface.
						
						*/
					
						if ($w_ratio > $h_ratio) {
							$new_w = $file_w * $w_ratio;
							$new_h = $file_h * $w_ratio;
						} else {
							$new_w = $file_w * $h_ratio;
							$new_h = $file_h * $h_ratio;
						}
						
						$new_x = ($resize_w - $new_w) / 2;
						$new_y = ($resize_h - $new_h) / 2;

						break;
					
					
					// Fit within
					case 'fw':
					default:
					
						if ($w_ratio < $h_ratio) {
							$new_w = $file_w * $w_ratio;
							$new_h = $file_h * $w_ratio;
						} else {
							$new_w = $file_w * $h_ratio;
							$new_h = $file_h * $h_ratio;
						}
						
						break;
				}
				
			} else if ($resize_h) {
				$new_w 	= ($resize_h * 100/$file_h) * $file_w/100;
			} else if ($resize_w) {
				$new_h 	= ($resize_w * 100/$file_w) * $file_h/100;
			}

			return array(
				'w' 	=> round($new_w,0)
				, 'h' 	=> round($new_h,0)
				, 'x' 	=> round($new_x, 0)
				, 'y' 	=> round($new_y, 0)
				);
				
	
	}

	
	protected function _show404() {
			show_404();	
			exit('Page not found');
	}


}