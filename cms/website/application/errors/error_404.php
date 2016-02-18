<?php header("HTTP/1.1 404 Not Found"); 

$show_custom = FALSE;

if (function_exists('get_instance')) {
	
	$CI= &get_instance();

	if (isset($CI->layout)) {

		// Need to make sure this is not called recurrsive. Will happen if using CI to display low level errors.
		$CI->error_count++;
		if ($CI->error_count < 2) $show_custom = TRUE;
	
	}

}


if ($show_custom === TRUE) {

	$CI->layout->appendTitle('Error');
	$CI->layout->setBodyClass('error_page');


	if ($CI->layout->getFormat() == 'xml') {

		// This should not be required, but the output library does not send stored headers to error pages
		header('Content-Type: text/xml');
		
		// FORMAT XML
		$content 	= '<error type="404">'
					. '<title>'.$heading.'</title>'
					. '<message>' . $message . '</message>'
					. '</error>';
		
	} else {
	
		$content 	= '<div class="error_message pagenotfound">'
					. '<h1>'.$heading.'</h1>' 
					. '<div class="message">'
					. $message
					. '</div>'
					. '<div class="buttons"><a class="button" href="javascript:history.go(-1);">Back</a></div>'
					. '</div>';
	
	}
		
	echo $CI->layout->wrap($content);

} else {


	/*
		OLD WAY. Use this if there is not an instance of CI
	*/
	
	
	?>
	<html>
	<head>
	<title>404 Page Not Found</title>
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
		</div>
	</body>
	</html>
	
<? } ?>