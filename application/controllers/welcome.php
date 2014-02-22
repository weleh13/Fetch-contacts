<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
        $this->load->config('contacts_api');
        $this->load->library('contacts_api');
    }

    public function index() {
		$this->load->view('welcome_message');
    }

    public function getGoogleResponse(){

        $this->contacts_api->setGoogleRedirectURL(base_url().'welcome/getGoogleResponse');
        $result=$this->contacts_api->getGoogleContacts();
        
        foreach ($result as $key=>$title) {
            $email=strval($title->attributes()->address);
            $this->prepare_email($email);
        }
    }

    public function connectYahoo() {
        $this->contacts_api->YahooInit();
        redirect('welcome/getYahooResponse');
    }

    public function getYahooResponse(){
        $result=$this->contacts_api->getYahooContacts();
        $this->prepare_email($result);
    }


    public function connectLive() {
        $this->contacts_api->setLiveRedirectURL(base_url().'welcome/getLiveResponse');
        redirect($this->contacts_api->genLiveRedirectURL());
    }

    public function getLiveResponse(){
        $this->contacts_api->setLiveRedirectURL(base_url().'welcome/getLiveResponse');
        //XML Response
        $result=$this->contacts_api->getLiveContacts();
        $this->prepare_email($result);
    }

    public function prepare_email($email){
    	echo "<pre>";
    	var_dump($email);
    }

}