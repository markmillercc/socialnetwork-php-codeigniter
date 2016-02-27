<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Login/register controller
 *
 * Handle login/registration from view to model
 */
class Enter extends CI_Controller {
	
	/**
	 * @return: validate_login()
	 */
	public function index()
	{
		$this->login();
	}
	
	/**
	 * Login
	 *
	 * @success: log in member, redirect to profile page
	 * @fail: reload login with errors
	 */
	public function login() 
	{
		$this->check_sess();
		
		$email = $this->input->post('email'); // Store email before callback overwrites
		
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('email', 'Email', 'required|trim|xss_clean|callback_validate_credentials');
		$this->form_validation->set_rules('password', 'Password', 'required|md5|trim');
		
		if ($this->form_validation->run()) {
			$data = array (
				'member_id' => $this->input->post('email'), /*post('email') overwritten with member id*/
				'email' => $email
			);
			$this->session->set_userdata($data);
			
			redirect('profile/me');
		}
		else {
			$this->load->view('login_register_page');
		}
	}
	
	/**
	 * Check login credentials 
	 *
	 * @callback from: login()
	 *
	 * @return false: bad credentials
	 * @return true: credentials OK
	 */
	public function validate_credentials()
	{
		$this->load->model('entry_model');
		
		if ($id = $this->entry_model->is_member()) {
			return $id;
		}
		else {
			$this->form_validation->set_message('validate_credentials', 'Incorrect username/password');
			return false;
		}
	}
	
	/**
	 * Validate registration
	 *
	 * @success: register new member and log in
	 * @fail: reload registration page with errors
	 */
	public function register() 
	{
		$this->check_sess();
		
		$this->load->library('form_validation');
		
		$this->form_validation->set_rules('first_name', 'First name', 'required|trim');
		$this->form_validation->set_rules('last_name', 'Last name', 'required|trim');
		$this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|is_unique[users.email]');
		$this->form_validation->set_rules('password', 'Password', 'required|trim');
		$this->form_validation->set_rules('repassword', 'Confirm password', 'required|trim|matches[password]');
		$this->form_validation->set_rules('dob', 'Birthday', 'callback_validate_dob');
		
		$this->form_validation->set_message('is_unique', 'There is already a membership with that email.');
		
		if ($this->form_validation->run()) {
		
			$new_email = $this->input->post('email');
			$new_password = $this->input->post('password');
			
			$this->load->model('entry_model');
			
			if ($this->entry_model->add_new_member()) {
				unset($_POST);
				$_POST['email'] = $new_email;
				$_POST['password'] = $new_password;
				$this->login();
			}
			else echo 'Registration failed';
		}
		else {
			$this->load->view('login_register_page', array('register'=>true));
		}
		
	}
	
	/**
	 * Check if birthday is decipherable string
	 *
	 * @callback from: register()
	 *
	 * @return false: birthday string not readable
	 * @return true: birthday string OK, convert and return
	 */
	public function validate_dob() 
	{
		if (!$this->input->post('dob')) return true;
		if (!$dob = strtotime($this->input->post('dob'))) {
			$this->form_validation->set_message('validate_dob', 'There was an error reading your birthday. Try again in the form of MM/DD/YYYY.');
			return false;
		}
		else return date('Y-m-d', $dob);
	}
	
		
	/**
	 * Kill session and logout
	 */
	public function logout() 
	{
		$this->session->sess_destroy();
		redirect(base_url());
	}
	
	/**
	 * If user is logged in, go to profile
	 */
	private function check_sess() 
	{
		if ($this->session->userdata('member_id')) {
			redirect('profile');
		}
	}
	
	
}
