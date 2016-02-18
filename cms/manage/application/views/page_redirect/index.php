<?

// ------------------------------------------------------------------------

?>
	
<? $this->load->view('shared/content_header', array('title' => 'All Redirects')); ?>

<div class="access_buttons clearfix">
	<button class="button button_outline" href="<?=$this->admin_path.$this->module?>/create" onClick="document.location = this.getAttribute('href'); this.blur();"><span>CREATE</span></button>
</div>

<br/>

<table class="data_table" width="100%" cellspacing="0" cellpadding="0" border="0">

 	<? foreach($items as $item): ?>

 		<tr>
 			<td width="10"><img src="<?=$this->asset_path?>img/mini_icons/turn_left.gif" width="10" height="10"/></td>
 			<td><a href="<?=$this->admin_path.$this->module.'/edit/'.$item['redirect_id']?>"><?=$item['old_path']?></a></td>
 			<td width="150"><?=$item['update_date']?></td>
 		</tr>
 	<? endforeach; ?>

 </table>
