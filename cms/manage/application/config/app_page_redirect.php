<?php

// ------------------------------------------------------------------------
// ITEM CONFIG 

$config['items']			= array(
								'redirect' 					=> array(
									'name'					=> 'Redirect'
									, 'allowed_children'	=> null
									, 'icons'				=> array('sm' => 'img/mini_icons/copy.gif')
									)
								);


// ------------------------------------------------------------------------
// ACCESS MENU

$config['access_menu']  = array(
                                'style'                 => 'custom'
                                , 'items'               => array(
                                
                                    array(
                                        'title'         => 'View All'
                                        , 'href'        => SITEPATH . CI()->zone. '/' . CI()->module . '/'
                                        , 'icon'        => 'img/mini_icons/arrow_collapse.gif'
                                        , 'display'     => true
                                        )
                                )

                            );
