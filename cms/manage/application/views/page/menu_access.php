<? if (!empty($output) && strlen($output)): ?>
	
	<div id="access_menu_tree_wrapper">
	
		<ul class="access_menu_tree">
			<?=$output?>	
		</ul>

		<div class="tree_buttons hide">
			<? if ($this->hide_redirects == TRUE): ?>
				<a href="<?=$this->admin_path . $this->module?>?toggle_redirects=show">Show Redirects</a>
			<? else: ?>
				<a href="<?=$this->admin_path . $this->module?>?toggle_redirects=hide">Hide Redirects</a>
			<? endif; ?>
			<br/>
			<a href="<?=$this->admin_path . $this->module?>?collapse=1&menu=access">Collapse Menu</a>
		</div>

		<script language="javascript">
			document.observe('dom:loaded', function(event) {
				TreeNav.init('access_menu_tree_wrapper',{'type':'tree', 'menu':'access'});
			});
		</script>
	
	</div>
	
<? endif; ?>


<ul class="access_menu clear" style="margin-top: 2px;">
	<li><div class="data"><div class="label"><a href="<?=($this->admin_path.$this->module)?>/report/activity"><img width="10" height="10" src="<?=$this->asset_path?>img/mini_icons/graph.gif"/> Page Activity Report</a></div></div></li>
	<li><div class="data"><div class="label"><a href="<?=($this->admin_path.$this->module)?>/report/pending"><img width="10" height="10" src="<?=$this->asset_path?>img/mini_icons/graph.gif"/> Pending Pages Report</a></div></div></li>
	<li><div class="data"><div class="label"><a href="<?=($this->admin_path.$this->module)?>/report/draft"><img width="10" height="10" src="<?=$this->asset_path?>img/mini_icons/graph.gif"/> Draft Pages Report</a></div></div></li>
	<li><div class="data"><div class="label"><a href="<?=($this->admin_path)?>page_import"><img width="10" height="10" src="<?=$this->asset_path?>img/mini_icons/plugin.gif"/> Import Tool</a></div></div></li>
</ul>
