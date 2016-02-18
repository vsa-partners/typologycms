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

	<div id="container">

		<div id="site_error_ie6" class="site_error"><div class="site_error_inner">This application has not been optimized for your web browser (Microsoft Internet Explorer 6), you may notice feature inconsistencies. We strongly encourage use of Mircosoft Internet Explorer 7 or Firefox.</div></div>
		<noscript><div class="site_error"><div class="site_error_inner"><p><strong>ERROR</strong><p><p>Javascript is required for this application to function properly. Please enable Javascript in your browser. If you feel you have reached this in error,or don't know what I am talking about, please contact your system administrator.</p></div></div></noscript>
		
		<div id="layout">
		
			<div id="side_col">
				<div id="side_col_top">
					<div id="side_col_bottom">
						<div id="logo"><?php if (method_exists(CI(),'getAdminLogo')) echo CI()->getAdminLogo(); ?></div>
						<div id="menu"><?=$this->layout->getRegionData('sidebar');?></div>
					</div>
				</div>
			</div>
				
			<div id="main_col">
	
				<?=$this->layout->getMessages();?>
	
				<!-- START DATA OUTPUT -->
				<?=$this->layout->getRegionData('content');?>
				<!-- END DATA OUTPUT -->	
		
			</div> <!-- /#main_col -->	
			
			<div class="clear"> </div>
		
		</div> <!-- /#layout -->	
	
		<div id="page_footer">
			<div class="tndr"><a href="http://www.vsapartners.com" target="_blank"><img src="<?=$this->asset_path?>img/vsa_logo.png" alt="VSA Partners"/></a></div>
		</div>
	
	</div>

	<div id="status_bar">
	
		<div class="status_bar_menu">
		
			<? foreach($this->ADMIN_CONF['modules'] as $module => $title) {					
					if ($this->authentication->hasPermission('module_' . $module)) {
						$current = ($this->module == $module) ? 'current' : '';
						echo '<a class="status_bar_button left '.$current.'" href="'.$this->admin_path.$module.'">'.$title.'</a>';
					}
				}
			?>	
		</div>

		<div class="publish_info">Next publish at: <?=CI()->nextPublishTime();?></div>

		<a class="status_bar_button right" href="<?=$this->admin_path?>login/logout">Log out</a>
		<a class="status_bar_button right" href="<?=$this->admin_path?>login/change">Change Password</a>	
		<div class="user_info">Hello <?=$this->authentication->get('user');?>, <?=$this->authentication->get('permission_group');?></div>

	</div>

	
	<? if ($this->authentication->isLoggedIn() && ($this->ADMIN_CONF['login']['expiration_mins'] > 1)) {?>
		<script language="javascript">
			new PeriodicalExecuter(function(pe) {
				document.location = '<?=$this->admin_path?>login/expire';
				pe.stop();
			}, <?=$this->ADMIN_CONF['login']['expiration_mins'] * 60?>);
		</script>
	<? } ?>	

</body>
</html>