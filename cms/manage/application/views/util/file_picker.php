<?

//print_r_pre($files);

echo '<div class="fileBrowser">';

// ------------------------------------------------------------------------
// FILE LIST

echo $input;

echo 'FILE BROWSER';

echo '<div class="path">'.$local_path.'</div>';

echo '<div class="files">';

	if ($local_path != '/'.$this->ADMIN_CONF['upload_location']) {
		echo '<div class="data">'
			.'<div class="label parent"><a href="?path='.substr($local_path, 0, strrpos($local_path, '/')).'&input='.urlencode($input).'">(Parent Directory)</a></div>'
			. '</div>';
	}

	if (count($files)) {

		foreach ($files as $key => $val) {
		
			$file_name 	= $key;
			if ($val['type'] == 'dir') {
				$file_name 	= '<a href="?path='.urlencode($val['localPath']).'&input='.urlencode($input).'">'.$key.'</a>';
			} else if ($val['type'] == 'file') {
				$js_param  = "{input: '".$input."', value: '".$val['path']."', close: true}";
				$file_name = '<a href="#" onClick="filePicker.choose('.$js_param.'); return false;">'.$key.'</a>';
			}
			$file_size 	= ($val['type'] == 'dir') ? '&nbsp;' : $val['size'];
			$row_icons	= 'CHOOSE';
		
			$row = '<div class="data">'
				// . '<div class="icons" style="display: Xnone;">'.$row_icons.'</div>'
				. '<div class="label '.$val['type'].'">'.$file_name.'</div>'
				. '<div class="size">'.$file_size.'</div>'
				. '<div class="date">'.$val['date'].'</div>'
				. '</div>';
	
	
			echo $row;
		}
	
	} else {
		echo '<div class="data"><div class="label">(No files)</div></div>';	
	}

echo '</div>';



// ------------------------------------------------------------------------
// UPLOAD



echo '<div class="upload">';

echo NL . form_open_multipart(trim($_SERVER['PHP_SELF'], SITEPATH));

	echo NL . form_hidden('path', $local_path);
	echo NL . '<div class="field">' . form_upload(array('name'=>'userfile', 'size'=>'40')) . '</div>';

	echo NL . form_submit('upload', 'UPLOAD');

echo NL . form_close();

echo '</div>';

echo '</div>';


echo '<div class="directory">';

echo NL . form_open(trim($_SERVER['PHP_SELF'], SITEPATH));

	echo NL . form_hidden('path', $local_path);
	echo NL . '<div class="field">' . form_input('directory_name') . '</div>';

	echo NL . form_submit('directory', 'CREATE');

echo NL . form_close();

echo '</div>';

echo '</div>';


?>