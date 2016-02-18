<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

// ------------------------------------------------------------------------
// ACCESS MENU

	$config['login']['req_password_len']		= 6;
	$config['login']['expiration_mins'] 		= 60;

	$config['login']['log_user_activity']		= FALSE;

	$config['login']['limit_enabled_login']		= FALSE;

	$config['login']['limit_failed_login']		= FALSE;
	$config['login']['limit_failed_mins']		= 30;
	$config['login']['limit_failed_cnt']		= 5;

	$config['login']['limit_inactive_login']	= FALSE;
	$config['login']['limit_inactive_days']		= 90;

	$config['login']['instructions']			= '';

	$config['login']['path']					= '/login';


	$config['permission_presets']				= array(

	$config['permissions']						= array(

													'manage'

													, 'global', 'global_create', 'global_delete'

													, 'module_page'
													, 'module_page_create', 'module_page_publish', 'module_page_delete'

													, 'module_page_attribute'
													, 'module_page_redirect'

													, 'module_file'
													, 'module_file_upload', 'module_file_delete'

													, 'module_template'
													
													, 'module_users'
													, 'module_users_create', 'module_users_delete', 'module_users_editpassword'

													, 'module_activity'
													
													, 'module_util'
													
													, 'module_config'

													);

	
	$config['user_groups']			= array(

										'content_editor'		=> array(
											'name'				=> 'Content Editor'
											, 'desc'			=> 'Only allowed to edit existing content and save as draft.'
											, 'permissions'		=> array(
																	'admin_access'
																	, 'module_file'
																	, 'module_page'
											)
										)

										
										, 'content_creator'		=> array(
											'name'				=> 'Content Creator'
											, 'desc'			=> 'Allowed to edit create and delete content.'
											, 'permissions'		=> array(
																	'admin_access'
																	, 'global_create'
																	, 'global_delete'
																	, 'module_file'
																	, 'module_page'
																	, 'module_page_attribute'
																	, 'module_page_redirect'
											)
										)
										

										, 'content_publisher'		=> array(
											'name'				=> 'Content Approver/Publisher'
											, 'desc'			=> 'Full content access including able to publish to website.'
											, 'permissions'		=> array(
																	'admin_access'
																	, 'global_create'
																	, 'global_delete'
																	, 'global_publish'
																	, 'module_file'
																	, 'module_page'
																	, 'module_page_attribute'
																	, 'module_page_redirect'
											)
										)

										, 'site_supervisor'		=> array(
											'name'				=> 'Website Supervisor'
											, 'desc'			=> 'Full content/publish access, able to view activity logs and create new users.'
											, 'permissions'		=> array(
																	'admin_access'
																	, 'global_create'
																	, 'global_delete'
																	, 'global_publish'
																	, 'module_file'
																	, 'module_page'
																	, 'module_util'
																	, 'module_user'
																	, 'module_activity'
																	, 'module_page_attribute'
																	, 'module_page_redirect'
		
																	/* Page module specific permissions */
																	//, 'page_options'	
											)
										)

										, 'administrator'		=> array(
											'name'				=> 'Administrator'
											, 'desc'			=> ''
											, 'permissions'		=> array('*')
											)
										);

	// ------------------------------------------------------------------------

	$config['forgot_pw_email']		= array(
											'from_name'	=> 'CMS'
											, 'from_email'	=> $config['admin_email']
											, 'subject'		=> 'Your password has been reset.'
											, 'message'		=> 'Your password has been successfully reset. Please use the new password listed below to access the cms. '
																. chr(10). chr(10)
											);
