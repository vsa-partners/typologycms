<? $this->load->view('shared/content_header', array('title' => 'Linked Pages', 'buttons' => array('delete', 'links'))); ?>

<fieldset class="open">
	<legend>Linked Pages</legend>
	
	<? foreach ($fields['related_page'] as $page_out) {?>
	
		<div class="form_row">
				
			<div  style="width: 350px; float: right;">
				<?=$page_out['path']?>
			</div>				
			
			<img src="<?=$this->asset_path?>img/mini_icons/document.gif" width="10" height="10"/>
			<a href="<?=$this->admin_path?>page/edit/<?=$page_out['page_id']?>" style="font-weight: bold;"><?=$page_out['title']?></a>
			
		</div>
	<? } ?>
	
	
</fieldset>