<?

if (isset($item['space'])) {

    if ($item['space'] === TRUE) {
    	// This is a spacer row
    	echo '<li class="spacer">&nbsp;</li>';
    }
	
} else if (!isset($item['display']) || ($item['display'] === TRUE)){

	$class 	= ('/' . $this->admin_dir . $item['href'] ==  $this->uri->uri_string()) ? 'data current' : 'data';

	$href 	=  (!empty($item['admin_path']) && ($item['admin_path'] != FALSE)) ? $this->admin_path . $item['href'] : $item['href'];

	echo '<li>'
		.'<div class="'.$class.'"><a href="'.$href.'"><img src="'.$this->asset_path . $item['icon'].'" width="10" height="10"/>  '.$item['title'].'</a></div>'
		.'</li>';

}