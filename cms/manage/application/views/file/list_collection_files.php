	<form class="tndr_form" method="post" name="editForm" id="editForm" action="<?=$this->admin_path.$this->module.'/update/file/-1'?>" accept-charset="utf-8" enctype="multipart/form-data">

		<input type="hidden" name="fields[parent_id]" value="<?=$this->current_id?>" />
		<input type="hidden" name="fields[file_id]" value="-1" />

		<div class="form_section">
			<div class="form_section_header">Upload File</div>
			<div class="section_content">
	
				<div class="form_row">
					<label>Title (Optional):</label>
					<div class="field"><input type="text" name="fields[title]"  /></div>
				</div>
				<div class="form_row">
					<label>File:</label>
					<div class="field"><input class="required" type="file" name="userfile"  /></div>
				</div>
				<div class="form_row">
					<div class="field"><button type="submit" class="button"><span>Upload</span></button></div>
				</div>
			
			</div>
		</div>
		
		<br/>


		<div class="form_section">
			<div class="form_section_header">Files</div>
			<div class="section_content">

				<? foreach ($files as $file): 
			
					$icon = !empty($file['is_image']) ? 'image' : 'document';
			
					?>
					<div class="form_row" id="file_<?=$file[$this->id_field]?>">
						<div class="row_buttons">
							<img src="<?=$this->asset_path?>img/mini_icons/magnify.gif" width="10" height="10"/>
							<a href="#DEL" onClick="confirmDelete('<?=$this->admin_path?>/file/delete/<?=$file[$this->id_field]?>?file_id=<?=$file[$this->id_field]?>&DELETE=DELETE'); this.blur();"><img src="<?=$this->asset_path?>img/mini_icons/trash.gif" width="10" height="10"/></a>
						</div>
						<img src="<?=$this->asset_path?>img/mini_icons/<?=$icon?>.gif" width="10" height="10"/> <a href="<?=$this->admin_path.$this->module.'/edit/'.$file[$this->id_field]?>"><?=$file['title']?></a>
			
					</div>
				<? endforeach; ?>
			
			</div>
		</div>

	</form>


<div class="clear"> </div>