<?php

class EAP_DeviceSecure{
	
	var $init;
	var $inithandle;
	var $enabled;
	var $handle;
	var $requestParams;
	var $responseData;
	var $authData;
	var $LOG_NAME = "DeviceSecure";
	var $vopWSDL = "https://webservice.eaponline.de/webservice.php?wsdl";
	var $vopWSDLDEV = "https://webservice.eaponline.de/ws_dev.php?wsdl";
	var $warenkorb;
	var $functions;
	var $pluginSettings;
	var $settingsArray;
	var $requested;
	var $sprachArr;
	var $smarty;

	var $accid = "SF-VOP-Prod-O1006-1006";
	var $apikey = "FFYDt1qVVB9xHia59N37UkEaC5z";
	var $snippetid = '1eSQKx';
	var $snippet_output = false;	
	var $request_called = 0;
	var $RetryIfHandleChanged = false;
	var $maxScore = 5432;
	
	 function __construct($functions) {
	  $this->functions = $functions;
	 
   }
   
	public function VOPTestModus()
	{
		 if($_SERVER['REMOTE_ADDR']=="62.225.158.106")
		 {
			 // VOP IP -> TESTZUGANG
			  $this->accid = "SF-VOP-Test-O1005-1005";
			  $this->apikey = "84j5163lMRt88wA8ObVIcsMB8EU";
	 		  $this->snippetid = 'hDN0Gj';
		 }
	}
   public function checkEnabled($oPlugin,$smarty,$settings,$requestparams)
   {
	    $this->VOPTestModus();
	    $this->requestParams = $requestparams;
	   	$this->pluginSettings =  $oPlugin;
	   	$this->smarty = $smarty;
		$this->settingsArray = $settings;
	   	$this->enabled = false;
	   	$this->sprachArr = null;
		if($this->settingsArray["jtl_eap_devicesecure"]==0) return false;
		
	 	$oPluginSprache = gibSprachVariablen($this->pluginSettings->kPlugin);
		$cLang = strtoupper($this->smarty->get_template_vars('lang'));

		foreach($oPluginSprache as $key)
		{
		 $this->sprachArr[$key->cName] = $key->oPluginSprachvariableSprache_arr[$cLang];
		}
		
		return true;
	   
   }
		function getDeviceSecure()
		{
			$requestparams =  base64_encode(serialize($this->functions->clearDataRechnungsadresse($this->requestParams["Rechnungsadresse"])));;
			$e = null;
			if(!$this->snippet_output) return;
			if($this->requested) return;
		 try
		{
			$client = new SoapClient($this->vopWSDL,array(  'encoding' => 'UTF-8', 'cache_wsdl' => WSDL_CACHE_NONE));
			$result = $client->getDeviceSecure($this->settingsArray['jtl_eap_userid'],md5($this->settingsArray['jtl_eap_passwort']),"0",session_id(),$requestparams,$this->requestParams["Warenkorb"]);
			$this->responseData = $result;
			$this->requested = true;

		}
		catch(Exception $e)
		{
			$this->responseData->secure_payment = true;
			$this->requested = true;
		}
			$this->functions->createLog($this,$e);
		}

		function createSnippet()
		{
		
		if(!$this->snippet_output)
		{
			$token = session_id();
			$snippet = $this->snippetid;
			$snippetCode = "<script type=\"text/javascript\">
      var di = {t:'$token',v:'$snippet',l:'Checkout'};
     <!-- di.va = 'onlineShipment%3D${onlineShipment}%26paymentType%3D${paymentType}%26knownAddress%3D${knownAddress}%26age%3D${age}%26riskProduct%3D${riskProduct}%26cartValue%3D${cartValue}%26newCustomerAccount%3D${newCustomerAccount}%26industrySector%3D${industrySector}%26packstation%3D${packstation}%26countryOfOrder%3D${countryOfOrder}%26voucherUsed%3D${voucherUsed}%26shipmentType%3D${shipmentType}' -->;
  </script>
  <script type=\"text/javascript\" src=\"//www.dev-secu.de/$snippet/di.js\"></script>
  <noscript>
      <link rel=\"stylesheet\" type=\"text/css\" href=\"//www.dev-secu.de/di.css?t=$token&amp;v=$snippet&amp;l=Checkout&amp;va=onlineShipment%3D${onlineShipment}%26paymentType%3D${paymentType}%26knownAddress%3D${knownAddress}%26age%3D${age}%26riskProduct%3D${riskProduct}%26cartValue%3D${cartValue}%26newCustomerAccount%3D${newCustomerAccount}%26industrySector%3D${industrySector}%26packstation%3D${packstation}%26countryOfOrder%3D${countryOfOrder}%26voucherUsed%3D${voucherUsed}%26shipmentType%3D${shipmentType}\">
  </noscript> 
  <object type=\"application/x-shockwave-flash\" data=\"//www.dev-secu.de/$snippet/c.swf\" width=\"0\" height=\"0\">
      <param name=\"movie\" value=\"//www.dev-secu.de/$snippet/c.swf\" />
      <param name=\"flashvars\" value=\"t=$token&amp;v=$snippet&amp;l=Checkout&amp;va=onlineShipment%3D${onlineShipment}%26paymentType%3D${paymentType}%26knownAddress%3D${knownAddress}%26age%3D${age}%26riskProduct%3D${riskProduct}%26cartValue%3D${cartValue}%26newCustomerAccount%3D${newCustomerAccount}%26industrySector%3D${industrySector}%26packstation%3D${packstation}%26countryOfOrder%3D${countryOfOrder}%26voucherUsed%3D${voucherUsed}%26shipmentType%3D${shipmentType}\"/>
      <param name=\"AllowScriptAccess\" value=\"always\"/>
  </object>";
  		pq($this->settingsArray['jtl_eap_selector_footer'])->before($snippetCode);
 		$this->snippet_output = true;
		}
		}
}