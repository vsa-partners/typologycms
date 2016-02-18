<? if (strlen($output)): ?>

	<ul class="access_menu">
		<?=$output?>	
	</ul>

	<div class="tree_buttons">(<a href="<?=$this->admin_path . $this->module?>/collapse_menu">Collapse Menu</a>)</div>

	<script language="javascript">
		document.observe('dom:loaded', function(event) {
			TreeNav.init('aside');
		});
	</script>

<? endif; ?>
