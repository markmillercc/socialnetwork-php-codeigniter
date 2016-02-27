<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Profile model
 *
 * Handle profile operations between controller and db
 */
class Profile_model extends CI_Model {

	/**
	 * Get profile data
	 *
	 * @param $id: int: get profile belonging to member with id=$id
	 * @param $id: false: get profile for session member
	 *
	 * @return object with profile data
	 */
	public function get_profile_data($id=false) 
	{
		if (!$id) $id = $this->session->userdata('member_id');
		
		$this->db->where('id', $id);
		
		if (!$row = $this->db->get('users')->row()) {
			redirect('profile/me');
		}
		else return $row;	
	}
	
	/**
	 * Get profile picture (small, medium, and large)
	 *
	 * @param $pic_id: id of picture to retrieve
	 *
	 * @return object with picture paths, all sizes
	 */
	public function get_profile_pic($pic_id) 
	{	
		$this->db->select('small, medium, large');
		$this->db->where('id', $pic_id);
		
		if (!$row = $this->db->get('images')->row()) {
			$row = new stdClass;
			$row->small = $row->medium = $row->large = 'uploads/default/nopic.jpg';
		}
	
		return $row;	
	}
	
	/**
	 * Get all pictures
	 *
	 * @param $id: int: id of member for which pictures to get
	 * @param $id: false: default to session member
	 *
	 * @param $profile_pic: id of member's main profile pic - do not include in result
	 *
	 * @return array of picture paths
	 */
	public function get_all_pics($id=false, $profile_pic) 
	{
		if (!$id) $id = $this->session->userdata('member_id');
		
		$this->db->select('id, small, medium, large');
		$this->db->from('images');
		$this->db->join('profile_images', 'profile_images.image_id = images.id AND profile_images.user_id = ' . $id);
		$this->db->order_by('id', 'DESC');
		if (!empty($profile_pic))
			$this->db->where('id <>', $profile_pic);
		
		return $this->db->get()->result_array();
	}
	
	/**
	 * Set main profile picture
	 *
	 * @param $pic_id: id of picture to use
	 */
	public function set_profile_pic($pic_id)
	{
		$this->db->where('id', $this->session->userdata('member_id'));
		$this->db->update('users', array('picture'=>$pic_id));
		redirect('profile/me');	
	}
	
	/**
	 * Delete a profile picture
	 *
	 * @param $pic_id: id of picture to delete
	 */
	public function delete_pic($pic_id)
	{
		$this->db->where('id', $pic_id);
		$paths = $this->db->get('images')->row_array();
		
		unlink(PUBPATH.'assets/'.$paths['small']);
		unlink(PUBPATH.'assets/'.$paths['medium']);
		unlink(PUBPATH.'assets/'.$paths['large']);
		
		$this->db->where('id', $pic_id);
		$this->db->delete('images');
		
		$this->db->where('image_id', $pic_id);
		$this->db->delete('profile_images');
		
		redirect('profile/me');	
	}
	
	/**
	 * Add picture to profile
	 */
	// public function add_profile_image() 
	// {
		// if ($new_img_id = $this->process_image($_FILES['profile_image'])) {
			// if ($this->db->insert('profile_images', array('image_id'=>$new_img_id, 'user_id'=>$this->session->userdata('member_id')))) {
				// if (is_null($this->get_profile_data()->picture)) {
					// if ($this->set_profile_pic($new_img_id)) {
						// return true;
					// }
				// }
				// return true;
			// }
		// }
		// return false;
	// }
	
	public function add_profile_image() 
	{
		if (!$new_img_id = $this->process_image($_FILES['profile_image']))
			return false;
			
		$insert = array('image_id'=>$new_img_id, 
						'user_id'=>$this->session->userdata('member_id'));
						
		if (!$this->db->insert('profile_images', $insert))
			return false;
			
		if (is_null($this->get_profile_data()->picture))
			$this->set_profile_pic($new_img_id);

		return true;
	}
	
	/**
	 * Update member profile information
	 * 
	 * @return true: success
	 * @return false: fail
	 */
	public function update_member_profile()
	{
		
		$data = array();
		foreach ($this->input->post() as $key => $val) {
		
			if ($key == 'save_updates' || empty($val)) 
				continue;
				
			if ($key == 'dob') {
			
				if (!$dob = strtotime($this->input->post('dob'))) 
					continue;
					
				$val = date('Y-m-d', $dob);
			}
			$data[$key] = $val;
		}
		
		$this->db->where('id', $this->session->userdata('member_id'));
		return $this->db->update('users', $data);
	}
	
