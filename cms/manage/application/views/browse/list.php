<div class="disk_browser">
<div class="items_wrapper">
	<div class="items" id="picker_items"> <!-- Scroll box -->

		<ul id="browser" class="access_menu">

			<?

			if (!empty($this->disk_paths['parent'])) {
				echo '<li><div class="data">'
				    .'<div class="label parent"><a class="item" href="?path='.$this->disk_paths['parent'].'&destination='.urlencode($this->input->get_post('destination')).'">Parent</a></div>'
				    .'</div></li>';
			}

			if (count($files)) {
			
				foreach ($files as $key => $val) {
				
					$file_name 	= $key;
					if ($val['type'] == 'dir') {
						$file_name 	= '<a href="?path='.urlencode($val['paths']['application']).'&destination='.urlencode($this->input->get_post('destination')).'" class="item">'.$val['name'].'</a>';
					} else if ($val['type'] == 'file') {
			
						switch ($this->mode) {
							case 'picker':
								$file_name = '<div class="item">' . $val['name'] . '</a>';
								break;
							default:
								$file_name = '<a href="'.$val['paths']['http'].'" name="'.$val['name'].'" class="item">'.$val['name'].'</a>';
						}
					}
					$file_size 	= ($val['type'] == 'dir') ? '&nbsp;' : $val['size'];
					$row_icons	= 'CHOOSE';

					$js_param = array(
						'value'			=> $val['paths']['application']
						, 'destination'	=> $this->input->get_post('destination')
						);
				
					$row = '<li>'
						  .'<div class="data">'
								. '<div class="label '.$val['type'].'">'
									. '<div class="size">'.$file_size.'</div>'
									. '<div class="date">'.$val['date'].'</div>'
									. $file_name
								. '</div>'
								. '<div class="icons"><a href="#" onClick=\'javascript:diskBrowser.choose('.json_encode($js_param).'); return false;\'><img src="'.$this->asset_path.'img/button_pick_sm.gif" alt="PICK" /></a></div>'
						  . '</div></li>';

					echo chr(10).$row;
			
				}
			
			} else {
				echo '<div class="data"><div class="label">(No files)</div></div>';	
			}
			?>
		
		</ul>
		<script language="javascript">
			document.observe('dom:loaded', function(event) {
				TreeNav.init('browser');
			});
		</script>
	
		</div>
	</div>
</div>