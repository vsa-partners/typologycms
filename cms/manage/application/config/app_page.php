<?php

$config['allow_page_links']					= TRUE;
$config['load_page_links']					= FALSE;													// true || false
$config['load_page_relations']				= FALSE;													// true || false

$config['allowed_html_tags']                = '<b><i><sup><sub><em><strong><u><br><iframe><a><ul><ol><li><p><div><h1><h2><h3><h4><h5>';

$config['xml_attribute_nodes']				= array('page_id', 'path', 'template_file_name', 'sort');
$config['xml_remove_nodes']					= array('path', 'template_file_name', 'options','type','module','parent_id','file_name','template_id','template_title','template_type','template_module','template_path','template_parent_id','template_html_xsl_path','template_xml_xsl_path','template_xml','template_options','template_sort','template_create_date','template_update_date','template_cache_time', 'sort');

$config['omniture_keys']					= array();

// ------------------------------------------------------------------------
// ITEM TYPES

$config['items']		= array(
								'root' 					    => array(
									'name'				    => 'Site Root'
									, 'allowed_children'    => array('page', 'section', 'redirect', 'secure_section', 'page_calendar', 'page_database', 'mirror_section_source', 'mirror_section', 'mirror_page_source', 'mirror_page', 'mirror_calendar_source', 'mirror_calendar')
									, 'list_children'       => true
									, 'icons'			    => array('sm' => 'img/mini_icons/home.gif')
									)
								, 'section' 		        => array(
									'name'				    => 'Folder'
									, 'allowed_children'    => array('page', 'section', 'mirror_page', 'mirror_section', 'redirect', 'page_database', 'page_calendar', 'mirror_calendar')
									, 'list_children'       => true
									, 'icons'			    => array('sm' => 'img/mini_icons/folder.gif')
									)
								, 'page' 				    => array(
									'name'				    => 'Page'
									, 'allowed_children'    => array()
									, 'icons'			    => array('sm' => 'img/mini_icons/document.gif')
									)
								, 'page_calendar'		    => array(
									'name'				    => 'Calendar'
									, 'allowed_children'    => array('page_calendar_event')
									, 'list_children'       => false
									, 'icons'			    => array('sm' => 'img/mini_icons/calendar.gif')
									, 'module'              => 'page_calendar'
									)
								, 'page_calendar_event'	    => array(
									'name'				    => 'Calendar Event'
									, 'allowed_children'    => array()
									, 'icons'			    => array('sm' => 'img/mini_icons/calendar_day.gif')
									, 'module'              => 'page_calendar'
									)									
								, 'secure_section'		    => array(
									'name'				    => 'Secure Folder'
									, 'allowed_children'    => array('secure_page')
									, 'list_children'       => true
									, 'icons'			    => array('sm' => 'img/mini_icons/key.gif')
									)
								, 'secure_page'		        => array(
									'name'				    => 'Secure Page'
									, 'allowed_children'    => array()
									, 'icons'			    => array('sm' => 'img/mini_icons/person.gif')
									)
								, 'redirect'				=> array(
									'name'					=> 'Redirect'
									, 'allowed_children'	=> array()
									, 'icons'				=> array('sm' => 'img/mini_icons/turn_left.gif')
									)

								, 'page_input' 				=> array(
									'name'				    => 'Input Page'
									, 'allowed_children'    => array()
									, 'icons'			    => array('sm' => 'img/mini_icons/mail.gif')
									)


								, 'mirror_section_source'	=> array(
									'name'				    => 'Mirror Source Folder'
									, 'allowed_children'    => array('mirror_page_source', 'mirror_section_source')
									, 'list_children'       => true
									, 'icons'			    => array('sm' => 'img/mini_icons/folder.gif')
									)
								, 'mirror_section'			=> array(
									'name'					=> 'Mirror Folder'
									, 'allowed_children'	=> array('page', 'section', 'mirror_page', 'mirror_section', 'redirect')
									, 'list_children'       => true
									, 'icons'				=> array('sm' => 'img/mini_icons/folder_dim.gif')
									)


								, 'mirror_page_source' 	 	=> array(
									'name'				    => 'Mirror Source Page'
									, 'allowed_children'    => array()
									, 'icons'			    => array('sm' => 'img/mini_icons/document.gif')
									)
								, 'mirror_page'				=> array(
									'name'					=> 'Mirror Page'
									, 'allowed_children'	=> array()
									, 'icons'				=> array('sm' => 'img/mini_icons/document_dim.gif')
									)

								, 'mirror_calendar_source' 	=> array(
									'name'				    => 'Mirror Source Calendar'
									, 'allowed_children'    => array('mirror_calendar_event_source')
									, 'icons'			    => array('sm' => 'img/mini_icons/calendar.gif')
									, 'list_children'       => false
									, 'module'              => 'page_calendar'
									)
								, 'mirror_calendar' 	 	=> array(
									'name'				    => 'Mirror Calendar'
									, 'allowed_children'    => array('mirror_calendar_event')
									, 'icons'			    => array('sm' => 'img/mini_icons/calendar_dim.png')
									, 'list_children'       => false
									, 'module'              => 'page_calendar'
									)

								, 'mirror_calendar_event_source' => array(
									'name'				    => 'Mirror Calendar Event Source'
									, 'allowed_children'    => array()
									, 'icons'			    => array('sm' => 'img/mini_icons/calendar_day.gif')
									, 'module'              => 'page_calendar'
									)
								, 'mirror_calendar_event' => array(
									'name'				    => 'Mirror Calendar Event'
									, 'allowed_children'    => array()
									, 'icons'			    => array('sm' => 'img/mini_icons/calendar_day.gif')
									, 'module'              => 'page_calendar'
									)



								, 'page_database'		    => array(
									'name'					=> 'Database Folder'
									, 'allowed_children'	=> array('page_database_item')
									, 'list_children'       => false
									, 'icons'				=> array('sm' => 'img/mini_icons/database.gif')
									)
								, 'page_database_item'		=> array(
									'name'					=> 'Database Page'
									, 'allowed_children'	=> array()
									, 'icons'				=> array('sm' => 'img/mini_icons/index_card.gif')
									)


								);


