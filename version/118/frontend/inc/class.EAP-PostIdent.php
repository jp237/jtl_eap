<?php
class EAP_PostIdent {
	var  	$TokenEndpoint = "https://postident.deutschepost.de/api/v2/token";
	var 	$apiUrl = "https://postident.deutschepost.de/api/v2/authorize";
	var 	$redirectURL = "http://jtl404.ihr-onlineshop.com/bestellvorgang.php";
	var 	$request = array();
	var 	$vopWSDL = "https://webservice.eaponline.de/DPAGService.php?wsdl";
	var 	$pluginConf;

	var $init;
	var $enabled;
	var $handle;
	var $requestParams;
	var $requested;
	var $responseData;
	var $idToken;
	var $authData;
	var $functions;
	var $pluginSettings;
	var $settingsArray;
	var $sprachArr;
	var $smarty;	
	var $secure_payments;
	var $RetryIfHandleChanged = false;
	var $completed;
	var $verified;
	var $LOG_NAME = "PostIdent";
	
	public function setAuthCode($code,$state)
	{
		$this->request['code'] = $code;
		$this->request['state'] = $state;
		
		return true;
	}
	public function __construct($functions)
	{
		$this->functions = $functions;
	}
	
	
	public function getVerifiedFromToken()
	{
		if($this->requested) return;
		$e = null;
		try
		{
			$requestParams = base64_encode(serialize($this->functions->clearDataRechnungsadresse($this->requestParams["Rechnungsadresse"])));
			$client = new SoapClient($this->vopWSDL,array(   'cache_wsdl' => WSDL_CACHE_NONE));
			$result = $client->getDPAGJWT($this->settingsArray['jtl_eap_userid'],md5($this->settingsArray['jtl_eap_passwort']),$this->idToken,$requestParams);
			
			$this->responseData = $result;
			$this->secure_payments = $this->responseData->verified == false ? true: false;
			$this->requested = true;
			$this->responseData->secure_payment = $this->secure_payments;
			$this->verified = $this->responseData->verified;
			$this->functions->createLog($this,$e);
			$type = $this->requireFullIDCard() == true ? 2:1;
			$this->functions->verifiedAndMovedToKundengruppe($this,$type);
			return $this->responseData->verified;

		}
			catch(Exception $e)
		{ 
		$this->functions->createLog($this,$e);
		$this->secure_payments = false;
		$this->responseData->secure_payment = true;
		$this->responseData->error = true;
		$this->responseData->verified = false;
				return false;
		}
		
	
	}
	
	
		public function requireFullIDCard()
	{
		$checkVal = $this->settingsArray['jtl_eap_postident_warenkorb'];
		$checkVal = $checkVal*100;
		if($checkVal>0 && $checkVal <= intval($this->requestParams["Warenkorb"]))
		{
			$lockedPayments = $this->functions->lockedPaymentsArray();
			foreach($lockedPayments as $payment)
			{
				if($this->requestParams['Zahlungsart']->kZahlungsart == $payment->kZahlungsart && $this->requestParams['art']=="B2C") return true;
			}
		}
		return false;
	}
		
	public function VOPTestModus()
	{
		if($_SERVER['REMOTE_ADDR']=="62.225.158.106")
		{
	 	$this->TokenEndpoint = "https://postident-demo.deutschepost.de/api/v2/token";
		$this->apiUrl = "https://postident-demo.deutschepost.de/api/v2/authorize";
		}
	
	}
	  public function checkEnabled($oPlugin,$smarty,$settings,$requestparams)
   {
	   $this->VOPTestModus();
	   	$this->requestParams = $requestparams;
		if(strlen($this->requestParams["Rechnungsadresse"]->cFirma)>3) return false;
	   	$this->pluginSettings =  $oPlugin;
	   	$this->smarty = $smarty;
		$this->settingsArray = $settings;
		
	
	   	$this->enabled =$this->settingsArray['jtl_eap_identcheck_use']=="2" ? true:false;
	   	
		if($this->enabled==false && $this->requireFullIDCard() == false ) return false;
	   	$this->sprachArr = null;
	 	$oPluginSprache = gibSprachVariablen($this->pluginSettings->kPlugin);
		$cLang = strtoupper($this->smarty->get_template_vars('lang'));
		
		foreach($oPluginSprache as $key)
		{
			$this->sprachArr[$key->cName] = $key->oPluginSprachvariableSprache_arr[$cLang];
		}
		return true;
   }
   
	function sendRedirectToAuthPage($state,$qbitlvl)
{
	
	$idcard = "idcard2";
	//if($this->requireFullIDCard()) $idcard = "idcard4";
	if($this->requireFullIDCard()) $idcard = "idcard5";
	//if($qbitlvl> 0 && $this->requireFullIDCard()) $idcard = "idcard5";
	$current_url = explode("?", $_SERVER['REQUEST_URI']);
	$this->redirectURL = $current_url[0] ;
    $this->redirectURL = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . "{$_SERVER['HTTP_HOST']}".$current_url[0];
	$url = $this->apiUrl."&response_type=code&client_id=".$this->settingsArray['jtl_eap_postident_clientID']."&reason=IhrEinkauf&redirect_uri=".$this->redirectURL."&scope=openid+$idcard&state=$state";
	echo "<script>window.location.href='$url'</script>";
}
	
	public function getAccessTicket()
	{
		
		$url = $this->TokenEndpoint;
		$fields = array(
						'grant_type' => "authorization_code",
						'code' => ($this->request['code']),
						'redirect_url' => ($this->redirectURL),
		);
		
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
			curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
			curl_setopt($ch, CURLOPT_VERBOSE, TRUE);
			curl_setopt($ch,CURLOPT_POSTFIELDS, http_build_query($fields));
			curl_setopt($ch, CURLOPT_USERPWD,$this->settingsArray['jtl_eap_postident_clientID'].":".$this->settingsArray['jtl_eap_postident_clientsecret']);  
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE) ;
			curl_setopt($ch, CURLOPT_POST, TRUE);
			
			 curl_setopt($ch, CURLOPT_STDERR, @$fp);
		
			curl_setopt($ch, CURLOPT_FAILONERROR, TRUE);
			
			
			$response = curl_exec($ch);
			$res = json_decode($response);
			$curlInfo = curl_getinfo($ch);

			curl_close($ch);
			
			if(substr($curlInfo['http_code'],0,1)=="2")
			{
				$this->idToken = isset($res->id_token) ? $res->id_token : null;
				return true;
			}
			
		
		return false;
	}
  
	
	
}