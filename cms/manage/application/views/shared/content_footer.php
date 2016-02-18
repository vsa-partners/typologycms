
<div class="content_footer">
	<dl>
		<dt class="small">ID:</dt>
		<dd class="small"><?=$fields[$this->id_field]?></dd>
		
		<? if (!empty($fields['update_date'])): ?>

			<dt style="width: 100px;">Last Updated:</dt>
			<dd><?=date(DATE_DISPLAY_FORMAT, strtotime($fields['update_date']))?></dd>
		
		<? elseif (!empty($fields[$this->module.'_update_date'])): ?>

			<dt style="width: 100px;">Last Updated:</dt>
			<dd><?=date(DATE_DISPLAY_FORMAT, strtotime($fields[$this->module.'_update_date']))?></dd>

		<? endif; ?>
		
	</dl>
	
	
</div>

<!-- End content_footer snippet -->
