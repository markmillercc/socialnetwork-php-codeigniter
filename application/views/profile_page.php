<!doctype html>
<html>
	
	<head>
		<meta charset= "utf-8" />
		<title>SocialNetwork | <?=ucwords($profile_data->first_name.' '.$profile_data->last_name)?></title>
		<link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/css/style.css">
		<link rel="stylesheet" type="text/css" href="<?=base_url()?>assets/css/colorbox.css" />
	</head>
	
	<body>

		<div id="header">
			
			<h1>SocialNetwork</h1>
			
			<a class="menu" href="<?=base_url().'enter/logout'?>">logout</a>
			<a class="menu" id="search_button">search</a>
			<a class="menu name" href="<?=base_url().'profile/me'?>"><?=ucwords($member_data->first_name.' '.$member_data->last_name)?></a>
			
			<div id="search">
				
				<input id="search_terms" type="text" placeholder="Search for people by name, location, school, etc..." autocomplete="off"/>	
			
			</div><!--/#search-->
			
			<div id="search_results"><!--ajax--></div>
			
			<div id="search_load" style="display:none"><img src="<?=base_url()?>assets/images/sload.gif" alt="loading"/></div>
			
			<div style="clear:both"></div>
		
		</div><!--/#header-->
		
		<div style="clear:both"></div>
		
		<div id="wrap">
			
			<div id="profile">

				<div id="who">
			
					<h1><?=ucwords($profile_data->first_name.' '.$profile_data->last_name)?></h1>
				
					<img src='<?=base_url()?>assets/<?=$profile_pic->medium?>' alt="profile picture"/>
			
					<?php if ($profile_data->quote) { ?>
						
						<div id="quote">
							
							<span class="quote_text">&#8220;<?=$profile_data->quote?>&#8221;</span>
							
							<span class="quote_src">-<?=$profile_data->quote_src?></span>
						
						</div><!--/#quote-->
					
					<?php } ?>
					
				</div><!--/#who-->

				<div id="about">
					
					<?php if ($me) { ?>
						
						<a class="edit" href="<?=base_url() . 'profile/edit_profile'?>">Edit</a>
					
					<?php } ?>
					
					<h2>About</h2>
					
					<ul>
						<?php if ($profile_data->location) { ?>
							<li>
								<span class="title">Currently lives</span>
								<span class="value"><?=$profile_data->location?></span>
							</li>
						<?php } ?>
						
						<?php if ($profile_data->hometown) { ?>
							<li>
								<span class="title">Originally from</span>
								<span class="value"><?=$profile_data->hometown?></span>
							</li>
						<?php } ?>
						
						<?php if ($profile_data->education) { ?>
							<li>
								<span class="title">Education</span>
								<span class="value"><?=$profile_data->education?></span>
							</li>
						<?php } ?>
					
						<?php if ($profile_data->work) { ?>
							<li>
								<span class="title">Work/Career</span>
								<span class="value"><?=$profile_data->work?></span>
							</li>
						<?php } ?>
						
						<?php if ($profile_data->relationship) { ?>
							<li>
								<span class="title">Relationship</span>
								<span class="value"><?=$profile_data->relationship?></span>
							</li>
						<?php } ?>
						
						<?php if ($profile_data->gender) { ?>
							<li>
								<span class="title">Gender</span>
								<span class="value"><?=($profile_data->gender=='m'?'Male':'Female')?></span>
							</li>
						<?php } ?>
						
						<?php if ($profile_data->dob) { ?>
							<li>
								<span class="title">Birthday</span>
								<span class="value"><?=date('F d, Y', strtotime($profile_data->dob))?></span>
							</li>
						<?php } ?>
						
						<?php if ($me) { ?>
							<li>
								<span class="title">Email <i>(private)</i></span> 
								<span class="value"><?=$profile_data->email?> </span>
							</li>
						<?php } ?>
						
						<?php if ($profile_data->about) { ?>
							<li>
								<span class="title">About</span>
								<span class="value"><?=$profile_data->about?></span>
							</li>
						<?php } ?>

					</ul>
					
				</div><!--/#about-->
				
				<div id="pics">
						
					<?php if ($me) { 
						
						echo form_open_multipart('profile/add_profile_image', array('id'=>'upload_profile_images'));
						echo form_upload(array('name'=>'profile_image', 'class'=>'add_pic'));
						echo form_close();
						
					} ?>
					
					<h2>Pictures</h2>
					
					<div id="loading" style="display:none"><br/><img src="<?=base_url()?>assets/images/loadingbar.gif" alt="loading"/><br/><br/></div>
					
					<?php
					$i = 0;
					foreach ($pic_cols as $n) {
					?>
						<div class="pic_col">
							
							<?php 
							for ($j=0; $j<$n; $j++) {
								$title = ''; 
								if ($me) {
									$title = "<a href='".base_url()."profile/set_profile_pic/".$all_pics[$i]['id']."'>Set as profile picture</a><br/>";
									$title .= "<a href='".base_url()."profile/delete_pic/".$all_pics[$i]['id']."'>Delete picture</a>";
								} ?>
								
								<a class="cbox_profile_pic" href="<?=base_url().'assets/'.$all_pics[$i]['large']?>" title="<?=$title?>">
									<img style="width:<?=$pic_max_width?>" src="<?=base_url().'assets/'.$all_pics[$i][$pic_size]?>" alt="profile pic"/>
								</a>
								
								<?php $i++; ?>
							
							<?php } ?>
						
						</div><!--/.pic_col-->
						
					<?php } ?>
				
				</div><!--/#pics-->
			
			</div><!--/#profile-->
			
			<div id="wall">
			
				<div id="new_post">
			
					<?=form_open_multipart('profile/new_wall_post/'.$profile_data->id, array('autocomplete'=>'off'))?>
						
						<?=form_textarea(array('id'=>'comment_txt', 'name'=>'text', 'rows'=>2, 'placeholder'=>'Post to ' . ($me?'your':ucfirst($profile_data->first_name)."'s") . ' wall...'))?>
							
						<div id="post_options">
							
							<div id="buttons">
								
								<a id="add_url_button">Add a URL</a> or 
								<a id="add_img_button">Add a picture</a>
							
							</div><!--/#buttons-->
						
							<div id="add_url">
								
								<?=form_input(array('id'=>'url', 'name'=>'url', 'placeholder'=>"Enter a URL you'd like to share...", 'autocomplete'=>'off'))?>
								<a id="cancel_url">cancel</a>
							
							</div><!--/#add_url-->
							
							<div id="add_img">
								
								<?=form_upload(array('id'=>'img', 'name'=>'image'))?>
								<a id="cancel_img">cancel</a>
							
							</div><!--/#add_img-->
							
							<div id="preview_url"></div>
						
							<div id="preview_img"><img style="display:none" id="pre_img" src="#" alt="upload preview"/></div>
						
							<div id="submit_post">
								
								<?=form_submit(array('name'=>'post', 'value'=>'Post'))?>
								<a id="cancel_new">cancel</a>
							
							</div><!--/#submit_post-->
							
							<div style="clear:both"></div>
						
						</div><!--/#post_options-->
					
					<?=form_close()?>
				
				</div><!--/new_post-->
				
				<?php
				$urls = array();
				foreach($wall as $post) {
				?>
					<div class="post">
							
						<div class="post_author">
							
							<img class="post_author_pic" src="<?=base_url().'assets/'.$post['author_pic']?>" alt="post author"/>
							
							<span class="post_author_name">
								
								<a href="<?=base_url()?>profile/<?=$post['author_id']?>"><?=ucwords($post['author_name'])?></a>
							
								<span class="post_date"><?=date('M j, Y g:ia', strtotime($post['date']))?></span>
							
							</span>

						</div><!--/.post_author-->
							
						<div class="post_body">
							
							<?php if ($post['text']) { ?>
								
								<div class="post_text"><?=$post['text']?></div>
							
							<?php } if ($post['url']) { ?>
								
								<div id="url_post_<?=$post['id']?>" class="post_url"><img src="images/loading.gif" alt="loading"/></div>
								
								<?php $urls[] = array('div'=>"url_post_{$post['id']}", 'url'=>"{$post['url']}"); ?>
							
							<?php } if ($post['medium_image']) { ?>
								
								<div class="post_image">
									
									<a class="cbox_wall_pic" href="<?=base_url().'assets/'.$post['large_image']?>">
										<img src="<?=base_url().'assets/'.$post['medium_image']?>" alt="wall pic"/>	
									</a>
								
								</div><!--/.post_image-->
							
							<?php } ?>	
						
						</div><!--/.post_body-->
							
						<div class="add_comment">
							
							<?=form_open()?>
								
								<?=form_textarea(array('id'=>'comment'.$post['id'], 'name'=>'comment', 'rows'=>2, 'placeholder'=>'Write a comment...', 'maxlength'=>255, 'onkeydown'=>"if(event.keyCode==13)addComment(".$post['id'].")"))?>
								
								<?=form_input(array('type'=>'hidden', 'id'=>'post_id'.$post['id'], 'name'=>'post_id', 'value'=>$post['id']))?>

							
							<?=form_close()?>
						
						</div><!--/.add_comment-->
							
						<div id="post_comments<?=$post['id']?>">
							
							<?php foreach ($post['comments'] as $comment) { ?>
								
								<div class="comment">
									
									<img class="comment_author_pic" src="<?=base_url().'assets/'.$comment['author_pic']?>" alt="comment author"/>

									<div class="comment_body">
										
										<a href="<?=base_url()?>profile/<?=$comment['author_id']?>"><?=ucwords($comment['author_name'])?></a>
									
										<span class="comment_date"><?=date('M j, Y g:ia', strtotime($comment['date']))?></span>
										
										<div class="comment_text"><?=htmlentities(urldecode($comment['comment']))?></div><!--/.comment_body-->
										
									</div><!--/.comment_body-->

									<div style="clear:both"></div>
								
								</div><!--/.comment-->
							
							<?php } ?>
							
						</div><!--/#post_comments{id}-->
							
					</div><!--/.post-->
				
				<?php } ?>
				
			</div><!--/#wall-->
			
			<div style='clear:both'></div>
		
		</div><!--/#wrap-->
  
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js" type="text/javascript"></script>
		<script src="<?=base_url()?>assets/js/jquery.colorbox-min.js" type="text/javascript"></script>
		<?php include_once('assets/js/functions.js.php'); ?>
		
		<script type="text/javascript">
			<?php
				foreach ($urls as $url)
					echo "embedly('{$url['div']}', '{$url['url']}');";
			?>
		</script>
		
	</body>
</html>
	
