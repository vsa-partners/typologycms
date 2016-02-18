<? if (strlen($output)): ?>

	<div id="popup_header">Please choose an item.</div>

	<div id="popup_content">
		<div id="inner_scroll">
			<ul id="picker" class="access_menu_tree">
				<?=$output?>
			</ul>
		</div>
	</div>

	<script language="javascript">
		document.observe('dom:loaded', function(event) {
			TreeNav.init('picker', {
				'type'			: 'tree'
				, 'menu'		: 'picker' 
				, 'destination'	: '<?=$this->input->get_post('destination')?>'
				, 'select_type'	: '<?=$this->input->get_post('select_type')?>'
			});
		});
	</script>

<? endif; ?>