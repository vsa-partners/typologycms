<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title><?=$this->layout->getTitle();?></title>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8" />
	<meta name="MSSmartTagsPreventParsing" content="true" />
	<meta http-equiv="imagetoolbar" content="no" />
	<?=$this->layout->getHead();?>

	<?php if (method_exists(CI(),'getThemeCss')) echo CI()->getThemeCss(); ?>

</head>
<body <?=$this->layout->getBodyId();?> <?=$this->layout->getBodyClass();?>>

	<!-- START DATA OUTPUT -->
	<?=$this->layout->getRegionData('content');?>
	<!-- END DATA OUTPUT -->	

</body>
</html>