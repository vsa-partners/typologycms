<?php

$config['allow_page_links']					= TRUE;
$config['load_page_links']					= FALSE;													// true || false
$config['load_page_relations']				= FALSE;													// true || false


$config['xml_attribute_nodes']				= array('page_id', 'template_file_name', 'path', 'parent_path', 'parent_id',  'template_id', 'type', 'sort');
$config['xml_remove_nodes']					= array('page_id', 'path', 'template_file_name', 'options','type','module','parent_id', 'template_id','template_title','template_type','template_module','template_path','template_parent_id','template_html_xsl_path','template_xml_xsl_path','template_xml','template_sort','template_create_date','template_update_date','template_cache_time', 'meta_title', 'meta_description', 'meta_keywords', 'publish_date', 'approve_date', 'file_title', 'parent_path');


// ------------------------------------------------------------------------
// ITEM TYPES

$config['items']		= array(
								'root' 					=> array(
									'name'				=> 'Root Item'
									, 'allowed_children'=> array('page', 'section', 'redirect', 'secure_section')
									, 'icons'			=> array('sm' => 'img/mini_icons/home.gif')
									)
								, 'section' 		=> array(
									'name'				=> 'Page Section'
									, 'allowed_children'=> array('page', 'section', 'redirect')
									, 'icons'			=> array('sm' => 'img/mini_icons/folder.gif')
									)
								, 'page' 				=> array(
									'name'				=> 'Page'
									, 'allowed_children'=> array()
									, 'icons'			=> array('sm' => 'img/mini_icons/document.gif')
									)

								, 'secure_section'		=> array(
									'name'				=> 'Secure Section'
									, 'allowed_children'=> array('secure_user')
									, 'icons'			=> array('sm' => 'img/mini_icons/key.gif')
									)

								, 'secure_user'		=> array(
									'name'				=> 'Secure Page'
									, 'allowed_children'=> array()
									, 'icons'			=> array('sm' => 'img/mini_icons/person.gif')
									)

								, 'redirect'				=> array(
									'name'					=> 'Simple Redirect'
									, 'allowed_children'	=> array()
									, 'icons'				=> array('sm' => 'img/mini_icons/turn_left.gif')
									)

								, 'page_input' 				=> array(
									'name'				=> 'Input Page'
									, 'allowed_children'=> array()
									, 'icons'			=> array('sm' => 'img/mini_icons/mail.gif')
									)
								);

// ------------------------------------------------------------------------

$config['page_types']	= array(
								'page'					=> 'Page'
								, 'section'				=> 'Section'
								, 'page_calendar'		=> 'Calendar'
								, 'secure_section'		=> 'Secure Section'
								, 'secure_user'			=> 'Secure User'
								, 'page_input'			=> 'Secure User'
								);

// ------------------------------------------------------------------------
// ACCESS MENU

$config['access_menu']	= array(
								'style'						=> 'tree'
								);


// ------------------------------------------------------------------------
// PAGE STATUS OPTIONS

$config['status']				= array(
									1						=> array(
										'title'				=> 'Draft'
										, 'icon'			=> 'status_draft.gif'
										, 'color'			=> '#a4a4a4'
										)
									, 5						=> array( 
										'title'				=>'Publish Declined'
										, 'icon'			=> 'status_draft.gif'
										, 'color'			=> 'red'
										)
									, 10					=> array( 
										'title'				=>'Pending Approval'
										, 'icon'			=> 'status_draft.gif'
										, 'color'			=> '#51e46a'
										)
									, 20					=> array( 
										'title'				=>'Published'
										, 'icon'			=> 'status_published.gif'
										, 'color'			=> '#51e46a'
										)
									, 80					=> array( 
										'title'				=>'Hidden'
										, 'icon'			=> 'status_deleted.gif'
										, 'color'			=> '#e12424'
										)
									, 90					=> array( 
										'title'				=>'Deleted'
										, 'icon'			=> 'status_deleted.gif'
										, 'color'			=> '#e12424'
										)
									);

// ------------------------------------------------------------------------
// PUBLISH CONTROLS

$config['publish_periods']		=  array(
									'now'					=> 'At Next Publish'
									, 'date'				=> 'Specific Time'
									);

$config['publish_times']		= array(
									'09:00'					=> 'Morning'
									, '12:00'				=> 'Mid-Day'
									, '18:00'				=> 'Afternoon'
									, '23:59'				=> 'Night'
									);

// ------------------------------------------------------------------------
// VERSIONS

$config['versions']['delete_old'] 	= TRUE;
$config['versions']['days_keep'] 	= 90;

// ------------------------------------------------------------------------
// XML

$config['trim_input_values']	= 1;										// Clean up leading/trailing whitespace user input values

/*
 * 	type 		= This is the type that will be used in the outputXML.
 * 	cdata		= If set to true value will be cdata in the node, else attribute.
 *  editAttribs = Attribute names tied to edit page. All others will be used from template.
 */
$config['xml_nodes'] 		= array(
								'edit_tab'	=> array(
									/* This is a sepecial section. It must be top level.
									 * Used to draw the tabbed edit screen.
									 */
									'type' 			=> 'section'
									)
									
								, 'section'		=> array(
									'type' 			=> 'section'
									)
									
								, 'textfield'	=> array(
									'type' 			=> 'string'
									, 'cdata' 		=> true
									)
									
								, 'textarea'	=> array(
									'type' 			=> 'string'
									, 'cdata' 		=> true
									)
									
								, 'select'	=> array(
									'type' 			=> 'string'
									, 'cdata' 		=> true
									)
						
								, 'href'		=> array(
									'type' 			=> 'string'	
									, 'cdata' 		=> true						
									, 'editAttribs' => array('target', 'href')	
									)

								, 'swf'		=> array(
									'type' 			=> 'string'	
									, 'cdata' 		=> true						
									, 'editAttribs' => array('width', 'height', 'background')	
									)
						
								, 'geocode'		=> array(
									'type' 			=> 'string'	
									, 'cdata' 		=> true						
									, 'editAttribs' => array('lat', 'long')	
									)

								, 'file'		=> array(
									'type' 			=> 'string'
									, 'cdata' 		=> true
									)
						
								, 'pagelink'	=> array(
									'type' 			=> 'string'
									, 'cdata' 		=> true
									, 'editAttribs' => array('page_path', 'page_title', 'page_id')
									)

								, 'date'	=> array(
									'type' 			=> 'string'
									, 'cdata' 		=> true
									)
						
								);

$config['xml_template_attribs'] = array('border', 'multi', 'maxlength', 'options', 'title', 'type', 'notes', 'type', 'editor'
										, 'collection', 'browser', 'file_title', 'file_path', 'size', 'link_module', 'link_type');
