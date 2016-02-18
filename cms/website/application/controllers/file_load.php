<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Typology CMS
 *
 * @author      VSA Partners / Louis D Walch (lwalch@vsapartners.com)
 * @link        http://www.vsapartners.com 
 *
 *
 *
 * Params: 
 * w = Width
 * h = Height
 * m = Mode
 *      e - Exact Fit / Crop
 *      p - Fit to size / Padded
 *      w - Fit within (Default)
 * c = Crop Type
 *      t - Top Center
 *      tl - Top Left
 *      c - Center (Default)
 * p = Preview Image
 *      5 bit preview image, always set value to 1       
 * bw = Grayscale Image
 *      5 bit preview image, always set value to 1       
 * s = Sharpen Image
 *      Set value to level of sharpness. Lower the number the more sharp it will be. 17 usually works well.
 */
 
 
require_once(APPPATH . 'controllers/application.php');

class File_load extends Application {

    var $zone           = 'website';

    var $module         = 'file';
    var $id_field       = 'file_id';
    
    var $show_cached    = TRUE;

    var $image_type     = null;
    var $image_h        = null;
    var $image_w        = null;
    
    var $jpg_quality    = 75;



    // ------------------------------------------------------------------------

    function __construct() {
            parent::__construct();

            $this->MODULE_CONF  = $this->loadConfig('file');

            $this->load->model('file_model');
            $this->load->helper('file');
            
            ini_set('memory_limit', '100M');
            
            if (!empty($this->MODULE_CONF['jpg_quality'])) {
                $this->jpg_quality = $this->MODULE_CONF['jpg_quality'];
            }

    }


    // ------------------------------------------------------------------------

