<!-- START PAGE CREATE -->
<div class="content_header"><h2>Create</h2></div>

<form class="tndr_form" method="post" name="editForm" id="editForm" action="<?=$this->admin_path.$this->module?>/update/-1">

	<input type="hidden" name="fields[parent_id]" value="<?=$parent[$this->id_field]?>" />
	<input type="hidden" name="fields[parent_path]" value="<?=$parent['path']?>" />
	<input type="hidden" name="fields[module]" value="<?=$this->module?>" />
	<input type="hidden" name="fields[page_id]" value="-1" />

	<div class="form_row">
		<label>Title:</label>
		<div class="field"><input type="text" name="fields[title]" class="required" /></div>
	</div>

	<div class="form_row">
		<label>Type:</label>
		<div class="field">
			<? if (count($allowed) == 1): ?>
				<div class="field_text">
					<? foreach ($allowed as $t_key => $t_val) echo '<input type="hidden" name="fields[type]" value="'.$t_key.'" />' . $t_val; ?>
				</div>
			<? else: ?>
				<?=form_dropdown('fields[type]', $allowed, $copy_type, 'class="toggle_hide" hideif="redirect,mirror_page,mirror_section,mirror_calendar" tohide="fields_template_id"')?>
			<? endif; ?>
		</div>
	</div>

	<div class="form_row" id="fields_template_id">

		<label>Template:</label>
		<div class="field">

			<? if (count($templates) == 1): ?>
				<div class="field_text">
					<? foreach ($templates as $t_key => $t_val) echo '<input type="hidden" name="fields[template_id]" value="'.$t_key.'" />' . $t_val; ?>
				</div>
			<? else: ?>
				<?=form_dropdown('fields[template_id]', $templates, $copy_template)?>
			<? endif; ?>

		</div>

	</div>

	<button name="submit_draft" value="update_draft" type="submit" class="button"><span>CREATE</span></button>

</form>

<!-- END PAGE CREATE -->