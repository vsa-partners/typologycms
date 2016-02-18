<div class="access_buttons clearfix">
	<button class="button button_outline" href="<?=$this->admin_path.$this->module?>/create" onClick="document.location = this.getAttribute('href'); this.blur();"><span>CREATE GROUP</span></button>
</div>

<? if (strlen($output)): ?>

	<ul class="access_menu clear">
		<?=$output?>	
	</ul>

<? endif; ?>
