<?php
class Curl 
{
    public function apiCall($url, array $data, $method, array $header, $username='', $password='')
    {
    	$params = '';

        if($data != '')
        {
            $params = http_build_query($data);
        }

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        if($method=='POST')
        {
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, count($data));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        }
        else if($method=='GET')
        {
            curl_setopt($ch, CURLOPT_URL, $url.'?'.$params );
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Will return the response, if false it print the response

        if(!empty($username)&&!empty($password))
        {
            curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        }

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $result = curl_exec($ch);
        curl_close($ch);

        return json_decode($result, true);
    }

    public function __get($var)
    {
        return get_instance()->$var;
    }
}
?>