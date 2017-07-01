<?php defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH.'/libraries/REST_Controller.php';
class Lwy extends CI_Controller
{

	public  function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->helper(array('form', 'url','url_helper'));
		$this->load->model('lwy_model');
		$this->load->library('form_validation');
		header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Headers:*'); 
	}

	private function response($data,$ret=200,$msg=null)
	{
        $response=array('ret'=>$ret,'data'=>$data,'msg'=>$msg);
        $this->output
            ->set_status_header($ret)
            ->set_header('Cache-Control: no-store, no-cache, must-revalidate')
            ->set_header('Pragma: no-cache')
            ->set_header('Expires: 0')
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($response))
            ->_display();
        exit;
    }


	public function index()
	{
		$this->load->view('reg');
	}

	public function login()
	{
		$this->load->view('Login');
	}
	public function logining()
	{

			$email = $this->input->post('email');
			$password = $this->input->post('password');
			// echo "$email";
			// echo "$password";
			
			$this->form_validation->set_rules('email','Email','required|valid_email');
			$this->form_validation->set_rules('password','password','required');

			

			
			if ($this->form_validation->run() == FALSE)
        	{
        		// echo "$password";
        		// echo "flaut";
            	$this->load->view('login');
        	}
       		else
        	{
        		$user = $this->lwy_model->get_userinfo($email);
        		print_r($user);
        		$data = $user;
        		// print_r($data);
        		if(password_verify($password,$data['pwd']))
				{
					$data['token'] = $this->jwt->encode(['exp'=>time()+3600,'id'=>$data['id'],'email'=>$data['email'],'username'=>$data['username']],$this->config->item('encryption_key'));
					//$data['token'] = "token";
					// print_r($data);
					// echo "<br /> 123";
					
            		$this->load->view('success',$data);

					// print_r($data['token']);
            	}
            	else
            	{
            		$this->load->helper('form');
					echo '用户名或密码错误！';
				$this->load->view('login');
				}
            	//print_r($data);
        	}
	}
	public function reg()
	{
		$this->load->view('reg');
	}

	public function register()
	{
		$email = $this->input->post('email');
		$username = $this->input->post('username');
		$password = $this->input->post('password');
		$password = password_hash($password,PASSWORD_DEFAULT);

		$this->form_validation->set_rules('email','Email','required|valid_email|is_unique[user.email]',
				array(
      		  		'required'  => 'You have not provided %s.',
     		   		'is_unique' => 'This %s already exists.'
   				 ));
		$this->form_validation->set_rules(
    			'username', 'Username',
    			'required|min_length[2]|max_length[12]|is_unique[user.username]',
   				array(
      		  		'required'  => 'You have not provided %s.',
     		   		'is_unique' => 'This %s already exists.'
   				 )
		);
		$this->form_validation->set_rules('password','password','required|min_length[5]');
		$this->form_validation->set_rules('passconf', 'Password Confirmation', 'required|matches[password]');

		if ($this->form_validation->run() == FALSE)
        	{
        		echo "flaut";
            	$this->load->view('reg');
        	}
       		else
        	{

        		$this->lwy_model->save($email,$username,$password);
        		$data['token'] = $this->jwt->encode(['exp'=>time()+3600,'id'=>$data['id'],'email'=>$data['email'],'username'=>$data['username']],$this->config->item('encryption_key'));
            	$this->load->view('success',$data);
            	//print_r($data);
        	}



	}


}


 ?>