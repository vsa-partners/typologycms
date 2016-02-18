<div id="login">

		<h2>Please login</h2>
		
		<?=$this->layout->getMessages();?>
		
		<form method="post" name="login_form" id="login_form" action="<?=SITEPATH.$this->request_path?>">
		
			<!-- Basic Fields -->
		
			<div class="form_node">
				<label>Username:</label>
				<div class="field"><input class="required" type="text" name="secure_username" /></div>
			</div>
		
			<div class="form_node">
				<label>Password:</label>
				<div class="field"><input class="required" type="password" name="secure_password" /></div>
			</div>
	
			<div class="form_node last">
				<input type="submit" name="LOGIN" value="LOGIN" />
			</div>
			
			<script language="javascript">
				document.observe('dom:loaded', function(event) {
					FormNodeValidation.init('login_form', null);
				});
			</script>
	
		</form>
	
	</div>
</div>