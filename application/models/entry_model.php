<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Entry model
 * 
 * Handle login/registration from controller to db
 */
class Entry_model extends CI_Model {
	
	/**
	 * Check if login credentials belong to member
	 *
	 * @return false: bad credentials
	 * @return int: credentials OK, return member id
	 */
	public function is_member() 
	{
		$this->db->where('email', $this->input->post('email'));
		$this->db->where('password', md5($this->input->post('password')));
		
		$query = $this->db->get('users');
		
		if ($query->num_rows() > 0) {
			return $query->row()->id;
		}
		else return false;
	}
	
	/**
	 * Insert a new member
	 *
	 * @return false: insert failed
	 * @return true: insert succeeded
	 */
	public function add_new_member() 
	{	
		$data = array();
		foreach ($this->input->post() as $key => $val) {
			if ($key == 'register' || $key == 'repassword') continue;
			if (!empty($val)) {
				$data[$key] = ($key=='password' ? md5($val) : $val);
			}
		}
		$data['reg_date'] = date('Y-m-d H:i:s');
		
		return $this->db->insert('users', $data);
	}
}