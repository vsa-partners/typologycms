<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Typology CMS
 *
 * @author      VSA Partners / Louis D Walch (lwalch@vsapartners.com)
 * @link        http://www.vsapartners.com
 *
 */
 
 
class LayoutAssets {

	var $_sets				= array();

	var $_assets			= array();
	var $_meta				= array();
	
	var $add_app_path		= FALSE;
	
	/* HTML Tag Templates */
	var $_js_tag			= '<script language="javascript" src="%s" group="%s" type="text/javascript"> </script>';
	var $_css_tag			= '<link rel="stylesheet" href="%s" type="text/css" media="%s" charset="utf-8" />';
	var $_cssiphone_tag		= '<link rel="stylesheet" href="%s" type="text/css" media="only screen and (max-device-width: 480px)" charset="utf-8" />';

	var $allowed_types		= array('css', 'cssiphone', 'js');
	
	var $cache_location		= '';
	
	var $compress_assets	= FALSE;

	var $cache_buster		= '';
	
	public function __construct($config=array()) {
	
			if (isset($config['assets']['cache_buster'])) $this->cache_buster = $config['assets']['cache_buster'];

			if (isset($config['assets']['add_app_path'])) $this->add_app_path = $config['assets']['add_app_path'];

			if (isset($config['assets']['compression_enabled']) && ($config['assets']['compression_enabled'] === TRUE)) $this->compress_assets = TRUE;
			if (CI()->input->get('compression_enabled') == 'FALSE') $this->compress_assets = FALSE;
		
			$this->cache_location 	= zonepath($config['assets']['cache_directory']);		
			$this->_sets 			= $config['asset_sets'];

			// Create holders for all the asset types
			foreach ($this->allowed_types as $type) {
				$this->_assets[$type] 		= array();
			}

	}
	

	// ------------------------------------------------------------------------
	// ADD

	public function add($type='', $path='', $group=null) {

//			if (!in_array($type, $this->allowed_types)) show_error('"'.$type.'" is not in the list of allowed asset types.');

			if (is_null($group)) {
				$group = ($type == 'css') ? 'screen' : 'default';
			}
			
			if (is_array($path)) {

				foreach($path as $add) $this->add($type, $add, $group);

			} else if (strlen($path)) {

				// pr('ADD - type:'.$type.' / path: '.$path.' / group: '.$group);

				if (strpos($path, 'ttp://') && !strpos($path, $_SERVER['HTTP_HOST'])) {	

					// This is an externally hosted JS. It should not be compressed
					$group = '_raw';
					
				} else {

					// Strip off any cache buster

					$path = (is_bool($this->add_app_path) && $this->add_app_path) ? (CI()->asset_path . '/' . $path) : (SITEPATH . $path);
					$path = reduce_multiples($path, '/');

					// Make sure it exists first, otherwise we don't need this asset
					if (!file_exists(DOCROOT.$path)) {
						pr($path, 'Can not find file:');
						return FALSE;
					}

				}

				$this->_assets[$type][$group][] = $path;

			}

	}
	
	public function addSets($sets=array()) {

			foreach ($sets as $set) {
				
				if (!empty($this->_sets[$set]) && count($this->_sets[$set])) {

					$g 		= explode('_', $set);
					$name	= !empty($g[1]) ? $g[1] : null;
					
					$this->add($g[0], $this->_sets[$set], $name);
				
				}
	
			}	
	
	}

	public function getTags($type=null, $group=null) {

			if (!in_array($type, $this->allowed_types)) show_error('"'.$type.'" is not in the list of allowed asset types.');
			if (is_null($type)) show_error('Could not retrieve asset, invalid type specified.');
			
			$output_default = '';
			$output_page 	= '';

			foreach ($this->_assets[$type] as $group_name => $group_files) {

				if (!is_null($group) && ($group != $group_name)) continue;
	
				$files = $this->_getPaths($group_name, $group_files, $type);

				foreach ($files as $file) {	

					// Ability to add a cache buster to the website config
					if (strlen($this->cache_buster)) {
						$file .= '?'.$this->cache_buster;
					}

					if ($group_name == 'pdf') {
						$output_default .= NL.TAB . sprintf($this->{'_'.$type.'_tag'}, $file, 'all');
					} else if ($group_name == 'page') {
						$output_page .= NL.TAB . sprintf($this->{'_'.$type.'_tag'}, $file, $group_name);
					} else {
						$output_default .= NL.TAB . sprintf($this->{'_'.$type.'_tag'}, $file, $group_name);
					}
				}
	
			}
			
			return $output_default . $output_page;

	}


