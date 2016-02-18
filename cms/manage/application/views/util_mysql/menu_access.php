<? if (count($this->tables)): ?>
	
	<ul class="access_menu clear">
		<? foreach ($this->tables as $table) {
		
			$row_class 	= ($table == $this->current_table) ? 'data current' : 'data';
		
			echo '<li>'
				.'<div class="'.$row_class.'">'
				.'<a href="'.$this->admin_path.$this->module_path.'/'.$table.'"><img src="'.$this->asset_path.'/img/mini_icons/database.gif" width="10" height="10"/> '.$table.'</a>'
				.'</div>'
				.'</li>'
				.NL;
			}
			
			
			$query_row_class 	= ('query' == $this->current_table) ? 'data current' : 'data';
			
		?>

		<li>
			<div class="<?=$query_row_class?>"><a href="<?=$this->admin_path.$this->module_path.'/query'?>"><img src="<?=$this->asset_path?>/img/mini_icons/field_input.gif" width="10" height="10"/> Raw Query</a></div>
		</li>

	</ul>
	
<? endif; ?>