<?php

// ------------------------------------------------------------------------
// ITEM CONFIG 

$config['items']			= array(
									'user' 					=> array(
										'name'				=> 'User'
										, 'icons'			=> array('sm' => 'img/mini_icons/person.gif')
										)
									, 'administrator'		=> array(
										'name'				=> 'Administrator'
										, 'icons'			=> array('sm' => 'img/mini_icons/happy.gif')
										)
									);	
	

// ------------------------------------------------------------------------
// ACCESS MENU

$config['access_menu']		= array(
								'style'					=> 'list'
								, 'title_field'			=> 'user'
								);


// ------------------------------------------------------------------------

$config['options']			= array(
								'output_profiler'		=> array(
									'0'					=> 'No'
									, '1'				=> 'Yes'
									)
								, 'enabled'			=> array(
									'1'					=> 'Yes'
									, '0'				=> 'No'
									)
								);
