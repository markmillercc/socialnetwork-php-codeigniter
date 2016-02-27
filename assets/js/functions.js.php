<script type='text/javascript'>
/** All pop out lightboxes **/
$(document).ready(function(){
	$(".cbox_profile_pic").colorbox({rel:'cbox_profile_pic'});
	$(".cbox_wall_pic").colorbox();
	$(".edit").colorbox({overlayClose:false, closeButton:false, innerWidth:940});
});
/******************************/

/** Ajax search results **/
$("#search_terms").keyup(function(){
	var terms = $("#search_terms").val();
	$("#search_load").show();
	$.ajax({
		type: "POST",
		url: "<?=base_url()?>profile/ajax_search/"+terms,
		cache: false,
		success: function(html){
			$("#search_load").hide();
			$("#search_results").html('');
			$("#search_results").show();
			$("#search_results").append(html);
		}
	});
	return false;
});
/******************************/

/** Show registration form **/
$('#reg_button').click(function(){
	$('#login').slideUp();
	$('#reg').slideDown();
});
/******************************/

/** On new post text box focus, show options **/
$('#comment_txt').focus(function(){
	if ($('#post_options').css('display') == 'none')
		$('#post_options').slideDown('fast');
});
/******************************/

/** Cancel new post: clear all values and hide options **/
$('#cancel_new').click(function(){
	$('#post_options').slideUp('fast');
	$('#add_url').hide();
	$('#url').val('');
	$('#add_img').hide();
	$('#img').val('');
	$('#buttons').show();
	$('#preview_url').html('');
	$('#pre_img').attr('src', '#').hide();
	$('#comment_txt').val('');
});
/******************************/

/** Open new post URL input **/
$('#add_url_button').click(function(){
	$('#buttons').hide();
	$('#add_url').show();
});
/******************************/

/** Open new post img input **/
$('#add_img_button').click(function(){
	$('#buttons').hide();
	$('#add_img').show();
});
/******************************/

/** Close new post URL input; clear values **/
$('#cancel_url').click(function(){
	$('#add_url').hide();
	$('#url').val('');
	$('#preview_url').html('');
	$('#buttons').show();
});
/******************************/

/** Close new post img input; clear values **/
$('#cancel_img').click(function(){
	$('#add_img').hide();
	$('#img').val('');
	$('#pre_img').attr('src', '#').hide();
	$('#buttons').show();
});
/******************************/

/** Onclick "search" open/close search bar **/
$('#search_button').click(function(){
	if ($('#search').css('display') == 'none') {
		$('#header').animate({'min-height':'70px'}, 200);
		$('#search').toggle(250);
	}
	else {
		$('#search').toggle(50);
		$('#header').animate({'min-height':'0px'}, 200);
	}
	$('#search_terms').focus();
	$('#search_results').toggle();
});
/******************************/

/** On change new profile pic input, submit form **/
$('.add_pic').change(function(){
	$('#loading').show();
	this.form.submit();
});
/******************************/

/** Call Embed.ly API to embed content from URL **/
function embedly (div, url){
	$('#'+div).html('');
	var encodeUrl = encodeURIComponent(url.trim());
	var embedlyKey = ''; // ENTER API KEY. Get from http://embed.ly/
	$.get( 
		"http://api.embed.ly/1/oembed?url="+encodeUrl+"&maxwidth=500&key="+embedlyKey, 
		function( data ) {
			var embed = '';
			if (data['type'] == 'link') {
				if (data['thumbnail_url'] !== undefined)
					embed += "<img style='max-width:400px' src='"+data['thumbnail_url']+"' /><br/>";
				
				embed += "<a href='"+data['url']+"' target='blank'>"+data['title']+"</a>";
				
				if (data['description'] !== undefined)
					embed += "<br/><span style='color:#666'>"+data['description']+"</span>";
			}
			else if (data['type'] == 'photo') {
				embed = "<img style='max-width:400px' src='"+data['thumbnail_url']+"'/>";
				embed += "<br/><a href='"+data['url']+"' target='blank'>"+data['title']+" ("+data['provider_name']+")</a>";
			}
			else if (data['type'] == 'video') {
				embed = data['html'];
			}
			$('#'+div).html(embed);
		}
	);
	if ($('#'+div).html() == '')
		$('#'+div).html("<a href='http://"+url+"' target='blank'>"+url+"</a>");
}
/******************************/

/** Preview embedded URL for wall upload**/
$("#url").bind("paste keyup input", function(){
	embedly('preview_url', this.value);
});
/******************************/

/** Preview image for wall upload**/	
function previewImg(input) {
	if (input.files && input.files[0]) {
		var reader = new FileReader();
		reader.onload = function (e) {
			$('#pre_img').attr('src', e.target.result).css('display', '');
		}
		reader.readAsDataURL(input.files[0]);
	}
}
$("#img").change(function(){
	previewImg(this);
});
/******************************/

/** Ajax new comment **/
function addComment(id) {
	var post_id = $("#post_id"+id).val();
	var comment = urlencode($("#comment"+id).val());
	var dataString = 'post_id='+ post_id + '&comment=' + comment;
	if (comment=='')
		alert('Oops, your comment was blank.');
	else {
		$.ajax({
			type: "POST",
			url: "<?=base_url()?>profile/ajax_comment/"+post_id+'/'+comment,
			data: dataString,
			cache: false,
			success: function(html){
				$("#post_comments"+id).prepend(html);
				$("#comment"+id).val('');
			}
		});
	}
	return false;
}
/******************************/	

/** urlencode() **/
function urlencode(str) {
  str = (str + '')
    .toString();
  return encodeURIComponent(str)
    .replace(/!/g, '%21')
    .replace(/'/g, '%27')
    .replace(/\(/g, '%28')
    .
  replace(/\)/g, '%29')
    .replace(/\*/g, '%2A')
}
/********************************/
</script>