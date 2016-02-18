<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

// ------------------------------------------------------------------------
// ACCESS MENU



	$config['access_menu']	= array(
									'style'					=> 'custom'
									, 'items'				=> array(
									
										array(
											'title'			=> 'Outstanding Publish Queues'
											, 'href'		=> SITEPATH . CI()->zone. '/' . CI()->module . '/jobs'
											, 'icon'		=> 'img/mini_icons/arrow_collapse.gif'
											, 'display'		=> (CI()->ADMIN_CONF['publish']['publish_method'] != 'local_table')
											)
										, array(
											'title'			=> 'Execute Publish'
											, 'admin_path'	=> FALSE
											, 'href'		=> SITEPATH . CI()->zone. '/' . CI()->module . '/publish'
											, 'icon'		=> 'img/mini_icons/arrow_collapse.gif'
											, 'display'		=> (CI()->ADMIN_CONF['publish']['publish_method'] != 'local_table')
											)
										, array('space'		=> (CI()->ADMIN_CONF['publish']['publish_method'] != 'local_table'))

										, array(
											'title'			=> 'View Activity Logs'
											, 'href'		=> SITEPATH . CI()->zone. '/activity'
											, 'icon'		=> 'img/mini_icons/arrow_collapse.gif'
											)
	
										, array('space'		=> TRUE)
	
										, array(
											'title'			=> 'Remote: Clear Content Cache'
											, 'href'		=> SITEPATH . CI()->zone. '/' . CI()->module . '/web_clearRemoteSiteCache'
											, 'icon'		=> 'img/mini_icons/arrow_collapse.gif'
											, 'display'		=> (CI()->ADMIN_CONF['publish']['publish_method'] != 'local_table')
											)
										, array(
											'title'			=> 'Remote: Clear JS/CSS Cache'
											, 'href'		=> SITEPATH . CI()->zone. '/' . CI()->module . '/web_clearRemoteAssetCache'
											, 'icon'		=> 'img/mini_icons/arrow_collapse.gif'
											, 'display'		=> (CI()->ADMIN_CONF['publish']['publish_method'] != 'local_table')
											)

										, array(
											'title'			=> 'Local: Clear Content Cache'
											, 'href'		=> SITEPATH . CI()->zone. '/' . CI()->module . '/web_clearLocalSiteCache'
											, 'icon'		=> 'img/mini_icons/arrow_collapse.gif'
											)
										, array(
											'title'			=> 'Local: Clear JS/CSS Cache'
											, 'href'		=> SITEPATH . CI()->zone. '/' . CI()->module . '/web_clearLocalAssetCache'
											, 'icon'		=> 'img/mini_icons/arrow_collapse.gif'
											)

										, array('space'		=> TRUE)

										, array(
											'title'			=> 'Database Explorer'
											, 'href'		=> SITEPATH . CI()->zone. '/util_mysql'
											, 'icon'		=> 'img/mini_icons/arrow_collapse.gif'
											, 'display'		=> (CI()->authentication->hasPermission('admin'))
											)
										, array(
											'title'			=> 'Database Download'
											, 'href'		=> SITEPATH . CI()->zone. '/' . CI()->module . '/backupDB'
											, 'icon'		=> 'img/mini_icons/arrow_collapse.gif'
											)
	
										, array('space'		=> CI()->authentication->hasPermission('admin'))
										, array(
											'title'			=> 'PHP Info'
											, 'href'		=> CI()->module . '/info'
											, 'icon'		=> 'img/mini_icons/arrow_collapse.gif'
											, 'display'		=> (CI()->authentication->hasPermission('admin'))
											)
	
	
	
										)
									);