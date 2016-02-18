<div class="content_header">
	<h2>Change Password</h2>
</div>

<form class="tndr_form" method="post" name="passwordform" id="passwordform" action="<?=$form_action?>">

	<div class="form_row">
		<label>Current Password:</label>
		<div class="field"><input class="required" type="password" name="fields[password_current]" /></div>
	</div>

	<div class="form_row">
		<label>New Password:</label>
		<div class="field"><input class="required validate_minlength" minlength="<?=$this->ADMIN_CONF['login']['req_password_len']?>" type="password" name="fields[password_new]" title="New Password" /></div>
	</div>

	<div class="form_row">
		<label>New Password: (Confirm)</label>
		<div class="field"><input class="required validate_confirm" confirm_with="fields[password_new]" type="password" name="fields[password_confirm]" /></div>
	</div>

	<div class="form_row last">
		<button type="submit" name="SUBMIT" class="button"><span>CHANGE</span></button>
	</div>
	

</form>