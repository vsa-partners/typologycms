<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class MY_Upload extends CI_Upload {

	function is_image() {
			// IE will sometimes return odd mime-types during upload, so here we just standardize all
			// jpegs or pngs to the same file type.
	
			$png_mimes  = array('image/x-png');
			$jpeg_mimes = array('image/jpg', 'image/jpe', 'image/jpeg', 'image/pjpeg', 'application/octet-stream');
			
			if (in_array($this->file_type, $png_mimes)) {
				$this->file_type = 'image/png';
			}
			
			if (in_array($this->file_type, $jpeg_mimes)) {
				$this->file_type = 'image/jpeg';
			}
	
			$img_mimes = array(
								'image/gif',
								'image/jpeg',
								'image/png',
							   );
	
			return (in_array($this->file_type, $img_mimes, TRUE)) ? TRUE : FALSE;
	}

	/**
	 * More verbose error message for debugging. Too hard to track down file's mime.
	 */	
	function display_errors($open = '<p>', $close = '</p>') {

			$str = parent::display_errors($open, $close);		
	
			$str .= ' (Type: ' . $this->file_type .' / Ext: '.$this->file_ext.')';
			return $str;
		
	}


	/**
	 * Verify that the filetype is allowed
	 *
	 * @access	public
	 * @return	bool
	 */	
	function is_allowed_filetype() {

			if (count($this->allowed_types) == 0 OR ! is_array($this->allowed_types)) {
				$this->set_error('upload_no_file_types');
				return FALSE;
			}
			

			// Fix for 'application/octet-stream' problem in SWFUpload
			if ($this->file_type == 'application/octet-stream') {
                $mime               = $this->mimes_types(trim($this->file_ext, '.'));
                $this->file_type    = (is_array($mime)) ? $mime[0] : $mime;
			}


            // Match the mime with the actual extension			
			
			$this->file_ext = strtolower($this->file_ext);
	
			if (CI()->CONF['match_mime_to_ext']) {
			
				// This will require that the extension of the file uploaded matches against only the extention's mimes
	
				if (in_array(trim($this->file_ext, '.'), $this->allowed_types)) {

					$mime = $this->mimes_types(trim($this->file_ext, '.'));
	
					if (is_array($mime) && in_array($this->file_type, $mime, TRUE)) {
						return TRUE;
					} else if ($mime == $this->file_type) {
						return TRUE;
					}		
				
				}
	
				return FALSE;
			
			} else {
				return parent::is_allowed_filetype();			
			}
		
	}

}