    public function _remap() {
    
    
            $uri    = $this->uri->segment_array();
            $id     = round(floatval($uri[2]));                                 // Do all the extra stuff to deal with possible extension
            $name   = !empty($uri[3]) ? $uri[3] : null;
    
            if (is_null($id)) $this->_show404();

            // Turn off the output profiler. Just in case.
            $this->output->enable_profiler(FALSE);

            // Load file record from db
            $file       = $this->file_model->first()->getById($id);
            
            // Is this a real file?
            if (!count($file) || empty($file['server_path']))  $this->_show404(); 
            if ($this->MODULE_CONF['force_name_in_uri'] && ($name != $file['file_name'] . $file['ext'])) $this->_show404(); 

            // Make sure file exists on disk
            if (!file_exists($file['server_path'])) $this->_show404(); 

            // Get file path to load, this will generate thumbnail versions
            $load_path = $this->_getFilePath($file);
            
            // Make sure file exists on disk
            if (!file_exists($load_path)) $this->_show404(); 

            // Could this file come from cache?
            $modified = (!empty($_SERVER['HTTP_IF_MODIFIED_SINCE'])) ? $_SERVER['HTTP_IF_MODIFIED_SINCE'] : false;
            
            if ($modified && (strtotime($modified) == filemtime($load_path))) {

                // Client's cache IS current, so we just respond '304 Not Modified'.
                header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($load_path)).' GMT', true, 304);
            
            } else {
            
                // Image not cached or cache outdated, we respond '200 OK' and output the image.
                header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($load_path)).' GMT', true, 200);
                header('Content-type: '.$file['mime']);

                if (isset($_SERVER['HTTP_RANGE'])) {
                        
                    // For iOS devices, serve with byte-range   
                    
                    return $this->_rangeDownload($load_path);
        
                } else {

                    // If we need to handle very large files, could try flushing the output buffer on every loop
                    
                    header('Content-Length: '. filesize($load_path));

                    if ($file['ext'] == '.pdf') {
                        header('Content-Disposition: attachment; filename="'.$file['file_name'].$file['ext'].'"');
                    }
    
                    $handle = fopen($load_path, "rb");
                    while (!feof($handle)) {
                      echo fread($handle, 8192);
                    }
                    fclose($handle);

                }
                
            }
            
            // No need to keep going
            exit();

    }


    private function _rangeDownload($file) {
        $fp = @fopen($file, 'rb');

        $size   = filesize($file); // File size
        $length = $size;           // Content length
        $start  = 0;               // Start byte
        $end    = $size - 1;       // End byte
        // Now that we've gotten so far without errors we send the accept range header
        /* At the moment we only support single ranges.
         * Multiple ranges requires some more work to ensure it works correctly
         * and comply with the spesifications: http://www.w3.org/Protocols/rfc2616/rfc2616-sec19.html#sec19.2
         *
         * Multirange support annouces itself with:
         * header('Accept-Ranges: bytes');
         *
         * Multirange content must be sent with multipart/byteranges mediatype,
         * (mediatype = mimetype)
         * as well as a boundry header to indicate the various chunks of data.
         */
        header("Accept-Ranges: 0-$length");
        // header('Accept-Ranges: bytes');
        // multipart/byteranges
        // http://www.w3.org/Protocols/rfc2616/rfc2616-sec19.html#sec19.2
        if (isset($_SERVER['HTTP_RANGE'])) {
                $c_start = $start;
                $c_end   = $end;
                // Extract the range string
                list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
                // Make sure the client hasn't sent us a multibyte range
                if (strpos($range, ',') !== false) {
                        // (?) Shoud this be issued here, or should the first
                        // range be used? Or should the header be ignored and
                        // we output the whole content?
                        header('HTTP/1.1 416 Requested Range Not Satisfiable');
                        header("Content-Range: bytes $start-$end/$size");
                        // (?) Echo some info to the client?
                        exit;
                }
                // If the range starts with an '-' we start from the beginning
                // If not, we forward the file pointer
                // And make sure to get the end byte if spesified
                if ($range{0} == '-') {
                        // The n-number of the last bytes is requested
                        $c_start = $size - substr($range, 1);
                } else {
                        $range  = explode('-', $range);
                        $c_start = $range[0];
                        $c_end   = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $size;
                }
                /* Check the range and make sure it's treated according to the specs.
                 * http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
                 */
                // End bytes can not be larger than $end.
                $c_end = ($c_end > $end) ? $end : $c_end;
                // Validate the requested range and return an error if it's not correct.
                if ($c_start > $c_end || $c_start > $size - 1 || $c_end >= $size) {
                        header('HTTP/1.1 416 Requested Range Not Satisfiable');
                        header("Content-Range: bytes $start-$end/$size");
                        // (?) Echo some info to the client?
                        exit;
                }
                $start  = $c_start;
                $end    = $c_end;
                $length = $end - $start + 1; // Calculate new content length
                fseek($fp, $start);
                header('HTTP/1.1 206 Partial Content');
        }
        // Notify the client the byte range we'll be outputting
        header("Content-Range: bytes $start-$end/$size");
        header("Content-Length: $length");

        // Start buffered download
        $buffer = 1024 * 8;
        while(!feof($fp) && ($p = ftell($fp)) <= $end) {
                if ($p + $buffer > $end) {
                        // In case we're only outputtin a chunk, make sure we don't
                        // read past the length
                        $buffer = $end - $p + 1;
                }
                set_time_limit(0); // Reset time limit for big files
                echo fread($fp, $buffer);
                flush(); // Free up memory. Otherwise large files will trigger PHP's memory limit.
        }

        fclose($fp);

    }    


    private function _getFilePath($file=null) {

            // If this is not an image just return path in Database
            if ($file['is_image'] != 1) return $file['server_path'];
                
            $file_path  = $file['server_path'];
            $_get       = !empty($_GET) ? $_GET : array();
            
            $this->_setFileProps($file);
            
            if (count($_get)) {
                
                $file_path_params   = '';
                
                $request_w          = $this->input->get('w');     // Width
                $request_h          = $this->input->get('h');     // Height
                $request_m          = $this->input->get('m');     // Mode
                $request_p          = $this->input->get('p');     // Preview (black/white, 8 bit)
                $request_bw         = $this->input->get('bw');    // Grayscale
                $request_c          = $this->input->get('c');     // Crop
                $request_s          = $this->input->get('s');     // Sharpen
                
                if ($request_w && !empty($this->MODULE_CONF['max_width']) && ($request_w > $this->MODULE_CONF['max_width']))
                    show_error('Invalid request. Resize width above allowed size.');

                if ($request_h && !empty($this->MODULE_CONF['max_height']) && ($request_h > $this->MODULE_CONF['max_height']))
                    show_error('Invalid request. Resize height above allowed size.');

                if ($request_w) $file_path_params .= '_w' . $request_w;
                if ($request_h) $file_path_params .= '_h' . $request_h;
                if ($request_m) $file_path_params .= '_m' . $request_m;
                if ($request_p) $file_path_params .= '_p' . $request_p;
                if ($request_c) $file_path_params .= '_c' . $request_c;
                if ($request_s) $file_path_params .= '_s' . $request_s;
                if ($request_bw) $file_path_params .= '_bw';

                $new_file_path = $file['base_path'] . $file_path_params . $file['ext'];
                
                // Does the file exist and make sure the original has not been updated since
                if ($this->show_cached && file_exists($new_file_path) && (filemtime($new_file_path) > filemtime($file_path))) {
                
                    // Loading existing file from disk
                    return $new_file_path;
    
                } else {
                
                    // Create a new file                

                    if (!is_writable(dirname($new_file_path))) {
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

                    $crop       = false;

                    $mode       = $this->input->get('m');

                    $output_w   = $this->input->get('w');
                    $output_h   = $this->input->get('h');

                    $scale_w    = $output_w;
                    $scale_h    = $output_h;

                    $file_w     = $file['options']['image_width'];
                    $file_h     = $file['options']['image_height'];


                    if ($output_w && $output_h) {
                        
                        if ($mode == 'p') {
                            
                            // p = Fit to size, padded

                            $color      = 'ff00ff';
                            $color      = $this->_rgb2array($color);

                            $scale      = min($output_w/$file_w, $output_h/$file_h);
                            $crop_w     = floor($scale*$file_w);
                            $crop_h     = floor($scale*$file_h);

                            $crop_x     = $file_w-($scale*$output_w);
                            $crop_y     = $file_h-($scale*$output_h);
                           
                            $crop       = ImageCreateTrueColor($output_w, $output_h);

                            $fill       = ImageColorAllocate($crop, $color[0], $color[1], $color[2]);
                            ImageFill($crop, 0, 0, $fill);

                            ImageCopy($crop, $src_img, 0, 0, ($crop_x/2), ($crop_y/2), $crop_w, $crop_h);                       

                            header('Content-Type: image/jpeg');
                            imagejpeg($crop, NULL, $this->jpg_quality);
                            
                            exit();

                            /*
                            pr($scale, 'scale');
                            pr($crop_x, 'crop_x');
                            pr($crop_y, 'crop_y');
                            pr($crop_w, 'crop_w');
                            pr($crop_h, 'crop_h');
                
                            // crop the middle part of the image to fit proportions
                            ImageCopy($crop, $src_img, 0, 0, ($crop_x/2), ($crop_y/2), $crop_w, $crop_h);                       
                            
                            exit();
                            */
                        
                        } else if ($mode == 'e') {
                        
                            // e = Exact Fit / Crop
    
                            $scale  = min($file_w/$output_w, $file_h/$output_h);
                
                            $crop_x = $file_w-($scale*$output_w);
                            $crop_y = $file_h-($scale*$output_h);
                            $crop_w = $file_w-$crop_x;
                            $crop_h = $file_h-$crop_y;
                           
                            $crop   = ImageCreateTrueColor($crop_w, $crop_h);
                
                            if ($request_c == 't') {
                                // crop the top center part of the image to fit proportions
                                ImageCopy($crop, $src_img, 0, 0, 0, ($crop_y/2), $crop_w, $crop_h);                       
                            } else if ($request_c == 'tl') {
                                // crop the top left part of the image to fit proportions
                                ImageCopy($crop, $src_img, 0, 0, 0, 0, $crop_w, $crop_h);                       
                            } else {
                                // crop the middle part of the image to fit proportions
                                ImageCopy($crop, $src_img, 0, 0, ($crop_x/2), ($crop_y/2), $crop_w, $crop_h);                       
                            }
                        
                        } else {
                        
                            // w = Fit within

                            $scale      = min($output_w/$file_w, $output_h/$file_h);
                            $output_w   = floor($scale*$file_w);
                            $output_h   = floor($scale*$file_h);

                        }
                        
                    } else if ($output_h) {
                        $output_w   = ($output_h * 100/$file_h) * $file_w/100;
                    } else if ($output_w) {
                        $output_h   = ($output_w * 100/$file_w) * $file_h/100;
                    }
            

                    // Make scaled image
                    $dest_img = ImageCreateTrueColor($output_w, $output_h);
            
                    if ($crop) {
                        // Create new image from cropped source
                        ImageCopyResampled($dest_img, $crop, 0, 0, 0, 0, $output_w, $output_h, $crop_w, $crop_h);
                        ImageDestroy($crop);
                    } else {
                        // Create new image from original source
                        ImageCopyResampled($dest_img, $src_img, 0, 0, 0, 0, $output_w, $output_h, $file_w, $file_h);
                    }


                    // Adjust image quality
                    if ($request_bw) {
                        
                        // Greyscale and adjust contrast
                        imagefilter($dest_img, IMG_FILTER_CONTRAST, 5);
                        imagefilter($dest_img, IMG_FILTER_GRAYSCALE);

                        $dest_copy  = imagecreatetruecolor($output_w, $output_h);

                        imagecopy($dest_copy, $dest_img, 0, 0, 0, 0, $output_w, $output_h);
                        imagetruecolortopalette($dest_img, $dither, $colors);
                        imagecolormatch($dest_copy, $dest_img);
                        imagedestroy($dest_copy);           

                    }
                    
                    
                    // Adjust image quality
                    if ($request_p) {
                        
                        // Force serving as GIF
                        $this->image_type = 1;
                        
                        // Greyscale and adjust contrast
                        imagefilter($dest_img, IMG_FILTER_CONTRAST, -30);
                        imagefilter($dest_img, IMG_FILTER_GRAYSCALE);

                        // Reduce color palette
                        // http://www.php.net/manual/en/function.imagetruecolortopalette.php
                        // zmorris at zsculpt dot com (17-Aug-2004 06:58)
                        $colors     = 5;
                        $colors     = max(min($colors, 256), 2);
                        $dither     = TRUE;
                        $dest_copy  = imagecreatetruecolor($output_w, $output_h);

                        imagecopy($dest_copy, $dest_img, 0, 0, 0, 0, $output_w, $output_h);
                        imagetruecolortopalette($dest_img, $dither, $colors);
                        imagecolormatch($dest_copy, $dest_img);
                        imagedestroy($dest_copy);           

                    }

                    // Sharpen Image
                    if ($request_s) {
                        
                        $matrix     = array(
                            array(-1.2, -1, -1.2),
                            array(-1, $request_s, -1),
                            array(-1.2, -1, -1.2),
                        );
                    
                        $divisor    = array_sum(array_map('array_sum', $matrix));
                        $offset     = 0;
                        
                        imageconvolution($dest_img, $matrix, $divisor, $offset);
                    
                    }
                    
                    switch ($this->image_type) {
                        case 1:
                            imagegif($dest_img, $new_file_path);
                            break;
                        case 2:
                            imagejpeg($dest_img, $new_file_path, $this->jpg_quality);
                            break;
                        case 3:
                            imagepng($dest_img, $new_file_path);
                            break;
                        default:
                            break;
                    }
            
                    ImageDestroy($dest_img);
                    ImageDestroy($src_img);
                    
                    
                }   
                
                // Return the newly created file path
                return $new_file_path;
            
            }
            
            
            return $file_path;

    }
    
    
    private function _rgb2array($rgb) {
        return array(
            base_convert(substr($rgb, 0, 2), 16, 10),
            base_convert(substr($rgb, 2, 2), 16, 10),
            base_convert(substr($rgb, 4, 2), 16, 10),
        );
    }
    
    

    function _setFileProps($file) {
    
        $vals   = @getimagesize($file['server_path']);
        $types  = array(1 => 'gif', 2 => 'jpeg', 3 => 'png');
        $mime = (isset($types[$vals['2']])) ? 'image/'.$types[$vals['2']] : 'image/jpg';

        $this->image_w      = $vals['0'];
        $this->image_h      = $vals['1'];
        $this->image_type   = $vals['2'];
        
    }


    
    protected function _show404() {
            show_404(); 
            exit('Page not found');
    }


}