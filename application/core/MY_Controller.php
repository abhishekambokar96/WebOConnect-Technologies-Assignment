<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{
	public function __construct()
    {
        parent::__construct();
    }
}

class AdminController extends CI_Controller
{
    public $header_data = array();

	public function __construct()
	{
		parent::__construct();

		$this->load->library(array('an_lib/curl'));

        /*$this->load->helper('url');*/

        $session_active=$this->session->userdata('session_active');

        if($session_active !== 'yes')
        {            
            redirect('/','refresh');
        }

        $this->header_data['title'] = 'SIGCE - ';
	}

	public function is_logged_in()
    {   
        
    }

    public function authenticate()
    {   

    }
}