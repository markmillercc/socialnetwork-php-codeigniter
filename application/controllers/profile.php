<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
* Profile controller 
*
* Handle profile from view to model
*/
class Profile extends CI_Controller {
	
	/**
	 * @return: me()
	 */
	public function index() 
	{
		redirect('profile/me');
	}
	
	/**
	 * Load member page (current session or other)
	 * 
	 * @param id: int: member id for current profile to display
	 * @param id: false: default to session member 
	 */
	public function me($id=false)
	{
		$this->check_sess();
	
		$this->load->model('profile_model');
		
		$member_data = $profile_data = $this->profile_model->get_profile_data($id);
		$me = true;
		
		if ($id) {
			if ($id != $member_id = $this->session->userdata('member_id')) {
				$member_data = $this->profile_model->get_profile_data($member_id);
				$me = false;
			}
		}
		
		$profile_pic = $this->profile_model->get_profile_pic($profile_data->picture);
		$all_pics = $this->profile_model->get_all_pics($id, $profile_data->picture);
		$pic_display = $this->get_pic_display(count($all_pics));
		$wall = $this->get_wall($id);
		
		$data = array(
			'me' => $me,
			'member_data' => $member_data,
			'profile_data' => $profile_data,
			'profile_pic' => $profile_pic,
			'all_pics' => $all_pics,
			'pic_cols' => $pic_display['pic_cols'],
			'pic_max_width' => $pic_display['pic_max_width'],
			'pic_size' => $pic_display['pic_size'],
			'wall' => $wall
		);
		
		$this->load->view('profile_page', $data);
	}
	
	/**
	 * Load member profile page (non session member)
	 * 
	 * @return: me($id)
	 */
	public function member($id)
	{
		if ($id == $this->session->userdata('member_id')) {
			redirect('profile/me');
		}
		$this->me($id);
	}
	
	/**
	 * Load edit_profile_page
	 *
	 */
	public function edit_profile() 
	{
		$this->check_sess();
		$this->load->model('profile_model');
		$profile_data = $this->profile_model->get_profile_data();
		$this->load->view('edit_profile_page', array('profile_data'=>$profile_data));
	}
	
	/**
	 * Save profile edits 
	 *
	 */
	public function edit_profile_save() 
	{
		$this->load->model('profile_model');
			
		if ($this->profile_model->update_member_profile()) {
			redirect('profile/me');
		}
		else echo 'Failed to update profile';

	}
	
	/**
	 * Add picture to profile
	 *
	 */
	public function add_profile_image() 
	{
		$this->load->model('profile_model');
		if ($this->profile_model->add_profile_image())
			redirect('profile/me');
	}
	
	/**
	 * Set picture as main profile picture
	 *
	 */
	public function set_profile_pic($pic_id)
	{
		$this->load->model('profile_model');
		$this->profile_model->set_profile_pic($pic_id);
	}
	
	/**
	 * Delete a picture from profile
	 *
	 */
	public function delete_pic($pic_id)
	{
		$this->load->model('profile_model');
		$this->profile_model->delete_pic($pic_id);	
	}
	
	/**
	 * Get all wall posts for current profile
	 *
	 * @param $member_id: int: id of wall owning member
	 * @param $member_id: false: default to session member
	 *
	 * @param $start: int: wall id to start from
	 * @param $start: false: default to 0
	 *
	 * @param $qty: int: number of posts to retrieve
	 * @param $qty: false: defaults to all posts
	 */
	public function get_wall($member_id=false, $start=false, $qty=false) 
	{
		$this->load->model('profile_model');
		return $this->profile_model->get_wall($member_id, $start, $qty);
	}
	
