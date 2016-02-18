<div id="website_login">

		<h3>This website is protected by a password. Please login below to view.</h3>
		
		<?=$this->layout->getMessages();?>
		
		<form method="post" name="login_form" id="login_form" action="<?=$this->request_path?>">
		
			<!-- Basic Fields -->
		
			<div class="form_node">
				<label>Password:</label>
				<div class="field"><input class="required" type="password" name="password" /></div>
			</div>
	
			<div class="form_node last">
				<input type="submit" name="Submit" value="Submit" />
			</div>
			
		</form>
	
	</div>
</div>