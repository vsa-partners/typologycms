<div id="login">

	<div id="logo">
		<?php if (method_exists(CI(),'getAdminLogo')) echo CI()->getAdminLogo(); ?>
	</div>

	<?=$this->layout->getMessages();?>

	<h2>Forgot Password</h2>
	
	<? if ($this->session->flashdata('page_message')) : ?>
		<div id="msg"><?=$this->session->flashdata('page_message')?></div>
	<? endif; ?>

	
	<form class="tndr_form" method="post" name="passwordform" id="passwordform" action="<?=$form_action?>">

			<div class="form_row text">
				Fill out the form below to reset the password on your account. Your new password will be emailed to the address we have on file.
			</div>		
		
			<div class="form_row">
				<label>Username:</label>
				<div class="field"><input type="text" name="fields[user]" /></div>
			</div>

			<div class="form_row">
				<label></label>
				<div class="field" style="padding-bottom: 3px;">or</div>
			</div>

			<div class="form_row">
				<label>Email:</label>
				<div class="field"><input type="text" name="fields[email]" /></div>
			</div>
		
			<div class="form_row last">
				<button type="submit" class="button"><span>RESET</span></button> <button type="button" class="button button_outline" onClick="document.location = this.getAttribute('href');" href="<?=$this->admin_path?>"><span>CANCEL</span></button>
			</div>
			
		</form>

	</div>
</div>
