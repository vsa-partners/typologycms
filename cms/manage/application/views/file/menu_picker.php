<div class="<?=(($this->input->get_post('parent_id') && ($this->input->get_post('parent_id') > 1)) ? 'file_picker collection' : 'file_picker')?>">
	
	<div id="popup_header">
		<? if (count($this->picker_parent_item)) {
			echo 'Browsing : ' . $this->picker_parent_item['title'];		
		} else {
			echo 'Browsing : FILES';
		}?>	
	</div>

	<div id="popup_content">

		<div id="inner_scroll">
			<ul id="picker" class="access_menu">
				<? if (count($this->picker_parent_item)) {
					echo '<li><div class="data"><a href="'.reduce_double_slashes($this->admin_path.'/'.$this->module.'/picker', '/').'?destination='.$this->input->get_post('destination').'&multi='.$this->input->get_post('multi').'&parent_id='.$this->picker_parent_item['parent_id'].'"><img src="'.$this->asset_path.'img/mini_icons/arrow_parent.gif" width="10" height="10"/> PARENT FOLDER</a></div></li>';
				} ?>			
				<?=$output?>
			</ul>
		</div>
	
		<!-- Control hover actions -->
		<script language="javascript">
			TreeNav.init('picker');
		</script>

	</div> <!-- /#popup_content -->

		<div class="fp_buttons">	
	
			<? if ($this->input->get_post('parent_id') && ($this->input->get_post('parent_id') != $this->_default_parent_id)): ?>
	
				<div class="fp_upload_form clearfix" id="upload_form">
				
					<form method="post" class="tndr_form" name="uploadForm" id="uploadForm" action="<?=$this->admin_path.$this->module?>/update/file/-1" accept-charset="utf-8" enctype="multipart/form-data">
					
						<script type="text/javascript" src="<?=$this->asset_path?>/js/swfupload/swfupload.js"></script>
						<script type="text/javascript" src="<?=$this->asset_path?>/js/swfupload/plugins/swfupload.queue.js"></script>
						<script type="text/javascript" src="<?=$this->asset_path?>/js/swfupload/plugins/fileprogress.js"></script>
						<script type="text/javascript" src="<?=$this->asset_path?>/js/swfupload/plugins/handlers.js"></script>
		
						<script type="text/javascript">
							var swfu;				
							var upload_destination = '<?=$this->input->get_post('destination')?>';
							var multi = '<?=$this->input->get_post('multi')?>';
							var qty	= (multi == 'yes') ? 0 : 1;
							var btn	= (multi == 'yes') ? 'SELECT FILES' : 'SELECT FILE';
							
							// Temporary fix to allow multiple files uploaded from a single button							
							var qty = 0;

							document.observe('dom:loaded', function(event) {
								var settings = {
									flash_url : "<?=$this->asset_path?>/js/swfupload/Flash/swfupload.swf",
									upload_url: "<?=$this->admin_path.$this->module?>/multiple/file/-1",
									post_params: {"fields[parent_id]" : "<?=$this->input->get_post('parent_id')?>", "fields[file_id]" : "-1", "multiupload":"145"},
									file_size_limit : "100 MB",
									file_types : "*",
									file_types_description : "All Files",
									file_upload_limit : 150,
									file_queue_limit : qty,								
									custom_settings : {
										progressTarget : "fsUploadProgress"
										, cancelButtonId : "multi_cancel_btn"
									},
									debug: false,				
									// Button settings
									button_image_url: "<?=$this->asset_path?>/img/file_upload_btn.png",
									button_width: "120",
									button_height: "24",
									button_placeholder_id: "multi_upload_btn",
									button_text: '<span class="theFont">'+btn+'</span>',
									button_text_style: ".theFont { font-family: Arial; font-size:10px; font-weight: bold; color: #ffffff;} .theFont:hover { color: #000000; }",
									button_text_left_padding: 20,
									button_text_top_padding: 3,							
									
									// The event handler functions are defined in handlers.js
									file_queued_handler : fileQueued_picker,
									file_queue_error_handler : fileQueueError,
									file_dialog_complete_handler : fileDialogComplete_picker,
									upload_start_handler : uploadStart,
									upload_progress_handler : uploadProgress,
									upload_error_handler : uploadError,
									upload_success_handler : uploadSuccess_picker,
									upload_complete_handler : uploadComplete,
									queue_complete_handler : queueComplete_picker
								};				
								swfu = new SWFUpload(settings);
							 });
					</script>
		
						<div class="form_row no_border">
							<label>Upload File:</label>
							<div class="field">

								<div><span id="spanButtonPlaceHolder"></span></div>
								<div class="">
									<div id="multi_upload_btn"> </div>
									<div id="multi_cancel_btn"> </div>
								</div>

							</div>
						</div>

						<input type="hidden" name="fields[parent_id]" value="<?=$this->input->get_post('parent_id')?>" />
						<input type="hidden" name="return" value="picker" />				
						<input type="hidden" name="destination" value="<?=$this->input->get_post('destination')?>" />
				
					</form>
				
				</div>
				<div class="clear"> </div>
			
			<? endif; ?>
			
			<div class="fp_collection_form clearfix">
							
				<form method="post" class="tndr_form" name="uploadForm" id="collectionForm" action="<?=$this->admin_path.$this->module?>/update/collection/-1" accept-charset="utf-8">
			
					<div class="form_row last">
						<label>Create Folder:</label>
						<div class="field"><input type="text" name="fields[title]" class="title_field required" /></div>
						<div class="row_buttons"><button class="button button_small" type="submit" value="create" name="create"><span>CREATE</span></button>	</div>
					</div>

					<input type="hidden" name="fields[parent_id]" value="<?=($this->input->get_post('parent_id') ? $this->input->get_post('parent_id') : $this->_default_parent_id)?>" />
					<input type="hidden" name="fields[file_id]" value="-1" />
					<input type="hidden" name="destination" value="<?=$this->input->get_post('destination')?>" />
					<input type="hidden" name="multi" value="<?=$this->input->get_post('multi')?>" />

					<input type="hidden" name="return" value="picker" />
			
				</form>
			
			</div>
		
		</div>



</div> <!-- /.file_picker -->
