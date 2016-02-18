<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');



exit('Not used');











/**
 * Typology CMS
 *
 * @author      VSA Partners / Louis D Walch (lwalch@vsapartners.com)
 * @link        http://www.vsapartners.com
 *
 */
 
 
require_once(APPPATH . 'models/core/application_model.php');

class Page_calendar_event_model extends Application_model {

	var $table 		= 'page_calendar_event';
	var $id_field	= 'event_id';
	
	var $sort_field = array('start_date', 'start_time');

    // ------------------------------------------------------------------------

    function __construct() {	
    		parent::__construct();
			CI()->load->helper('time');
    }
    
    
    public function getEvents($page_id, $date=null) {

		// TODO: Add where statement to only fetch events in the future

		if (!is_null($date)) {
			$this->db->where("LEFT(start_date, ".strlen($date).") = '".$date."'");
		}
    	
    	return $this->get(array('page_id'=>$page_id));
    
    }


	// Putting this in here because view can not call controller. Ghetto i know.
	public function drawDateBrowser() {
		
		$output = '';

		// Month
		$output .= '<select class="left date" name="month">';
		foreach ($this->MODULE_CONF['browse_months'] as $month_val => $month) {
			$output .= '<option value="'.$month_val.'"'
					. (($this->date['month'] == $month_val) ? ' SELECTED' : '')
					. '>'.$month.'</option>';		
		}
		$output .= '</select>';

		// Year
		$output .= '<select class="left date" name="year">';
		foreach ($this->MODULE_CONF['browse_years'] as $year_val => $year) {
			$output .= '<option value="'.$year_val.'"'
					. (($this->date['year'] == $year_val) ? ' SELECTED' : '')
					. '>'.$year.'</option>';		
		}
		$output .= '</select>';
	
		return $output;
	
	}
	
	
    // ------------------------------------------------------------------------
	// EXTENDED METHODS
	
	public function update($fields) {
	
		if (empty($fields['all_day'])) {
			$fields['all_day'] 		= '0';
			$fields['ignore_end'] 	= '0';
		} else {
			$fields['start_time']	= '';
			$fields['end_time']		= '';
		}

		if (empty($fields['ignore_end'])) $fields['ignore_end'] = '0';
	
		if (!empty($fields['start_time']) && is_array($fields['start_time'])) 
			$fields['start_time'] = timeFromArray($fields['start_time']);

		if (!empty($fields['end_time']) && is_array($fields['end_time'])) 
			$fields['end_time'] = timeFromArray($fields['end_time']);
		
		
		return parent::update($fields);
		
	
	} 
	
	public function get($fields, $select_set=FALSE) {
			
			if (!empty($fields['start_date'])) {
				 $this->db->where("LEFT(start_date, ".strlen($fields['start_date']).") = '".$fields['start_date']."'");
				 unset($fields['start_date']);
			}

			CI()->load->model('file_model');
			
			return parent::get($fields, $select_set);

	}
	

	// Make model specific updates to result array
	protected function _processGetRow($row=array()) {

			if (!empty($row['start_time'])) {
				$row['start_time'] 			= timeToArray($row['start_time']);
				$row['start_time_display'] 	= timeArrayToDisplay($row['start_time']);
			}

			if (!empty($row['end_time'])) {
				$row['end_time'] 			= timeToArray($row['end_time']);
			}

			if (!empty($row['start_date'])) {
				$row['start_date_display']		= date('M jS', strtotime($row['start_date']));
			}
			
			if (!empty($row['image'])) {

				// Loadthe image
				$row['image_details'] = CI()->file_model->first()->getById($row['image']);
			
			}
			
			return $row;

	}


	public function getEmptyItem() {
		$item = parent::getEmptyItem();
		$item['page_id'] = $this->current_id;
		$item['start_time'] = array(
			'hour' 		=> ''
			, 'min' 	=> ''
			, 'ampm' 	=> ''
		);
		$item['end_time'] = array(
			'hour' 		=> ''
			, 'min' 	=> ''
			, 'ampm' 	=> ''
		);
		$item['ignore_end']	= 1;

		return $item;
	}

}