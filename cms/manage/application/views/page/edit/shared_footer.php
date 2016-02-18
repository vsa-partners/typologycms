
<!-- Start content_footer snippet -->

<div class="clear"> </div>

<div class="content_footer">
	<dl>
		<dt class="small">ID:</dt>
		<dd class="small"><?=$fields[$this->id_field]?></dd>
		
		<dt>Last Updated:</dt>
		<dd><a href="<?=$this->admin_path?><?=$this->module?>/activity/<?=$this->current_id?>"><?=date(DATE_DISPLAY_FORMAT, strtotime($fields['update_date']))?></a>
			
			<? if (!empty($fields['related_editor']['user'])) echo ' by '.$fields['related_editor']['user']; ?>
		
		</dd>
	</dl>
	
	
</div>

<!-- End content_footer snippet -->
