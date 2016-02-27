<div class="comment">
									
	<img class="comment_author_pic" src="<?=base_url().'assets/'.$ajax['picture']?>" />

	<div class="comment_body">
		
		<a href="profile.php?id=<?=$ajax['id']?>"><?=ucwords($ajax['first_name'].' '.$ajax['last_name'])?></a>
	
		<span class="comment_date"><?=$ajax['date']?></span>
		
		<div class="comment_text"><?=htmlentities(urldecode($ajax['comment']))?></div>
		
	</div><!--/.comment_body-->

	<div style="clear:both"></div>

</div><!--/.comment-->