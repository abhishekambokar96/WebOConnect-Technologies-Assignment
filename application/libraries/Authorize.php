<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Autorize
*
* Author: Rajeshkumar Nadar
*		  nadar.rajeshnadar@gmail.com
*
*
* Description:  validate weather the user is available.
*
* Requirements: PHP5 or above
*
*/

class Authorize{

	public function __construct()
	{
		$this->load->library('session');		
		$this->load->helper(array('cookie', 'language','url'));
		//$this->load->model('lauthm');
		// $this->load->model('role-management/Menu_model');
	}	

	public function __get($var)
	{
		return get_instance()->$var;
	}

	public function checkAliveSession($session_for=1){ 
		$session_active=$this->session->userdata('is_logged_in');
		if($session_active == 1){			
			return true;
		}
		return false;
	}
	
	
	
}
?>