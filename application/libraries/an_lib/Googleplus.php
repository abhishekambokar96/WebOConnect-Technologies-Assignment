<?php defined('BASEPATH') OR exit('No direct script access allowed');

require_once 'vendor/autoload.php';

class Googleplus {
	
	public function __construct() 
	{
		$this->load->config('googleplus');
		
		$this->client = new Google_Client();
		/*$this->client->setApplicationName($CI->config->item('Authenticookapplication_name'));*/
		/*$this->client->setDeveloperKey($CI->config->item('googleplus_api_key'));*/
		$this->client->setClientId($this->config->item('googleplus_client_id'));
		$this->client->setClientSecret($this->config->item('googleplus_client_secret'));
		$this->client->setRedirectUri(base_url().$this->config->item('googleplus_redirect_uri'));
		$this->client->setScopes($this->config->item('googleplus_scopes'));
		$this->client->setAccessType('online');
		$this->client->setApprovalPrompt('auto');
		$this->client->setHttpClient(new GuzzleHttp\Client(['verify' => false]));
		$this->oauth2 = new Google_Service_Plus($this->client);

	}
	
	public function loginURL()
	{
        return $this->client->createAuthUrl();
    }
	
	public function getAuthenticate($code)
	{
        return $this->client->authenticate($code);
    }
	
	public function getAccessToken()
	{
        return $this->client->getAccessToken();
    }
	
	public function setAccessToken()
	{
        return $this->client->setAccessToken();
    }
	
	public function revokeToken()
	{
        return $this->client->revokeToken();
    }
	
	public function getUserInfo()
	{
        return $this->oauth2->people->get('me');
    }

    public function __get($var)
    {
        return get_instance()->$var;
    }
}
?>