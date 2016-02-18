<?php 

$show_custom = FALSE;

if (function_exists('get_instance')) {

	$CI= &get_instance();

	// Need to make sure this is not called recurrsive. Will happen if using CI to display low level errors.
	$CI->error_count++;
	if (isset($CI->layout) && ($CI->error_count < 2)) $show_custom = TRUE;

}


if ($show_custom === TRUE) {

	if ($CI->layout->getFormat() == 'xml') {
		
		// FORMAT XML
		$content 	= '<error>'
					. '<title>'.$heading.'</title>'
					. '<message>' . strip_tags($message) . '</message>'
					. '</error>';
		
	} else {
	
		$CI->layout->appendTitle('Error');
		$CI->layout->setBodyClass('error_page');
	
		$content 	= '<div class="error_message database">'
					. chr(10) . '<h1>We are sorry, an error has occured.</h1>' 
					. chr(10) . '<div class="message"><p>Please try reloading the page, if the problem persists contact the web administrator.</p></div>'				
					. chr(10) . '<div class="detail" style="display:none;">'. $message .'</div>'
					. chr(10) . '<div class="buttons"><a class="button" href="javascript:location.reload();">Reload</a> <a class="button" href="javascript:history.go(-1);">Back</a></div>'
					. '</div>';
	
	}

	$CI->_formatOutput($content);

} else {

	/*
		OLD WAY. Use this if there is not an instance of CI
	*/
	
	
	?>
	<html>
	<head>
	<title>Error</title>
	<style type="text/css">
	
	body {
	background-color:	#fff;
	margin:				40px;
	font-family:		Lucida Grande, Verdana, Sans-serif;
	font-size:			12px;
	color:				#000;
	}
	
	#content  {
	border:				#999 1px solid;
	background-color:	#fff;
	padding:			20px 20px 12px 20px;
	}
	
	h1 {
	font-weight:		normal;
	font-size:			14px;
	color:				#990000;
	margin: 			0 0 4px 0;
	}
	</style>
	</head>
	<body>
		<div id="content">
			<h1><?php echo $heading; ?></h1>
			<?php echo $message; ?>
			<div class="button"><br/><a href="javascript:history.go(-1);">Back</a></div>
		</div>
	</body>
	</html>
	
<? }