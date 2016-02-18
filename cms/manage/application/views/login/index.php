<div id="login">

	<div id="logo">
		<?php if (method_exists(CI(),'getAdminLogo')) echo CI()->getAdminLogo(); ?>
	</div>
	
	<h2>Please login</h2>
	
	<? if ($this->session->flashdata('page_message')) : ?>
		<div id="msg"><?=$this->session->flashdata('page_message')?></div>
	<? endif; ?>
	
	<form class="tndr_form" method="post" name="login_form" id="login_form" action="<?=$form_action?>">
	
		<!-- Basic Fields -->
	
		<div class="form_row">
			<label>Username:</label>
			<div class="field"><input class="required" type="text" name="username" /></div>
		</div>
	
		<div class="form_row">
			<label>Password:</label>
			<div class="field"><input class="required" type="password" name="password" /></div>
		</div>
	
		<div class="form_row last">
			<button type="submit" name="LOGIN" class="button"><span>LOGIN</span></button>
		</div>
		
	</form>
	
	
	<? if (!empty($this->ADMIN_CONF['login']['instructions'])) : ?>
		<div class="instructions"><?=$this->ADMIN_CONF['login']['instructions']?></div>
	<? endif; ?>

	<div class="instructions"><a href="<?$this->admin_path?>login/forgot">Forgot Password?</a></div>
	
</div>