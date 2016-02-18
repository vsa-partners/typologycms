<div class="access_buttons clearfix">
	<button type="button" class="button button_outline" href="<?=$this->admin_path.$this->module?>/create" onClick="document.location = this.getAttribute('href')"><span>CREATE CONFIG</span></button>
</div>


<? if (strlen($output)): ?>

	<ul class="access_menu">
		<?=$output?>	
	</ul>

<? endif; ?>
