<!doctype html>
<html>
	
	<head>
		
		<meta charset= "utf-8" />
		<title>SocialNetwork | Profile Information</title>
		<link rel="stylesheet" type="text/css" href="css/style.css">
		
	</head>
	
	<body>
		<div id="edit_form">
		
			<h1>Tell us about yourself</h1>
			
			<?=form_open('profile/edit_profile_save')?>
						
				<span class='validation_errors'>
					<?=validation_errors()?>
				</span>

				<div class="field">
					<h3>What's your favorite quote?</h3>
					<?=form_input(array('name'=>'quote', 'maxlength'=>255, 'placeholder'=>'ex. Freeeeeedom!!', 'value'=>$profile_data->quote))?>
				</div>
				
				<div class="field">
					<h3>Who said that?</h3>
					<?=form_input(array('name'=>'quote_src', 'maxlength'=>75, 'placeholder'=>'ex. William Wallace', 'value'=>$profile_data->quote_src))?>
				</div>
				
				<div class="field">
					<h3>Where do you currently live?</h3>
					<?=form_input(array('name'=>'location', 'maxlength'=>75, 'placeholder'=>'Any combo of city, state, country, region, etc', 'value'=>$profile_data->location))?>
				</div>
				
				<div class="field">
					<h3>Where are you from?</h3>
					<?=form_input(array('name'=>'hometown', 'maxlength'=>75, 'placeholder'=>'Your hometown', 'value'=>$profile_data->hometown))?>
				</div>
				
				<div class="field">
					<h3>Where did you go to school?</h3>
					<?=form_input(array('name'=>'education', 'maxlength'=>75, 'placeholder'=>'ex. Starfleet Academy', 'value'=>$profile_data->education))?>
				</div>
				
				<div class="field">
					<h3>Where do you work? What do you do?</h3>
					<?=form_input(array('name'=>'work', 'maxlength'=>75, 'placeholder'=>'ex. Astrophysicist at SNASA (secret NASA)', 'value'=>$profile_data->work))?>
				</div>
				
				<div class="field">
					<h3>What is your relationship status?</h3>
					<?=form_input(array('name'=>'relationship', 'maxlength'=>75, 'placeholder'=>'Single? Married? Other', 'value'=>$profile_data->relationship))?>
				</div>
				
				<div class="field">
					<h3>Your gender?</h3>
					<?=form_dropdown('gender', array(''=>'', 'm'=>'Male', 'f'=>'Female'), $profile_data->gender)?>
				</div>
				
				<div class="field">
					<h3>When is your birthday?</h3>
					<?=form_input(array('name'=>'dob', 'maxlength'=>50, 'placeholder'=>'ex. March 13, 1984 or 3/13/1984', 'value'=>$profile_data->dob))?>
				</div>
				
				<div class="field">
					<h3>Anything else you want to share?</h3>
					<?=form_textarea(array('name'=>'about', 'maxlength'=>2000, 'rows'=>3, 'value'=>$profile_data->about))?>
				</div>
				
				<div class="field">
					<?=form_submit('save_updates', 'Save', "class='submit'")?>
					<a href="<?=base_url() . 'profile/me'?>">Cancel</a>
				</div>
				
				<br/>
				
			<?=form_close()?>

		</div><!--/#edit_form-->
	
	</body>

</html>