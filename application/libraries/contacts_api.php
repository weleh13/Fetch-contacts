<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class Contacts_api {

  //Google Paramethers Vars
	private $GoogleClientId;
	private $GoogleClientSecret;
	private $GoogleApplicationName;
	private $GooglesetScopes;
	private $GoogleredirectURL;
  //Yahoo Paramethers Vars
  private $YahooClientKey;
  private $YahooAppID;
  private $YahooClientSecret;
  //Live Paramethers Vars
  private $LiveClientKey;
  private $LiveClientSecret;

	public function __construct($config){
    //Google Paramethers Vars
		$this->GoogleClientId=$config['setGoogleClientId'];
		$this->GoogleClientSecret=$config['setGoogleClientSecret'];
		$this->GoogleApplicationName=$config['setGoogleApplicationName'];
		$this->GooglesetScopes=$config['setGoogleScopes'];
    //Yahoo Paramethers Vars
    $this->YahooClientKey=$config['setYahooClientKey'];
    $this->YahooAppID=$config['setYahooAppID'];
    $this->YahooClientSecret=$config['setYahooClientSecret'];
    //Live Paramethers Vars
    $this->LiveClientKey=$config['setLiveClientKey'];
    $this->LiveClientSecret=$config['setLiveClientSecret'];
	}

	public function setGoogleRedirectURL($url){
		$this->GoogleredirectURL=$url;
	}

  public function getGoogleContacts()
  {

  	require_once APPPATH."third_party/Google/Google_Client.php";
  	$client = new Google_Client();
    $client->setApplicationName($this->GoogleApplicationName);
    $client->setScopes($this->GooglesetScopes);
    $client->setClientId($this->GoogleClientId);
    $client->setClientSecret($this->GoogleClientSecret);
    $client->setRedirectUri($this->GoogleredirectURL);
    $client->authenticate();

    $req = new Google_HttpRequest("https://www.google.com/m8/feeds/contacts/default/full?updated-min=2005-03-16T00:00:00");
    $val = $client->getIo()->authenticatedRequest($req);
    $xml =  new SimpleXMLElement($val->getResponseBody());
    $xml->registerXPathNamespace('gd', 'http://schemas.google.com/g/2005');
    $result = $xml->xpath('//gd:email');
    return $result;
  }

  public function YahooInit() {
  	require_once APPPATH."third_party/Yahoo/lib/Yahoo.inc"; 
    YahooSession::clearSession();
  }

  public function getYahooContacts(){
    	require_once APPPATH."third_party/Yahoo/lib/Yahoo.inc"; 
      define('OAUTH_CONSUMER_KEY', $this->YahooClientKey);
      define('OAUTH_CONSUMER_SECRET', $this->YahooClientSecret);
      define('OAUTH_APP_ID', $this->YahooAppID);
      // debug settings
      YahooLogger::setDebug(true);
      YahooLogger::setDebugDestination("true");
      

      // check for the existance of a session.
      // this will determine if we need to show a pop-up and fetch the auth url,
      // or fetch the user's social data.
      $hasSession = YahooSession::hasSession(OAUTH_CONSUMER_KEY, OAUTH_CONSUMER_SECRET, OAUTH_APP_ID);
      
      if($hasSession == FALSE) {
        // create the callback url,
        $callback = YahooUtil::current_url();
        // pass the credentials to get an auth url.
        // this URL will be used for the pop-up.
        $auth_url = YahooSession::createAuthorizationUrl(OAUTH_CONSUMER_KEY, OAUTH_CONSUMER_SECRET, $callback);
        redirect($auth_url);
      } else {
          // pass the credentials to initiate a session
          $session = YahooSession::requireSession(OAUTH_CONSUMER_KEY, OAUTH_CONSUMER_SECRET, OAUTH_APP_ID);
          // if a session is initialized, fetch the user's profile information
          if($session) {
            // Get the currently sessioned user.
            $user = $session->getSessionedUser();
            // Load the profile for the current user.
            $profile = $user->getProfile();
            return $profile;
          } 
      }
    }

    public function setLiveRedirectURL($url){
      $this->LiveRedirectURL=$url;
    }

    public function genLiveRedirectURL(){
      $LiveClientKey=$this->LiveClientKey;
      $LiveRedirectURL=$this->LiveRedirectURL;
      $URL = 'https://login.live.com/oauth20_authorize.srf?client_id='.$LiveClientKey.'&scope=wl.basic&response_type=code&redirect_uri='.$LiveRedirectURL;
      return $URL;
    }

    public function getLiveContacts(){
        $LiveClientKey = $this->LiveClientKey;
        $LiveClientSecret = $this->LiveClientSecret;
        $LiveRedirectURL = $this->LiveRedirectURL;
        $auth_code = $_GET['code'];
        $fields=array(
            'code'=>  urlencode($auth_code),
            'client_id'=>  urlencode($LiveClientKey),
            'client_secret'=>  urlencode($LiveClientSecret),
            'redirect_uri'=>  urlencode($LiveRedirectURL),
            'grant_type'=>  urlencode('authorization_code')
        );
        $post = '';
        foreach($fields as $key=>$value) { 
            $post .= $key.'='.$value.'&'; 
        }
        $post = rtrim($post,'&');
        $curl = curl_init();
        curl_setopt($curl,CURLOPT_URL,'https://login.live.com/oauth20_token.srf');
        curl_setopt($curl,CURLOPT_POST,5);
        curl_setopt($curl,CURLOPT_POSTFIELDS,$post);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,TRUE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER,0);
        $result = curl_exec($curl);
        curl_close($curl);
        $response =  json_decode($result);
        $accesstoken = $response->access_token;
        $url = 'https://apis.live.net/v5.0/me/contacts?access_token='.$accesstoken.'&limit=200';
        $xmlresponse =  $this->curl_file_get_contents($url);
        $xml = json_decode($xmlresponse, true);
        return $xml;
    }

    function curl_file_get_contents($url) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
            $data = curl_exec($ch);
            curl_close($ch);
            return $data;
    }
}