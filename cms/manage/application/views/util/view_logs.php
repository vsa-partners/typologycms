echo "<strong>".TEXT_FILE."</strong>";

	$fsize = round(filesize(TEXT_FILE)/1024/1024,2);

	echo "<br/>File size is {$fsize} megabytes";
	echo "<br/>Last ".LINES_COUNT." lines of the file:";

	echo '<pre>';

	$lines = read_file(TEXT_FILE, LINES_COUNT);
	foreach ($lines as $line) {
		echo $line;
	}

	echo '</pre>';

	echo '<div class="refresh_time">Page drawn at:  <strong>' . date('Y-m-d H:i:s') . '</strong></div>';

} else {

	echo '<div style="color: red;">ERROR: File not found</div>';

}?>
