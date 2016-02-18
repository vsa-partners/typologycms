<style type="text/css">

body {
	background-color: #fff;
	margin: 10px;
	font-family: Lucida Grande, Verdana, Sans-serif;
	font-size: 12px;
	color: #000;
	}
A { color: #000 !important; }
</style>


<p>The following page has been submitted for your approval to be published.</p>

<div style="width: 250px; margin-top: 20px; padding: 10px; border: 1px solid #ccc;">
	<? 
	foreach ($pages as $page) {
		echo '<strong><a href="'.$module_path.'edit/'.$page['page_id'].'">' . $page['title'] . '</a></strong>'
			. '<br/>'
			. $page['path'] 
			. '<br/>'
			. date(DATE_DISPLAY_FORMAT, strtotime($page['update_date']))
			. '<br/><br/>';
	}
	?>
</div>

<p style="font-size: 10px;">This email is being sent automatically by your CMS. If you no longer which to receive these notifications please contact your website administrator.</p>