<?
header('Content-Type: text/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="UTF-8" standalone="no" ?>';

if(isset($this->layout->preview) && $this->layout->preview) {	
	echo '<!-- WARNING: You are viewing a preview of this page, this should not be enabled for production. -->';
}

//$content = $this->layout->getRegionData('content');
//echo str_replace(NL, '', $content);

?><?=$this->layout->getRegionData('content');?>