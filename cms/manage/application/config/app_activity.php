<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

// ------------------------------------------------------------------------
// ACCESS MENU

$config['access_menu']	= array(
								'style'					=> 'custom'
								, 'items'				=> array(

									array(
										'title'			=> 'Page Module Activity'
										, 'href'		=> CI()->module . '/page'
										, 'icon'		=> 'img/mini_icons/arrow_collapse.gif'
										)

									, array(
										'title'			=> 'User Module Activity'
										, 'href'		=> CI()->module . '/user'
										, 'icon'		=> 'img/mini_icons/arrow_collapse.gif'
										)	

									)
								);


$config['filter_module']		= array(
									'page'				=> 'Page'
									, 'file'			=> 'File'
									, 'user'			=> 'User'
									, 'config'			=> 'Config'
									);
