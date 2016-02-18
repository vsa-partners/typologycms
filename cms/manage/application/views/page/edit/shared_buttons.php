	<div id="page_edit_buttons">

		<button id="submit_draft" name="submit_draft" value="update_draft" class="button button_outline" type="submit"><span>SAVE DRAFT</span></button>

		<? if ($this->authentication->hasPermission('global_publish')): ?>
			<button name="submit_publish" value="uppublish_date" type="submit" class="button"><span>SAVE & PUBLISH</span></button>
		<? else: ?>
			<button name="submit_request" value="update_request" type="submit" class="button"><span>SAVE FOR APPROVAL</span></button>
		<? endif; ?>

	
	</div>

	<? if ($this->authentication->hasPermission('debug')): ?>
		<div class="clear"> </div>
		<fieldset class="closed" id="set_debug">
			<legend><a href="#" onClick="TNDR.Form.Actions.toggleFieldset('set_debug'); return false;">Debug</a></legend>
			<div class="form_row">
				<? pr($fields, 'Page Fields', TRUE, TRUE); ?>
			</div>
		</fieldset>
	<? endif; ?>
