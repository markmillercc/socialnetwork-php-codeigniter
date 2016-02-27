<?php if ($results) { ?>
	
	<div id='result_container'>
	
		<?php foreach ($results as $r) { ?>
			
			<div class='result'>
				
				<img src='<?=base_url().'assets/'.$r['small']?>' />
				
				<a href='<?=base_url()?>profile/<?=$r['id']?>'><?=ucwords($r['first_name'].' '.$r['last_name']).' '.(!$r['location']?'':'&#8226; '.$r['location'])?></span></a>
			
			</div><!--/.result-->
		
		<?php } ?>
	
	</div><!--/#result_container-->

<?php } elseif ($terms != '') { ?>

	<div id='result_container'>no results</div>

<?php } ?>