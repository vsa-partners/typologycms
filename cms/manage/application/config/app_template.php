<?php

// ------------------------------------------------------------------------
// ITEM CONFIG 

$config['items']			= array(
								'template' 					=> array(
									'name'					=> 'Page Template'
									, 'allowed_children'	=> null
									, 'icons'				=> array('sm' => 'img/mini_icons/copy.gif')
									)
								);


// ------------------------------------------------------------------------
// ACCESS MENU

$config['access_menu']	= array(
								'style'						=> 'list'
								);


// ------------------------------------------------------------------------
// TEMPLATE OPTIONS

$config['options']	= array(

								'html_action'				=> array(
									'show'					=> 'Show Page'
									, 'show_w_immediate'	=> 'Show Page - Include Immediate Children'
									, 'show_w_all'			=> 'Show Page - Include All Children'
									, 'first'				=> 'Show First Page'
									, 'specified'			=> 'Show Specified Page'
									, 'deny'				=> 'Deny'
									)

								, 'xml_action'				=> array(
									'show'					=> 'Show Page'
									, 'show_w_immediate'	=> 'Show Page - Include Immediate Children'
									, 'show_w_all'			=> 'Show Page - Include All Children'
									, 'deny'				=> 'Deny'
									)

								, 'cache_time'				=> array(
									'none'					=> 'No Cache'
									, '3 Hours'				=> 'Three Hours'
									, '6 Hours'				=> 'Six Hours'
									, '12 Hours'			=> 'Twelve Hours'
									, '1 Day'				=> 'One Day'
									, '7 Days'				=> 'One Week'
									, '1 Month'				=> 'One Month'
									)

								, 'child_edit_style'		=> array(
									'normal'				=> 'Normal (In Tree)'
									, 'list'				=> 'List on Edit Page'
									)
									
								,'child_sort_method'		=> array(
									'manually'				=> 'Manually'
									, 'title'				=> 'By Title (Alpha)'
									, 'date_asc'			=> 'By Date - Ascending'
									, 'date_desc'			=> 'By Date - Descending'
									)

								, 'max_depth'				=> 20			// Max depth for 'all' action

								);