// ------------------------------------------------------------------------

$config['page_types']	= array(
								'page'							=> 'Page'
								, 'section'						=> 'Folder'
								, 'page_calendar'				=> 'Calendar'
								, 'redirect'		    		=> 'Redirect'
								, 'secure_section'				=> 'Secure Folder'
								, 'secure_page'					=> 'Secure Page'
								, 'page_calendar'				=> 'Calendar'
								, 'page_calendar_event'			=> 'Calendar Event'
								, 'root'						=> 'Root'

								, 'page_database'				=> 'Database Folder'
								, 'page_database_item'			=> 'Database Page'

								, 'mirror_calendar_source'		=> 'Mirror Calendar Source'
								, 'mirror_calendar'				=> 'Mirror Calendar'
								, 'mirror_calendar_event_source'=> 'Mirror Calendar Event Source'
								, 'mirror_calendar_event'		=> 'Mirror Calendar Event'

								, 'mirror_page'					=> 'Mirror Page'
								, 'mirror_page_source'			=> 'Mirror Page Source'
								, 'mirror_section'				=> 'Mirror Folder'
								, 'mirror_section_source'		=> 'Mirror Folder Source'

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
									'800'				=> '8:00 am'
									, '1000'				=> '10:00 am'
									, '1200'				=> '12:00 pm'
									, '1400'				=> '2:00 pm'
									, '1600'				=> '4:00 pm'
									, '1800'				=> '6:00 pm'
									, '2000'				=> '8:00 pm'
									);

// Used for page calendar events

$config['time_hours']		= array(
								'01' 	=> '1'
								, '02' 	=> '2'
								, '03'	=> '3'
								, '04'	=> '4'
								, '05'	=> '5'
								, '06'	=> '6'
								, '07'	=> '7'
								, '08'	=> '8'
								, '09'	=> '9'
								, '10'	=> '10'
								, '11'	=> '11'
								, '12'	=> '12'
								);
$config['time_mins']		= array(
								'00' 	=> '00'
								, '15' 	=> '15'
								, '30'	=> '30'
								, '45'	=> '45'
								);
$config['time_ampm']		= array(
								'am' 	=> 'AM'
								, 'pm' 	=> 'PM'
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

								, 'multilang_textfield'	=> array(
									'type' 			=> 'string'
									, 'cdata' 		=> true
									)
									
								, 'multilang_textarea'	=> array(
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
								, 'streetview'		=> array(
									'type' 			=> 'string'	
									, 'cdata' 		=> true						
									, 'editAttribs' => array('lat', 'long', 'zoom', 'pitch', 'heading')	
									)

								, 'file'		=> array(
									'type' 			=> 'string'
									, 'cdata' 		=> true
									, 'editAttribs' => array('file_title', 'file_path', 'file_id')	
									)
						
								, 'pagelink'	=> array(
									'type' 			=> 'string'
									, 'cdata' 		=> true
									, 'editAttribs' => array('page_path', 'page_title', 'page_id')
									)

								, 'pageselect'	=> array(
									'type' 			=> 'string'
									, 'cdata' 		=> true
									, 'editAttribs' => array('template_id', 'parent_id')
									)

								, 'date'	=> array(
									'type' 			=> 'string'
									, 'cdata' 		=> true
									)

								, 'time'	=> array(
									'type' 			=> 'string'
									, 'cdata' 		=> true
									, 'editAttribs' => array('hour', 'minute')
									)


								, 'checklist'		=> array(
									'type' 			=> 'section'
									)
								, 'checkbox'	=> array(
									'type' 			=> 'string'
									, 'cdata' 		=> false
									)						
								);

$config['xml_template_attribs'] = array('border', 'multi', 'maxlength', 'options', 'title', 'type', 'notes', 'type', 'editor'
										, 'collection', 'browser', 'size', 'link_module', 'link_type', 'template_id', 'parent_id'
										, 'image_height', 'image_width'
										, 'toggle');
