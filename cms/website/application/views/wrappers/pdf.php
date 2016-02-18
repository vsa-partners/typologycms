<!DOCTYPE html> 
<html lang="en"> 
<head> 
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" /> 
	<title><?=$this->layout->getTitle();?></title>
	<?=$this->layout->getHead();?>
</head>
<body <?=$this->layout->getBodyId();?> <?=$this->layout->getBodyClass();?>>
<?=$this->layout->getRegionData('content');?>
<?=$this->layout->getFoot();?>	
</body>
</html>