<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


/**
 * Typology CMS
 *
 * @author      VSA Partners / Louis D Walch (lwalch@vsapartners.com)
 * @link        http://www.vsapartners.com
 *
 */
 
 
// ------------------------------------------------------------------------

class Site_maintenance {
    
    // ------------------------------------------------------------------------
    
    public function purgeSiteCache() {

            CI()->load->model('cache_model');    
        
            if (CI()->cache_model->purgeOld()) {
                $data = 'Old cache successfully purged.';
            } else {
                $data = 'Hmm, something went wrong. Could not purge cache. Please contact system administrator.';
            }        
            
            return $data;
    }

    public function clearSiteCache() {
        
            CI()->load->model('cache_model');    
        
            if (CI()->cache_model->clearall()) {
                $data = 'Cache successfully cleared.';
            } else {
                $data = 'Hmm, something went wrong. Could not clear cache. Please contact system administrator.';
            }        
            
            return $data;
    }

    public function clearAssetCache($type='all') {
    
            $directory          = DOCROOT . zonepath('cache/');
                
            if (!@is_dir($directory)) show_error('Directory not found.<br/><em>Path: '.$directory.'</em>');

            switch ($type) {
                case 'js':      $file = 'asset_js_*.js';     break;
                case 'css':     $file = 'asset_css_*.css';     break;
                default:        $file = 'asset_*';             break;
            }

            return (shell_exec('rm -fv '.$directory . $file));

    }


    public function clearLoginFails() {
        
            CI()->authentication->clearLoginFail();

            $data = 'Failed login history cleared.';
            
            return $data;
    }
    

    public function clearLogs() {
    
            $directory     = reduce_multiples(DOCROOT.SITEPATH.BASEPATH.'logs/', '/');
    
            if (!@is_dir($directory)) show_error('Directory not found.<br/><em>Path: '.$directory.'</em>');
            
            return (shell_exec('rm -fv '.$directory . 'log-*.php'));

    }

    public function clearTmpFiles() {
    
            $FILE_CONF      = CI()->loadConfig('file');
            $directory      = DOCROOT . zonepath($FILE_CONF['file_directory'].'/'.$FILE_CONF['temp_folder'] . '/');
    
            if (!@is_dir($directory)) show_error('Directory not found.<br/><em>Path: '.$directory.'</em>');
            
            $rm_result     = shell_exec('rm -fv '.$directory . '*.jpg');
            $rm_result     .= shell_exec('rm -fv '.$directory . '*.gif');
            $rm_result     .= shell_exec('rm -fv '.$directory . '*.png');
            
            return ($rm_result);

    }



}