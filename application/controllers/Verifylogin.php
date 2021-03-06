<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
class VerifyLogin extends CI_Controller {
 
public function __construct()
{
   parent::__construct();
   $this->load->model('user','',TRUE);
}
 
public function index()
{
   //This method will have the credentials validation
   $this->load->library('form_validation');
 
   $this->form_validation->set_rules('username', 'Username', 'trim|required');
   $this->form_validation->set_rules('password', 'Password', 'trim|required|callback_check_database');
 
   if($this->form_validation->run() == FALSE)
   {
		$data = array();
		$CI =& get_instance();
     //Field validation failed.  User redirected to login page
     		$session_data = $this->session->userdata('logged_in');
	    	$data['username'] = $session_data['username'];
	    	$data['menu'] = $CI->db->get('menu');
		$data['submenu'] = $CI->db->get('submenu');
		$this->load->view('header', $data);
		$this->load->view('login_view', $data);
		$this->load->view('footer', $data);
   }
   else
   {
     //Go to private area
     redirect('welcome/admin', 'refresh');
   }
 
}
 
public function check_database($password)
{
   //Field validation succeeded.  Validate against database
   $username = $this->input->post('username');
 
   //query the database
   $result = $this->user->login($username, $password);
 
   if($result)
   {
     $sess_array = array();
     foreach($result as $row)
     {
       $sess_array = array(
         'id' => $row->id,
         'username' => $row->username
       );
       $this->session->set_userdata('logged_in', $sess_array);
     }
     return TRUE;
   }
   else
   {
     $this->form_validation->set_message('check_database', 'Invalid username or password');
     return false;
   }
}
}
?>