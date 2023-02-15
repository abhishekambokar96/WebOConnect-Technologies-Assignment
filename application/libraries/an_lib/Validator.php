<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Validator {

	public function __construct()
    {
        $this->load->library('form_validation');
    }
    
    public function validate()
    {
        if($this->form_validation->run() == FALSE)
		{
			if($this->input->is_ajax_request())
			{
				$post_data = $this->input->post();
				$error_data = $this->form_validation->error_array();
				
				foreach ($post_data as $key => $value)
				{
					if(is_array($value))
					{
						foreach ($value as $k => $v) 
						{
							$old_key = $new_key = (isset($error_data[$key.'[]']) && $error_data[$key.'[]']) ? $key.'[]' : $key.'['.$k.']';
							/*$old_key = $key.'['.$k.']';*/

							$ajax_error[] = $this->ajax_error_message($error_data, $new_key, $old_key);
						}
					}
					else
					{
						$ajax_error[] = $this->ajax_error_message($error_data, $key, $key);
					}
				}

				if(isset($_FILES))
				{
					foreach ($_FILES as $key => $value)
					{
						$new_key = (is_array($value['type'])) ? $key.'[]' : $key;
						$ajax_error[] = $this->ajax_error_message($error_data, $new_key, $new_key);
					}
				}

				echo json_encode($ajax_error);
				exit();
			}
			else
			{
				return FALSE;
			}
		}
		else
		{
			return TRUE;
		}
    }

    public function file_validation($files, $is_true = TRUE)
    {
    	foreach($files as $key => $value) 
    	{
    		$field = $value['field'];
	        $this->form_validation->set_rules($field, 'File', array(
			    array('my_custom_validation', function () use ($value, $is_true) {

			    	$field = preg_replace('/\[[^\]]*\]/', '', $value['field']);

			    	if(array_key_exists('rules', $value) && !empty($value['rules']))
			    	{
			    		$rules_data = explode('|', $value['rules']);

			    		$file_size = 0;
			    		$size = preg_grep('/size/', $rules_data);
			    		if(!empty($size))
			    		{
			    			$a = array_values($size);
			    			preg_match('/\d+/is', array_shift($a), $matches);
			    			$file_size = array_shift($matches);
			    		}	

			    		if(is_array($_FILES[$field]['type']))
						{
							foreach($_FILES[$field]['type'] as $a => $b)
							{
								$s[] = $this->check_error($rules_data, $_FILES[$field]['type'][$a], $_FILES[$field]['size'][$a], $file_size, $value, $is_true);
							}

							return $this->in_array_all(1, $s);
						}
						else
						{
							return $this->check_error($rules_data, $_FILES[$field]['type'], $_FILES[$field]['size'], $file_size, $value, $is_true);
				    	}
			    	}
			    }),
		  	));
	  	}
    }

    public function check_error($rules_data, $file_type, $file_size, $validate_size, $value, $is_true)
    {
    	if(in_array('required', $rules_data) && empty($file_type))
		{
			$required_message = (array_key_exists('required', $value['errors'])) ? $value['errors']['required'] : 'Field is empty';
			$this->form_validation->set_message('my_custom_validation', $required_message);
    		return FALSE;
		}
		else if($file_size > $validate_size && $is_true)
		{
			$size_message = (array_key_exists('size', $value['errors'])) ? $value['errors']['size'] : 'Too Large';
			$this->form_validation->set_message('my_custom_validation', $size_message);
			return FALSE;
		}
		else if(!in_array($file_type, $value['mime']) && $is_true)
		{
			$mime_message = (array_key_exists('mime', $value['errors'])) ? $value['errors']['mime'] : 'Different File';
			$this->form_validation->set_message('my_custom_validation', $mime_message);
    		return FALSE;
		}
		else
		{
			return TRUE;
		}
    }

    public function ajax_error_message($error_data, $key, $new_key)
    {
    	$error_value['key'] = $new_key;
		$error_value['success'] = 1;
		$error_value['msg'] = 'Successful';

		if(array_key_exists($key, $error_data))
		{
			$error_value['success'] = 0;
			$error_value['msg'] = $error_data[$key];
		}

		return $error_value;
    }

    public function in_array_all($value, $array)
	{
	  return (reset($array) == $value && count(array_unique($array)) == 1);
	}

	public function __get($var)
    {
        return get_instance()->$var;
    }
}