	public function _getPaths($group_name='', $group_files=array(), $type=null) {

			if ($this->compress_assets === FALSE ) return $group_files;

			if ($group_name == '_raw') return $group_files;

			// TODO: Check to see if the cache folder is writable

			$asset_name = '';

			foreach ($group_files as $file) {
				$asset_name .= '_' . $file . ':' . filemtime(DOCROOT.$file);
			}

			$ext = ($type == 'cssiphone') ? 'css' : $type;

			$cache_file 		= 'asset_' . $type . '_' . $group_name . '_' . md5($asset_name) . '.' . $ext;
			$cache_file_path 	= $this->cache_location . $cache_file;
			
			if (!file_exists(DOCROOT . $cache_file_path)) {
				// Create new cached file
				
				if (!$this->_createCompressedAsset($cache_file, $group_files, $type)) {
					// Something went wrong with the compression. Return the original array
					return $group_files;
				}
			}

			return array($cache_file_path);

	}

	private function _createCompressedAsset($file_name, $files, $type) {
			
			$save_path = DOCROOT . $this->cache_location . $file_name;
				
			$content 	= '';
			$content 	.= '/* File Created: '.standard_date().' */';
	
			foreach ($files as $file) {
				
				if (strpos($file, 'ttp://') && !strpos($file, $_SERVER['HTTP_HOST'])) {
					// REMOTE JS
					return false;
				}
				
				$full_path	= reduce_multiples(DOCROOT.'/'.$file, '/');
				
				$content 	.= NL . NL;
				$content	.= NL . '/* ----------------------------------------------------------------------------';
				$content 	.= NL . ' * Original File: '. basename($file);
				$content 	.= NL . ' */';
				$content 	.= NL . NL;

				$file_contents 	= file_get_contents($full_path);
	
				if (isset($config['assets']['compression_minify']) && ($config['assets']['compression_minify'] === TRUE)) {
					// Hand off to specialized compressors
					switch ($type) {
						case 'js':  
						case 'js_top':  
						case 'js_page':  
						case 'js_bottom':  
							$content .= $this->_compressJS($file_contents, $full_path);
							break;
						case 'iphone_css':  
						case 'css':  
							$content .= $this->_compressCSS($file_contents, $full_path);
							break;
						default:	
							$content .= $file_contents;
							break;
					}
				} else {
					$content .= $file_contents;
				}
					

			}

			$fp = fopen($save_path, FOPEN_READ_WRITE_CREATE);
			fwrite($fp, $content);
			fclose($fp);
			
			chmod($save_path, DIR_READ_MODE); 
			
			return true;	
	
	}
	
	
	private function _compressJS($content='', $file_path) {
			
			// Remove comments, is this really needed?
			// Dont do this, it's braking php from rendering on controls.js of scriptac.
			//$content = preg_replace('</\*([^*\\\\]|\*(?!/))+\*/>x', '', $content);
			
			// Remove tabs
			$content = preg_replace('/\\t/', '', $content);

			// Remove occurances of multiple spaces
			$content = preg_replace('/ {2,}/',' ',$content);

			// Remove empty lines
			$content = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $content);
			
			return $content;
	
	}

	private function _compressCSS($content='', $file_path) {
		
			// TODO: Replace @imports with referenced file
			
			// Convert all referenced path to be absolute
			$content = $this->_makeAbsoluteCSSPaths($content, $file_path);

			// Remove tabs
			$content = preg_replace('/\\t/', '', $content);

			// Remove occurances of multiple spaces
			$content = preg_replace('/ {2,}/',' ',$content);

			// Remove comments
			$content = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $content);

			// Remove line breaking between { }
			$content = preg_replace('/(?=[^{}]*\})\r?\n?/','',$content);

			// Remove empty lines
			$content = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $content);

			return $content;	
	}
	
	
	private function _makeAbsoluteCSSPaths($content=null, $file_path) {
	
			if (!strlen($content)) return $content;

			// Find all occurances of a relativly referenced file in the css
			// Example: url('foo.css');   Will not replace if starts with http:// or /

			preg_match_all('/url\((.*?)\)/is', $content, $matches);
			
			if (count($matches[0])) {

				$base_path			= dirname($file_path);
				$cnt 				= count($matches[0]);
				$replace_current 	= array();
				$replace_new		= array();

				for ($i=0; $i<$cnt; $i++) {
				
					// TODO: Add some control to the realpath check to make sure the file is not out of the document root
				
					$new_file				= realpath($base_path . '/' . str_replace("'", '', $matches[1][$i]));
					$new_file				= reduce_multiples("/" . str_replace(DOCROOT, '', $new_file) . "", '/');
					
					$new_string 			= str_replace($matches[1][$i], $new_file, $matches[0][$i]);
					
					$replace_current[$i] 	= $matches[0][$i];
					$replace_new[$i] 		= $new_string;
					
				}
				
				// TODO: We should also load the referenced file for @load and add it to $content

				$content 	= str_replace($replace_current, $replace_new, $content);											

			}
			
			return $content;
	
	}
	
	
}