	/**
	 * Create a new wall post
	 *
	 * @param $member_id: int: id of owner of wall to post to
	 * @param $member_id: false: default to session member
	 */
	public function new_wall_post($member_id=false) 
	{
		if (!$member_id) $member_id = $this->session->userdata('member_id');
		
		$text = $this->input->post('text');
		$url = $this->input->post('url');
		if ($_FILES['image']['error'] == 0) {
			$img = $_FILES['image'];
		}
		else $img = false;
		
		$data['wall_owner'] = $member_id;
		$data['author'] = $this->session->userdata('member_id');
		$data['date'] = date('Y-m-d H:i:s');
		
		$this->load->model('profile_model');
		
		if ($img) {
			if (!$img_id = $this->profile_model->process_image($img)) {
				echo 'Failed to upload image.';
			}
			else {
				$data['picture'] = $img_id;
			}
		}
		
		if ($text) $data['text'] = strip_tags($text);
		if ($url) $data['url'] = $url;
		
		$this->profile_model->new_wall_post($data);
		if ($member_id == $this->session->userdata('member_id')) {
			redirect('profile/me');
		}
		else {
			redirect('profile/'.$member_id);
		}
	}
	
	/**
	 * Insert new comment using ajax
	 *
	 * @param $post_id: id of wall post to attach comment to
	 * @param $comment: body of comment
	 */
	public function ajax_comment($post_id, $comment)
	{
		$data['post_id'] = $post_id;
		$data['comment'] = urldecode(strip_tags($comment));
		$data['author'] = $this->session->userdata('member_id');
		$data['date'] = date('Y-m-d H:i:s');
		
		$this->load->model('profile_model');
		if (!$this->profile_model->new_comment($data)) {
			echo 'Error inserting comment';
			return false;
		}
		
		$author = $this->profile_model->get_profile_data($data['author']);
		$author_pic = $this->profile_model->get_profile_pic($author->picture);
		
		$ajax['id'] = $data['author'];
		$ajax['picture'] = $author_pic->small;
		$ajax['first_name'] = $author->first_name;
		$ajax['last_name'] = $author->last_name;
		$ajax['comment'] = $data['comment'];
		$ajax['date'] = date('M j, Y g:ia', strtotime($data['date']));
		
		$this->load->view('ajax_comment', array('ajax'=>$ajax));
	}
	
	/**
	 * Setup profile picture display
	 *
	 * @param $num_pics: number of pics in profile
	 *
	 * @return array:
	 * 		pic_cols: number of columns to display pictures in
	 * 		pic_size: size of pictures to pull from db (small, medium, or large)
	 * 		pic_max_width: max displayed width of each picture
	 */
	private function get_pic_display($num_pics) 
	{		
		if ($num_pics <= 10) {
			$num_cols = 1;
			$pic_size = 'medium';
			$pic_max_width = '326px';
		}
		elseif ($num_pics <= 20) {
			$num_cols = 2;
			$pic_size = 'medium';
			$pic_max_width = '161px';
		}
		elseif ($num_pics <= 40) {
			$num_cols = 3;
			$pic_size = 'small';
			$pic_max_width = '106px';
		}
		elseif ($num_pics <= 80) {
			$num_cols = 4;
			$pic_size = 'small';
			$pic_max_width = '78px';
		}
		else {
			$num_cols = 5;
			$pic_max_width = '62px';
			$pic_size = 'small';
		}

		$pic_cols = array();
		
		for ($i=0; $i<$num_cols; $i++) 
			$pic_cols[$i] = floor($num_pics/$num_cols);
		
		if ($num_pics % $num_cols != 0)
			for ($i=0; $i<$num_pics%$num_cols; $i++)
				$pic_cols[$i]++;
		
		return array('pic_cols'=>$pic_cols, 'pic_size'=>$pic_size, 'pic_max_width'=>$pic_max_width);
	}
	
	/**
	 * Load search results using ajax
	 *
	 * @param $terms: search terms
	 */
	public function ajax_search($terms=false) 
	{
		$this->load->model('profile_model');
		$results = $this->profile_model->search($terms);
		$this->load->view('ajax_search', array('results' => $results, 'terms'=>$terms));
	}
	
	/**
	 * Verify that a user is logged in
	 */
	private function check_sess() 
	{
		if (!$this->session->userdata('member_id')){
			redirect(base_url());
		}
	}
}
