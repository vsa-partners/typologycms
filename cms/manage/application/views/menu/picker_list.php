<? if (strlen($output)): ?>

	<div id="popup_header">Please choose a <?=$this->module?>.</div>

	<div id="popup_content">

		<div id="inner_scroll">
		
			<ul id="picker" class="access_menu">
				<?=$output?>	
			</ul>
			<div class="access_menu_buttons">
				<?
					$js_param = array(
						'id'			=> ''
						, 'title'		=> ''
						, 'destination'	=> $this->input->get_post('destination')
						);
					if (CI()->input->get_post('submitForm')) $js_param['submitForm'] = CI()->input->get_post('submitForm');
				?>		
				<a href="#" onClick='javascript:itemPicker.choose(<?=json_encode($js_param)?>); return false;'>CLEAR CURRENT SELECTION</a>
			</div>
			
		</div>

	</div>

	<script language="javascript">
		document.observe('dom:loaded', function(event) {
			TreeNav.init('picker');
		});
	</script>

<? endif; ?>
