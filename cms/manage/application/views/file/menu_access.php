<div class="access_buttons clearfix">
	<button type="button" class="button button_outline" onClick="document.location = this.getAttribute('href')" href="<?=$this->admin_path.$this->module.'/create/collection/'.$this->CONF['access_menu']['model_param']['parent_id']?>" onClick="this.blur();"><span>CREATE FOLDER</span></button>
</div>

<ul class="access_menu clear">
	<?

		if ($this->CONF['access_menu']['model_param']['parent_id'] != $this->_default_parent_id) {
			echo '<li><div class="data">'
				.'<a href="'.$this->admin_path.$this->module.'/edit/'.$this->_current_parent_id.'"><img src="'.$this->asset_path . 'img/mini_icons/arrow_parent.gif"/> Parent Folder</a>'
				.'</div></li>';
		}
	?>
	<? if (strlen($output)) echo $output; ?>
</ul>
