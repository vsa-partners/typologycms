<!DOCTYPE html>
<!--[if IE 8]> <html class="lt-ie10 lt-ie9" lang="en" <?=$this->layout->getBodyClass();?>> <![endif]-->
<!--[if IE 9]> <html class="lt-ie10" lang="en" <?=$this->layout->getBodyClass();?>> <![endif]-->
<!--[if gt IE 9]><!--> <html lang="en" <?=$this->layout->getBodyClass();?>> <!--<![endif]-->
<head>
	<title><?=$this->layout->getTitle();?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="Content-Language" content="EN" />
	<meta name="MSSmartTagsPreventParsing" content="true" />
	<meta http-equiv="imagetoolbar" content="no" />
	<meta name="robots" content="index, follow"/>
	<meta name="googlebot" content="index, follow"/>
	<link rel="shortcut icon" href="<?=SITEPATH?>favicon.ico" />
	<?=$this->layout->getHead();?>

</head>

<body <?=$this->layout->getBodyId();?> <?=$this->layout->getBodyClass();?> <?=$this->layout->getBodyScript();?>>
<?=$this->layout->getRegionData('content');?>
<?=$this->layout->getFoot();?>	
</body>
</html>