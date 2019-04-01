<?php
class EAP_Bonitaetspruefung{
	var $vopWSDL = "https://webservice.eaponline.de/webservice.php?wsdl";
	var $init;
	var $initHandle;
	var $enabled;
	var $handle;
	var $warenkorb;
	var $requestParams;
	var $responseData;
	var $authData;
	var $functions;
	var $pluginSettings;
	var $settingsArray;
	var $sprachArr;
	var $smarty;	
	var $RetryIfHandleChanged = true;
	var $LOG_NAME = "Bonitätsprüfung";

	  function __construct($functions) {
	  $this->functions = $functions;
   }

   public function doRequest($handle)
   {
	   
	  

	   if(($this->handle != null && $this->handle == $handle) && ($this->warenkorb != null && $this->warenkorb == $warenkorb)) {
		    return;
	   }
	   	
		if(!$this->functions->istGesperrt()) return;

	  	if($this->requestParams["Rechnungsadresse"]->cLand != "DE" && $this->settingsArray['jtl_eap_ausland'] == 2) return;

	   $Rechnungsadresse = $this->functions->clearDataRechnungsadresse($this->requestParams["Rechnungsadresse"]);
	   $Lieferadresse = $this->functions->clearDataRechnungsadresse($this->requestParams["Lieferadresse"]);
	   $warenkorb = $this->requestParams["Warenkorb"];
	   	
	   if($this->functions->checkKundengruppeBoni($this->requestParams["Rechnungsadresse"]->kKundengruppe)) return ;
	
	//	$warenkorb = 13;
		$this->requested = null;
		$this->responseData = null;
		$this->handle = null;
		$this->warenkorb = null;
		
		$this->handle = $handle;
		$this->warenkorb = $warenkorb;
	  	$selected_payment = "Unbekannt";
	
			if(isset($_POST['Zahlungsart']))
		{
		$get_payment =	$this->functions->dbQuery("SELECT cName FROM tzahlungsart where kZahlungsart = " . (int)$_POST['Zahlungsart'],1);
	  	$this->requestParams["Zahlungsart"]->cName = $get_payment->cName;
		$this->requestParams["Zahlungsart"]->kZahlungsart = $_POST['Zahlungsart'];
		
		}
		$e=null;
		$request = $Rechnungsadresse;
		
		
	   if($this->requestParams["art"] == "B2C" )
	   {
		
		   if($this->settingsArray['jtl_eap_b2c']==1){ 
		   		 $this->requested = true; 
				 $this->responseData->secure_payment = true; 
				  
			}
			else  if($this->settingsArray['jtl_eap_b2c']==2){ 
		   		 $this->requested = true; 
				 $this->responseData->secure_payment = false;
				 
			}
			else
			{
				try
				{
				$client = new SoapClient($this->vopWSDL,array(  'encoding' => 'UTF-8', 'cache_wsdl' => WSDL_CACHE_NONE));
				
				$result = $client->getSCHUFAB2C($this->settingsArray['jtl_eap_userid'],md5($this->settingsArray['jtl_eap_passwort']),"0","shoplogin",
										$request['Vorname'],$request['Nachname'],$request['geschlecht'],$request['geb'],
										$request['Strasse'],$request['PLZ'],$request['Ort'],$request['mail'],$request['tel'],$request['ip'],$warenkorb,"FALSE","","","","",$request['land'],$this->requestParams["Zahlart"]->cName,"","");
				
				$this->responseData = $result;
				$this->requested = true;
				}catch(Exception $e)
				{
					$this->requested = true;
					$this->responseData->secure_payment= true;
				}
			}
			
	   }
	   else
	   {
		      if($this->settingsArray['jtl_eap_b2b']==1){ 
		   		 $this->requested = true; 
				 $this->responseData->secure_payment = true; 

			}
			else  if($this->settingsArray['jtl_eap_b2b']==2){ 
		   		 $this->requested = true; 
				 $this->responseData->secure_payment = false; 
			}
			else
			{
				
				try
				{
				$client = new SoapClient($this->vopWSDL,array(  'encoding' => 'UTF-8', 'cache_wsdl' => WSDL_CACHE_NONE));
			
				$result = $client->getSCHUFAB2B($this->settingsArray['jtl_eap_userid'],md5($this->settingsArray['jtl_eap_passwort']),"0","shoplogin",
										$request['Firma'],"",$request['Strasse'],$request['PLZ'],$request['Ort'],$request['land'],$this->requestParams["Zahlart"]->cName,"",$warenkorb);
				$this->responseData = $result;
				$this->requested = true;
				}catch(Exception $e)
				{
				$this->requested = true;
				$this->responseData->secure_payment= true;
				}
				
			}
	   }
	   	$this->functions->createLog($this,$e);
   }
	  public function checkEnabled($oPlugin,$smarty,$settings)
   {
	   	$this->pluginSettings =  $oPlugin;
	   	$this->smarty = $smarty;
		$this->settingsArray = $settings;
	   	$this->enabled = true;
	   	$this->sprachArr = null;
	 	$oPluginSprache = gibSprachVariablen($this->pluginSettings->kPlugin);
		$cLang = strtoupper($this->smarty->get_template_vars('lang'));
		
		foreach($oPluginSprache as $key)
		{
		 $this->sprachArr[$key->cName] = $key->oPluginSprachvariableSprache_arr[$cLang];
		}
		
		return true;
	   
   }
  
}