	/**
	 * Search members
	 *
	 * @return array of results
	 */
	public function search($terms)
	{
		if (!$terms) return false;
		
		$terms = $this->db->escape_like_str($terms); 
		$this->db->select('id, picture, first_name, last_name, location');
		$this->db->or_like("CONCAT_WS('\%20', first_name, last_name)", $terms);
		$this->db->or_like('hometown', $terms);
		$this->db->or_like('location', $terms);
		$this->db->or_like('education', $terms);
		$this->db->or_like('work', $terms);
		$this->db->where('id <>', 1);
		$query = $this->db->get('users');
		
		$results = $query->result_array();
		
		foreach ($results as $key=>$r) {
			$pic = $this->get_profile_pic($r['picture']);
			$results[$key]['small'] = $pic->small;
		}
		
		return $results;
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
	 *
	 * @return array of wall posts
	 */
	public function get_wall($member_id=false, $start=false, $qty=false) 
	{
		if (!$member_id) $member_id = $this->session->userdata('member_id');
		
		$start = !$start ? 0 : $start;
		$limit = !$qty ? '' : "LIMIT {$start}, {$qty}";
		
		$sql = "
		SELECT 
			wp.id
			,wp.author AS author_id
			,u.picture AS author_pic
			,CONCAT_WS(' ', u.first_name, u.last_name) AS author_name
			,IF(wp.picture IS NULL, 0, i.medium) AS medium_image
			,IF(wp.picture IS NULL, 0, i.large) AS large_image
			,IFNULL(wp.text, 0) AS text
			,IFNULL(wp.url, 0) AS url
			,wp.date
		FROM wall_posts wp 
		INNER JOIN users u ON u.id = wp.author
		LEFT JOIN images i ON i.id = wp.picture
		WHERE wp.wall_owner = {$member_id}
		ORDER BY wp.date DESC
		{$limit}
		;";
		
		$result = $this->db->query($sql)->result_array();

		foreach($result as $i => $r) {
			$auth_pic = $this->get_profile_pic($r['author_pic']);
			$result[$i]['author_pic'] = $auth_pic->small;
			$result[$i]['comments'] = $this->get_comments($r['id']);
		}
		return $result;
	}
	
	/**
	 * Get all comments for a particular post
	 *
	 * @param $post_id: post to get comments from
	 *
	 * @return array of comments
	 */
	public function get_comments($post_id) 
	{	
		$sql = "
		SELECT
			c.id
			,c.author AS author_id
			,u.picture AS author_pic
			,CONCAT_WS(' ', u.first_name, u.last_name) AS author_name
			,c.comment
			,c.date
		FROM comments c
		INNER JOIN users u ON u.id = c.author
		WHERE c.post_id = {$post_id}
		ORDER BY c.date DESC
		;";
		
		$result = $this->db->query($sql)->result_array();
		
		foreach($result as $i => $r) {
			$auth_pic = $this->get_profile_pic($r['author_pic']);
			$result[$i]['author_pic'] = $auth_pic->small;
		}
		
		return $result;
	}

	/**
	 * Insert a new wall post
	 *
	 * @param $data: array of post data
	 *
	 * @return true: success
	 * @return false: fail
	 */
	public function new_wall_post($data) 
	{
		return $this->db->insert('wall_posts', $data);
	}
	
	/**
	 * Insert a new comment
	 *
	 * @param $data: array of comment data
	 *
	 * @return true: success
	 * @return false: fail
	 */
	public function new_comment($data) 
	{
		return $this->db->insert('comments', $data);
	}
	
	/**
	 * Process and upload image - small, medium, and large versions
	 *
	 * @param $file: uploaded file
	 * 
	 * @return true: success
	 * @return false: fail
	 */
	public function process_image($file) {
		$error_alert = '';
		$uploaded_files = array();
		$temp_uploads_dir = 'temp_uploads';
		$perm_uploads_dir = 'uploads';
		if ($file['error'] == 1) {
			$error_alert .= "Failed to upload image ".htmlspecialchars($file['name'])."- file must be less than 6MB.<br/>";
		}
		if (is_uploaded_file($file['tmp_name'])) {
			
			if ($file['size'] > 6291456) // Check file size
				$error_alert .= "Failed to upload image ".htmlspecialchars($file['name'])."- file must be less than 6MB.<br/>";
			
			else if ($file['size'] <= 0) // Check if file is empty
				$error_alert .= "Failed to upload image ".htmlspecialchars($file['name'])."- file is empty.<br/>";
			
			else if (!$img_data = getimagesize($file['tmp_name'])) // Check if file is an image
				$error_alert .= "Failed to upload image ".htmlspecialchars($file['name'])."- file is not an image.<br/>";
			
			else {
				// Get/check extension
				switch ($img_data['mime']) {
					case 'image/jpeg':
						$ext = 'jpg';
						break;
					case 'image/gif':
						$ext = 'gif';
						break;
					case 'image/png':
						$ext = 'png';
						break;
					default:
						$ext = FALSE;
				}
				
				if (!$ext) {
					$error_alert .= "Failed to upload image ".htmlspecialchars($file['name'])."- image file must have extension JPG, PNG, or GIF.<br/>";
				}
				elseif ($error_alert == '') {
					$sizes = array(
						array("name"=>"sml", "width"=>"100", "height"=>"100"),
						array("name"=>"med", "width"=>"400", "height"=>"400"),
						array("name"=>"lrg", "width"=>"800", "height"=>"800")
					);
					foreach ($sizes as $size) {
						$name = $size['name'];
						$max_width = $size['width'];
						$max_height = $size['height'];
						
						$tmp_file_name = PUBPATH.'assets/'.$temp_uploads_dir.'/'.uniqid().'.'.$ext;
					
						$width_orig = $img_data[0];
						$height_orig = $img_data[1];
						
						if ($width_orig > $max_width || $height_orig > $max_height) {
							@$ratio_orig = $width_orig/$height_orig;
							
							if ($max_width/$max_height > $ratio_orig) 
								$max_width = $max_height*$ratio_orig;
							else 
								$max_height = $max_width/$ratio_orig;
						}
						else {
							$max_width = $width_orig;
							$max_height = $height_orig;
						}
						if (@!$image_p = imagecreatetruecolor($max_width, $max_height))
							$error_alert .= "Failed to upload image ".htmlspecialchars($file['name'])."- processing error.<br/>";
						else {
							switch ($ext) {
								case 'jpg':
									$image = imagecreatefromjpeg($file['tmp_name']);
									$imageresample = imagecopyresampled($image_p, $image, 0, 0, 0, 0, $max_width, $max_height, $width_orig, $height_orig);
									$imagecopy = imagejpeg($image_p, $tmp_file_name);
								break;
								case 'gif':						
									$image = imagecreatefromgif($file['tmp_name']);
									$imageresample = imagecopyresampled($image_p, $image, 0, 0, 0, 0, $max_width, $max_height, $width_orig, $height_orig);
									$imagecopy = imagegif($image_p, $tmp_file_name);					
								break;
								case 'png':
									$image = imagecreatefrompng($file['tmp_name']);
									$imageresample = imagecopyresampled($image_p, $image, 0, 0, 0, 0, $max_width, $max_height, $width_orig, $height_orig);
									$imagecopy = imagepng($image_p, $tmp_file_name);
								break;
								default:
									$image = FALSE;
									$imageresample = FALSE;
									$imagecopy = FALSE;
							}
							
							// Verify resample success
							if (!$image || !$imageresample || !$imagecopy)
								$error_alert .= "Failed to upload image ".htmlspecialchars($file['name'])."- processing error.<br/>";
							else {
								// On success, create random file name and upload to Temp Uploads dir
								// Store new name in $uploaded_files[] 
								$rand_num = rand(10000, 99999);
								$new_file_name = $size['name'].'_'.$rand_num.uniqid().'.'.$ext;
								if (rename($tmp_file_name, PUBPATH.'assets/'.$perm_uploads_dir.'/'.$new_file_name))
									$uploaded_files["{$size['name']}"] = $perm_uploads_dir.'/'.$new_file_name;
								else
									$error_alert .= "Failed to upload image ".htmlspecialchars($file['name'])."- processing error.<br/>";
							}
							imagedestroy($image_p);
						}
					}
					if ($error_alert == '') {
						$insert = array(
							'small' => $uploaded_files['sml'],
							'medium' => $uploaded_files['med'],
							'large' => $uploaded_files['lrg']
						);
						if ($this->db->insert('images', $insert)) {
							$img_id = $this->db->insert_id();
						}
						else {
							$error_alert .= 'Failed to upload image';
						}
					}
				}
			}
		}
		if ($error_alert == '')
			return $img_id;
		else {
			echo $error_alert;
			return false;
		}
	}
}