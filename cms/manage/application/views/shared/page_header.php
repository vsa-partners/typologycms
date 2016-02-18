	<div id="header" class="clearfix">	
	
		<div class="title left">
			<?=$this->SITE_CONF['site_title'] . ' Admin' ?>
		</div>

		
		<? foreach(array_reverse($this->ADMIN_CONF['modules']) as $module => $title) {
			
				if ($this->authentication->hasPermission('module_' . $module)) {
					echo '<a class="right" href="'.$this->admin_path.$module.'">'.$title.'</a>';
				}
			}
		?>	
		
		

	</div>