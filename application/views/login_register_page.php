<!doctype html>
<html>
	
	<head>
		<meta charset= "utf-8" />
		<title>SocialNetwork | Login</title>
		<link rel="stylesheet" type="text/css" href="<?=base_url().'assets/css/style.css'?>">
	</head>

	<body>
	
		<div id="header">
			
			<h1>SocialNetwork</h1>
			
			<div style="clear:both"></div>
		
		</div><!--/#header-->

		<div id="wrap">
			
			<div id="welcome">
				
				<div id="login" style="<?=(isset($register)?'display:none':'')?>">
					
					<h2>Login</h2>
					
					<?=form_open()?>
					
						<span class='validation_errors'>
							<?=validation_errors()?>
						</span>
						
						<div class="field">
							<h3>Email:</h3>
							<?=form_input(array('name'=>'email', 'value'=>$this->input->post('email'), 'placeholder'=>'Email'))?>
						</div>
						
						<div class="field">
							<h3>Password:</h3>
							<?=form_password(array('name'=>'password', 'placeholder'=>'Password'))?>
						</div>
						
						<div class="field">
							<?=form_submit(array('name'=>'local_login', 'class'=>'submit', 'value'=>'Login'))?>
						</div>
						
						<div class="field" style="float:right">
							<h3><a id="reg_button">Create a new account</a></h3>
						</div>
					<?=form_close()?>

				</div><!--/login-->
				
				<div id="reg" style="<?=(isset($register)?'':'display:none')?>">

					<h2>Create a new account</h2>
				
					<?=form_open('enter/register')?>
						
						<span class='validation_errors'>
							<?=validation_errors()?>
						</span>
						
						<div class="field">
							<h3>First name:*</h3>
							<?=form_input(array('name'=>'first_name', 'value'=>$this->input->post('first_name')))?>
						</div>
						
						<div class="field">
							<h3>Last name:*</h3>
							<?=form_input(array('name'=>'last_name', 'value'=>$this->input->post('last_name')))?>
						</div>
						
						<div class="field">
							<h3>Email:*</h3>
							<?=form_input(array('name'=>'email', 'value'=>$this->input->post('email')))?>
						</div>
						
						<div class="field">
							<h3>Password:*</h3>
							<?=form_password('password')?>
						</div>

						<div class="field">
							<h3>Re-type password:*</h3>
							<?=form_password('repassword')?>
						</div>
						
						<h2>Tell us about yourself (optional)</h2>
						
						<div class="field">
							<h3>What's your favorite quote?</h3>
							<?=form_input(array('name'=>'quote', 'maxlength'=>255, 'placeholder'=>'ex. Freeeeeedom!!', 'value'=>$this->input->post('quote')))?>
						</div>
						
						<div class="field">
							<h3>Who said that?</h3>
							<?=form_input(array('name'=>'quote_src', 'maxlength'=>75, 'placeholder'=>'ex. William Wallace', 'value'=>$this->input->post('quote_src')))?>
						</div>
						
						<div class="field">
							<h3>Where do you currently live?</h3>
							<?=form_input(array('name'=>'location', 'maxlength'=>75, 'placeholder'=>'Any combo of city, state, country, region, etc', 'value'=>$this->input->post('location')))?>
						</div>
						
						<div class="field">
							<h3>Where are you from?</h3>
							<?=form_input(array('name'=>'hometown', 'maxlength'=>75, 'placeholder'=>'Your hometown', 'value'=>$this->input->post('hometown')))?>
						</div>
						
						<div class="field">
							<h3>Where did you go to school?</h3>
							<?=form_input(array('name'=>'education', 'maxlength'=>75, 'placeholder'=>'ex. Starfleet Academy', 'value'=>$this->input->post('education')))?>
						</div>
						
						<div class="field">
							<h3>Where do you work? What do you do?</h3>
							<?=form_input(array('name'=>'work', 'maxlength'=>75, 'placeholder'=>'ex. Astrophysicist at SNASA (secret NASA)', 'value'=>$this->input->post('work')))?>
						</div>
						
						<div class="field">
							<h3>What is your relationship status?</h3>
							<?=form_input(array('name'=>'relationship', 'maxlength'=>75, 'placeholder'=>'Single? Married? Other', 'value'=>$this->input->post('relationship')))?>
						</div>
						
						<div class="field">
							<h3>Your gender?</h3>
							<?=form_dropdown('gender', array(''=>'', 'm'=>'Male', 'f'=>'Female'), $this->input->post('gender'))?>
						</div>
						
						<div class="field">
							<h3>When is your birthday?</h3>
							<?=form_input(array('name'=>'dob', 'maxlength'=>50, 'placeholder'=>'ex. March 13, 1984 or 3/13/1984', 'value'=>$this->input->post('dob')))?>
						</div>
						
						<div class="field">
							<h3>Anything else you want to share?</h3>
							<?=form_textarea(array('name'=>'about', 'maxlength'=>2000, 'rows'=>3, 'value'=>$this->input->post('about')))?>
						</div>
						
						<div class="field">
							<?=form_submit('register', 'Register', "class='submit'")?>
							<a href="<?=base_url()?>">Cancel</a>
						</div>

					<?=form_close()?>
					
				</div><!--/#reg-->
				
			</div><!--/#welcome-->

		</div><!--/#wrap-->
		
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js" type="text/javascript"></script>
		<?php include_once('assets/js/functions.js.php'); ?>
	
	</body>

</html>