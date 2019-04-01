<?php
	include_once "class.EAP-DeviceSecure.php";
	include_once "class.JTL-Shop.EAP.php";
	include_once "EAP_Postdirekt.php";
	include_once "class.EAP-Bonitaetspruefung.php";
	include_once "class.EAP-IdentCheck.php";
class EAPBoniGateway{


	var $wsdlURL = "https://api.eaponline.de/bonigateway.php?wsdl";
	var $pluginSettings;
	var $settingsArray;
	var $requestParams;

	// * PRÜFUNGSAKTIONEN....
	var $deviceSecure;
	var $schufaIdent;
	var $schufaBoni;
	// * PRÜFUNGSAKTIONEN....
	var $smarty;
    /** @var EAP_Functions  */
	var $functions;
	var $versandArt;
	var $requested;
	var $sprachArr;
	var $checkout_session;
	var $card_changed = 0;
	var $current_card = 0;
	var $pq_footer;
	var $pq_zahlung;
	var $pq_alert;
	var $pq_warenkorb;

	public function getCurrentPluginConfig($oPlugin,$smarty)
	{
		$this->pluginSettings = $oPlugin;
		$this->smarty = $smarty;
		$this->settingsArray = $this->functions->createSettingsArray($this->pluginSettings->oPluginEinstellung_arr);

		$this->pq_alert = $this->settingsArray['jtl_eap_selector_alert'];
		$this->pq_footer = $this->settingsArray['jtl_eap_selector_footer'];
		$this->pq_zahlung = $this->settingsArray['jtl_eap_selector_zahlung'];
		$this->pq_warenkorb = $this->settingsArray['jtl_eap_selector_warenkorb'];
		$this->pq_confirm = $this->settingsArray['jtl_eap_selector_confirm'];
		$this->sprachArr = null;
	 	$oPluginSprache = gibSprachVariablen($this->pluginSettings->kPlugin);
		$cLang = strtoupper($this->smarty->get_template_vars('lang'));

		if(strlen($cLang)==0){
			$cLang = "GER";
		}
		foreach($oPluginSprache as $key)
		{
			$this->sprachArr[$key->cName] = $key->oPluginSprachvariableSprache_arr[$cLang];
		}

		$this->versandArt = @$_SESSION['Versandart'];
	}

	public function __construct()
	{
		if($this->checkout_session== null) $this->checkout_session = md5(date("Ymd").rand(1000,99999)."VOP");
		$this->functions = new EAP_Functions($this->checkout_session);
	}
	public function fetchDeviceSecureOptInOut()
	{
		// pq("#panel-register-form form")->append($this->smarty->fetch($this->pluginSettings->cFrontendPfad. "tpl/deviceSecure.tpl"));
	}



	public function CheckRequiredFiles()
   {
	   		$this->smarty->assign("btn_adresschange",$this->sprachArr['jtl_eap_identcheck_addrchange']);
			$this->smarty->assign("identcheck_notice",$this->sprachArr["jtl_eap_identcheck_notice"]);

			$this->smarty->assign("tel",$this->settingsArray["jtl_eap_tel"] == 1 ? true :false);
			$this->smarty->assign("tel_text",$this->sprachArr['jtl_eap_tel_text']);
			$this->smarty->assign("abweichendeAdresse",$this->sprachArr['jtl_eap_abweichend_adresse']);
			$this->smarty->assign("geb",$this->settingsArray["jtl_eap_geb"] >0 ? true :false);
			$this->smarty->assign("geb_text",$this->sprachArr['jtl_eap_geb_text']);
			$this->smarty->assign("btn_submit",$this->sprachArr['jtl_eap_continuebutton']);
			$this->smarty->assign("btn_close",$this->sprachArr['jtl_eap_abbrucbbutton']);
			$this->smarty->assign("eap_notice",$this->sprachArr['jtl_eap_eingabe_notice']);
			$this->smarty->assign("selector_zahlung",$this->settingsArray['jtl_eap_selector_zahlung']);
			$this->smarty->assign("EAP_CUSTOM_OPTIN",$this->sprachArr['EAP_CUSTOM_OPTIN']);
			$parsed_date = $this->functions->getParsedDate($this->requestParams['Rechnungsadresse']->dGeburtstag);

			$this->smarty->assign("dob_customer",$parsed_date == "00.00.0000" ? "" : $parsed_date);
			$this->smarty->assign("tel_customer",$this->requestParams['Rechnungsadresse']->cTel);
	 		$this->smarty->assign('IMGPATH',$this->pluginSettings->cFrontendPfadURLSSL);



		//$datepicker = "<script>"

		$datepicker = "<script> $( function(){ $('input.birthday').datepicker({
      changeMonth: true,
	  yearRange: '-90:-16',
	  reverseYearRange:true,
	   maxDate: '-16y',
      changeYear: true,
	  dateFormat : 'dd.mm.yy'
    	})
	});</script>";

			pq('head')->append("<script src='".$this->pluginSettings->cFrontendPfadURLSSL."js/validation.js'></script>");
			pq('head')->append("<link type='text/css' rel='stylesheet' href='".$this->pluginSettings->cFrontendPfadURLSSL."css/jquery.fancybox.css?v=2.1.5'>");
			pq('head')->append("<link type='text/css' rel='stylesheet' href='".$this->pluginSettings->cFrontendPfadURLSSL."css/flatpickr.css'>");
			pq('head')->append("<link type='text/css' rel='stylesheet' href='".$this->pluginSettings->cFrontendPfadURLSSL . "css/gateway.css'>");
			pq('head')->append("<script src='".$this->pluginSettings->cFrontendPfadURLSSL."js/jquery.fancybox.pack.js?v=2.1.5'></script>");
			pq('head')->append("<script src='//code.jquery.com/ui/1.11.4/jquery-ui.min.js'></script>");
			if($this->settingsArray['jtl_eap_datepicker']>0)pq('head')->append($datepicker);
   }

   public function adressValidatorJS(){
       pq('head')->append("<script src='".$this->pluginSettings->cFrontendPfadURLSSL."js/adressvalidator.js'></script>");
       $this->CheckRequiredFiles();
   }
   public function generateFancyBoxContent()
   {


	 	$loadedTPL = "<div id='eap_fancyBox' style=\"width:auto;height:auto;display:none\">
								<div class='eap_h1'></div>
									".$this->smarty->fetch($this->pluginSettings->cFrontendPfad. "tpl/fancyBox.tpl").
						  "</div>";
	   //$loadedTPL = file_get_contents($this->pluginSettings->cFrontendPfad. "tpl/fancyBox.tpl");
	   pq($this->pq_zahlung)->prepend("<input type='hidden' id='eap_hidden_geb' name='eap_hidden_geb' value=''>");
	   pq($this->pq_zahlung)->prepend("<input type='hidden' id='eap_hidden_tel' name='eap_hidden_tel' value=''>");
	   pq($this->pq_zahlung)->prepend("<input type='hidden' id='eap_hidden_company' name='eap_hidden_company' value=''>");
	   pq($this->pq_zahlung)->prepend("<input type='hidden' id='eap_hidden_optin' name='eap_hidden_optin' value=''>");
	   pq($this->pq_footer)->prepend($loadedTPL);

	   return $loadedTPL;
   }

	public function getCurrentHandle($datenschutz = false)
	{

		try
		{

		$Kunde = $this->requestParams["Rechnungsadresse"];
		$checkhash = md5($Kunde->cFirma.$Kunde->cVorname.$Kunde->cNachname.$Kunde->cStrasse.$Kunde->cHausnummer.$Kunde->cPLZ.$Kunde->cOrt.$Kunde->cMail);
		}catch(Exception $e)
		{
		}
		return $checkhash;
	}


	public function getCurrentRequestParams()
	{
			$this->requestParams["Rechnungsadresse"] = $this->smarty->get_template_vars("Kunde");
			$this->requestParams["Lieferadresse"]	 = $this->smarty->get_template_vars("Lieferadresse");
			$this->requestParams["Warenkorb"] 		 = 0;
			$this->requestParams['art'] = strlen($this->requestParams["Rechnungsadresse"]->cFirma)>3 ? "B2B" : "B2C";
			$this->requestParams["check_firma"] = false;


			if($this->requestParams['art']=="B2B" && $this->settingsArray['jtl_eap_shopart'] == 0)
			{
				$checkFirma = strtoupper($this->requestParams["Rechnungsadresse"]->cFirma);
				// FIX ADRESSEINGABE...
				if(preg_match("/HERR/",$checkFirma))
				$this->requestParams['art']  = "B2C";
				else if(preg_match("/FRAU/",$checkFirma))
				$this->requestParams['art']  = "B2C";
				else if(preg_match("/KEINE/",$checkFirma))
				$this->requestParams['art']  = "B2C";
				else if(preg_match("/GBR/",substr($checkFirma,-5)))
				$this->requestParams['art'] = "B2C";
				else if(preg_match("/ EK/",substr($checkFirma,-3)))
				$this->requestParams['art'] = "B2C";
				else if(preg_match("/ EK./",substr($checkFirma,-4)))
				$this->requestParams['art'] = "B2C";
				else if(preg_match("/ E.K/",substr($checkFirma,-4)))
				$this->requestParams['art'] = "B2C";
				else if(preg_match("/ E.K./",substr($checkFirma,-5)))
				$this->requestParams['art'] = "B2C";
				else if(preg_match("/FIRMA/",$checkFirma))
				$this->requestParams['art'] = "B2B";
				else if(preg_match("/GMBH/",$checkFirma))
				$this->requestParams['art'] = "B2B";
				else if(preg_match("/HAFTUNGSBESCHR/",$checkFirma))
				$this->requestParams['art'] = "B2B";
				else if(preg_match("/ KG/",substr($checkFirma,-5)))
				$this->requestParams['art'] = "B2B";
				else if(preg_match("/ AG/",substr($checkFirma,-5)))
				$this->requestParams['art'] = "B2B";
				else if(preg_match("/ KG/",substr($checkFirma,-5)))
				$this->requestParams['art'] = "B2B";
				else if($this->settingsArray['jtl_eap_shopart'] == 0 ) {
				$this->requestParams["check_firma"] = true;
			}


			if(strlen(@$this->requestParams['B2BArt'])>0 && $this->requestParams["check_firma"])
				{
					$this->smarty->assign('COMPANY_DROPDOWN_PRESELECT',$this->requestParams['B2BArt']);
					if( $this->requestParams['B2BArt'] == "Einzelunternehmen" || $this->requestParams['B2BArt']  == "Privatkunde" || $this->requestParams['B2BArt']  == "GBR" || $this->requestParams['B2BArt']  == "EK"){
						$this->requestParams['art']="B2C";
					}
					else $this->requestParams['art'] = "B2B";

				}

			}//ENDIF $this->requestParams['art']=="B2B" && $this->settingsArray['jtl_eap_shopart'] == 0


			// SETTING SHOPART ÜBERSCHREIBT ALLES AUF B2B oder B2C
			if($this->settingsArray['jtl_eap_shopart'] == 1 ) $this->requestParams['art'] = "B2C";
			if($this->settingsArray['jtl_eap_shopart'] == 2 ) $this->requestParams['art'] = "B2B" ;
			$this->smarty->assign('check_firma',$this->requestParams["check_firma"]);
			if($this->requestParams['check_firma']){
			$this->smarty->assign("EAP_COMPANY_NOTICE",str_replace('{$Kunde->cFirma}',$this->requestParams["Rechnungsadresse"]->cFirma,$this->sprachArr["EAP_COMPANY_NOTICE"]));
			}


				for($i=0;$i<count($_SESSION['Warenkorb']->PositionenArr);$i++)
			{
				if($_SESSION['Warenkorb']->PositionenArr[$i]->nPosTyp==1)
				{

				// REINER WARENWERT OHNE VERSAND ABZÜGE ETC
				$position = $_SESSION['Warenkorb']->PositionenArr[$i]->nAnzahl*$_SESSION['Warenkorb']->PositionenArr[$i]->fPreis;
				$mwst = (($position*$_SESSION['Warenkorb']->PositionenArr[$i]->Artikel->fMwSt)/100);
				$this->requestParams["Warenkorb"] = $this->requestParams["Warenkorb"] + ($position+$mwst);
				}
			}

        if(isset($_SESSION['Zahlungsart']))
        {
            $get_payment =	$this->functions->dbQuery("SELECT cName FROM tzahlungsart where kZahlungsart = " . (int)$_SESSION['Zahlungsart']->kZahlungsart,1);
            $this->requestParams["Zahlungsart"]->cName = $get_payment->cName;
            $this->requestParams["Zahlungsart"]->kZahlungsart =  $_SESSION['Zahlungsart']->kZahlungsart;
        }

			if($this->current_card > $this->requestParams["Warenkorb"]){
			    $this->card_changed++;
			    if($this->settingsArray["jtl_eap_cardprotection"] > 0 && $this->card_changed+1 == ($this->settingsArray["jtl_eap_cardprotection"]+2) )
                {
                    //die("CARDPROTECTION");
                    $logEntry = new stdClass();
                    $logEntry->cArt = (utf8_decode("Warenkorbänderungen"));
                    $logEntry->sessToken = (md5($this->checkout_session.session_id()));
                    $logEntry->tstamp = (date("d.m.Y H:i:s"));
                    $logEntry->customer_vname = ($this->requestParams["Rechnungsadresse"]->cVorname);
                    $logEntry->customer_nname = ($this->requestParams["Rechnungsadresse"]->cNachname);
                    $logEntry->customer_firma = ($this->requestParams["Rechnungsadresse"]->cFirma);
                    $logEntry->warenkorb = $this->requestParams["Warenkorb"];
                    $logEntry->zahlungsart =  ($this->requestParams["Zahlungsart"]->cName);
                    $logEntry->pruefung = true;
                    $logEntry->ergebnis = true;
                    $logEntry->error = "";
                    $logEntry->responseCode = 0;
                    $logEntry->responseText = "";
                    $logEntry->scoreInfo = "";
                    $this->functions->schreibeEAPLog($logEntry);
                }
            }
			$this->current_card = $this->requestParams['Warenkorb'];
				$this->requestParams["personAbweichend"] = false;
				$this->requestParams["adresseAbweichend"] = false;

			if($this->requestParams["Rechnungsadresse"]->cVorname != $this->requestParams["Lieferadresse"]->cVorname || $this->requestParams["Rechnungsadresse"]->cNachname != $this->requestParams["Lieferadresse"]->cNachname)
			{
				$this->requestParams["personAbweichend"] = true;
				$this->requestParams["adresseAbweichend"] = true;
			}
			else
			{
				if($this->requestParams["Rechnungsadresse"]->cStrasse != $this->requestParams["Lieferadresse"]->cStrasse || $this->requestParams["Rechnungsadresse"]->cPLZ != $this->requestParams["Lieferadresse"]->cPLZ || $this->requestParams["Rechnungsadresse"]->cOrt != $this->requestParams["Lieferadresse"]->cOrt || $this->requestParams["Rechnungsadresse"]->cHausnummer != $this->requestParams["Lieferadresse"]->cHausnummer)
				{
				$this->requestParams["adresseAbweichend"] = true;
				}
			}

		$this->requestParams["Warenkorb"] = number_format($this->requestParams["Warenkorb"],0,"","");
		$this->requestParams["currenthandle"] = $this->getCurrentHandle(false);


	}


	public function identCheckKundeAddressChange()
	{
		if($this->settingsArray['jtl_eap_ident_recheck'] == 0) return false;
	    if($this->requireFullIDCard())
		{
			// IDENTCHECK DARF NUR IDCARD 5 SEIN
		 $query = "SELECT handle FROM xplugin_jtl_eap_identcheck_log  where type = 2 and handle = '".$this->functions->dbEscape($this->requestParams["currenthandle"])."' and kKunde = " . (int)$this->requestParams["Rechnungsadresse"]->kKunde;
		}
		else
		{
			// ALTERSCHECK IST IDCARD EGAL
		 $query = "SELECT handle FROM xplugin_jtl_eap_identcheck_log  where type < 3 and handle = '".$this->functions->dbEscape($this->requestParams["currenthandle"])."' and kKunde = " . (int)$this->requestParams["Rechnungsadresse"]->kKunde;
		}
		$val = $this->functions->dbQuery($query,1);
		if(@$val->handle != @$this->requestParams["currenthandle"])
		return true;
		else return false;
	}
	/** @deprecated  */
	public function requireFullIDCard()
	{
	    return false ;
	}

	public function identCheckAlwaysOrAttribute()
	{
			if($this->settingsArray['jtl_eap_identcheck_use_art']==0) return true;
		else
		{

			foreach($_SESSION['Warenkorb']->PositionenArr as $warenkorbpos)
			{
				foreach($warenkorbpos->Artikel->FunktionsAttribute as $key => $value)
				{
					if(strtoupper($key)  == strtoupper($this->settingsArray['jtl_eap_attributname']))
					{
						return true;
					}
				}
			}
		}
		return false;
	}
	public function fetchIdentCheckTemplate($altersueberpruefung = true)
	{
		//die("GEHT NED ?");
		$paymentWallContent = "<form method=\"post\" id=\"eap_identcheck\"><input type=\"hidden\" name=\"cmd\" id=\"eap_cmd\" value=\"requestIdentCheck\">".
								"<input type=\"hidden\" name=\"eap_hidden_geb\"><input type=\"hidden\" name=\"eap_hidden_tel\">";

		if($altersueberpruefung) $paymentWallContent.= $this->smarty->fetch($this->pluginSettings->cFrontendPfad. "tpl/identcheck.tpl")."</form>";

		//* bis shop 405
		pq($this->pq_confirm)->before($paymentWallContent);
		pq($this->pq_confirm)->remove();
	 	$this->renamePaymentStepIdentCheck();
	}
	public function renamePaymentStepIdentCheck()
	{

		$renameText ="Alters-/Identit&auml;tspr&uuml;fung";
		pq('#content .text-center')->html($renameText);
		pq('.stepwizard .step6 div')->html($renameText);
		pq('.clearall .step6 li')->html($renameText);
	}
	public function RemovePaymentWallSetIdentCheckWall()
	{
		$removeOutput = false;
		$setting = $this->settingsArray['jtl_eap_identcheck_use'];


		if($this->requestParams['art']=="B2B" ) return;

		$adresschanged = $this->identCheckKundeAddressChange();
		if($this->requestParams["Rechnungsadresse"]->kKundengruppe>0 && $this->functions->checkkundenGruppeIdent($this->requestParams["Rechnungsadresse"]->kKundengruppe) && !$adresschanged)
		{
		 // AUSGESCHLOSSENE KUNDENGRUPPE ABER NUR WENN EIN EINTRAG ZU DER GEPRÜFTEN ADRESSE EXISTIERT
		 return  ;
		}

		if($this->schufaIdent->requested && $this->schufaIdent->verified)
		{
			//die("SCHUFA VERIFIED OK");
			return;
		}

		if($this->schufaIdent->requested != true )
		{

			$this->fetchIdentCheckTemplate();
			return;
		}


		if($setting==1 && $this->schufaIdent->requested == true && $this->schufaIdent->verified !== true)
		{
			$this->smarty->assign('IDENT_FAILED',true);
			// NUR SCHUFA FAILED
			$this->smarty->assign('QBIT_FAILED',true);
			$this->smarty->assign('identcheck_failed_msg',$this->sprachArr['jtl_eap_identcheck_failed_msg']);
			$this->smarty->assign('identcheck_failed_headline' , $this->sprachArr['jtl_eap_identcheck_failed_headline']);
			$this->smarty->assign('identcheck_qbit_output',$this->settingsArray['identcheck_qbit_output']  == 1 ? true:false);
			$this->smarty->assign('identcheck_qbit_dataerror_msg' , $this->sprachArr['identcheck_qbit_dataerror_msg']);
			$this->smarty->assign('identcheck_qbit_dataerror' ,  str_replace(")","%)",$this->schufaIdent->responseData->dataerror));
			$this->fetchIdentCheckTemplate();
			return;
		}

	return;
	}

	public function AutoSubmitPaymentform($selected_payment)
	{

		$payment = $this->functions->dbQuery("SELECT cModulId FROM tzahlungsart where kZahlungsart = " . (int)$selected_payment->Zahlungsart,1);
		pq('#' . $payment->cModulId. ' input[type=radio]')->attr("checked","checked");
		$_SESSION['eap_selected_payment'] = null;
	    pq($this->footer)->prepend("<script>$('".$this->pq_zahlung."').submit();</script>");
	}

	public function setNoticeAgeCheck($warenkorb)
	{

		$this->smarty->assign('agecheck_warenkorb_msg',$this->sprachArr['agecheck_warenkorb_msg']);
		$this->smarty->assign('alertmsg_shipping',$this->sprachArr['alertmsg_shipping']);
		$this->smarty->assign('alert_warenkorb',$warenkorb);

		if($warenkorb)
		pq($this->pq_warenkorb)->prepend($this->smarty->fetch($this->pluginSettings->cFrontendPfad. "tpl/alertAgecheck.tpl"));
		else
		{
		pq($this->pq_alert)->prepend($this->smarty->fetch($this->pluginSettings->cFrontendPfad. "tpl/alertAgecheck.tpl"));
		}


	}

	public function disableShippingMethods()
	{

		$lockedshipping = $this->functions->lockedShipping();
		foreach($lockedshipping as $shipping)
		{
			pq('#shipment_' . $shipping->kVersandart . " label ")->prepend(' ** ');
			pq('#shipment_' . $shipping->kVersandart . "")->addClass('deaktiviert');
			pq('#shipment_' . $shipping->kVersandart . " input[type=radio]")->remove();
		}
		return count($lockedshipping);

	}
	public function disablePaymentMethods()
	{

		$lockedPayments = $this->functions->lockedPaymentsArray();
		$this->smarty->assign('alertcolor',"red");


		if($this->settingsArray['jtl_eap_payment_disableremove']=="disable")
		{
			$this->smarty->assign('alertmsg',$this->sprachArr['jtl_eap_pruefung_vor_zahlung']);
			foreach($lockedPayments as $payment)
			{
				pq('#' . $payment->cModulId . " label ")->prepend(' ** ');
				pq('#' . $payment->cModulId . "")->addClass('deaktiviert');
				pq('#' . $payment->cModulId . " input[type=radio]")->remove();
			}
		}else if($this->settingsArray['jtl_eap_payment_disableremove']=="remove")
		{
			$this->smarty->assign('alertmsg',$this->sprachArr['jtl_eap_pruefung_removed_payment']);
			foreach($lockedPayments as $payment)
			{
				pq('#' . $payment->cModulId . "")->remove();
			}
		}
		pq($this->pq_alert)->{$this->settingsArray['jtl_eap_hinweis_pos']}($this->smarty->fetch($this->pluginSettings->cFrontendPfad. "tpl/alert.tpl"));
		$_SESSION['eap_selected_payment'] = null;
	}

	public function setFancyBoxIdentCheckFailed()
	{

		$this->smarty->assign("use_postident",true);
		$loadedTPL = "<div id='eap_fancyBoxIdentFailed' style=\"width:auto;height:auto;display:none\"><a id='fancyTrigger'>
									".utf8_decode($this->smarty->fetch($this->pluginSettings->cFrontendPfad. "tpl/fancyBoxIdentFailed.tpl")).
						  "</div>";

		pq($this->pq_footer)->prepend($loadedTPL);



 		$execute = "<script>$(document).ready(function() { 
		
		$.fancybox({
					'scrolling'     : 'no',
					'overlayOpacity': 0.9,
					'showCloseButton'   : true,
					'href' : '#eap_fancyBoxIdentFailed'            
				});
		
		});</script>";
		pq($this->pq_footer)->prepend($execute);
	}

	public function redirectToAdresse()
   {
	   	header( 'Location: ' . URL_SHOP . '/bestellvorgang.php?editRechnungsadresse=1' );
		exit();
   }
   	public function redirectToshipping()
   {
	   	header( 'Location: ' . URL_SHOP . '/bestellvorgang.php?editVersandart=1' );
		exit();
   }
	   public function redirectToPaymentWall()
   {
	   	header( 'Location: ' . URL_SHOP . '/bestellvorgang.php?editZahlungsart=1' );
		exit();
   }

      public function setIdentCheckWall()
   {
	   $this->RemovePaymentWallSetIdentCheckWall();
   }

	 public function setPaymentWallFancyBox($type,$currentHandle)
   {


        if($this->settingsArray["jtl_eap_cardprotection"] > 0 && $this->card_changed+1 >= ($this->settingsArray["jtl_eap_cardprotection"]+2))
            {
                $this->disablePaymentMethods();
                return;
            }

			if($this->requestParams["Rechnungsadresse"]->kKundengruppe>0)
			{
				$kundengruppenregeln =$this->functions->leseKundengruppen($this->requestParams["Rechnungsadresse"]->kKundengruppe);
				$kundengruppenregeln = $kundengruppenregeln[0];
			}
			else
			{
				// NEUKUNDEN IMMER PRÜFEN
				$kundengruppenregeln->nBoni = 0;
				$kundengruppenregeln->nIdent = 0;
			}


	   		if($type->LOG_NAME == "Bonitätsprüfung" && $kundengruppenregeln->nBoni == 1){
				return;
			}


			$this->generateFancyBoxContent();

	   		$lockedPayments = $this->functions->lockedPaymentsArray();

			if($this->requestParams["Rechnungsadresse"]->cLand != "DE" && $this->settingsArray['jtl_eap_ausland']== 1 )
			{
				 $this->disablePaymentMethods() ;
				 return;
			}

			if($this->requestParams["adresseAbweichend"] && $this->settingsArray['jtl_eap_abweichend'])
			{
				$this->smarty->assign("abweichend",true);
				$this->smarty->assign("abweichend_msg","Abweichende Lieferadresse gesperrt");
				$this->disablePaymentMethods();
				return;
			}





		   	if($type->responseData== null || ($currentHandle != $type->handle && $type->RetryIfHandleChanged) || $type->warenkorb != $this->requestParams["Warenkorb"] && $type->RetryIfHandleChanged)
		   { // NOCH KEIN ERGEBNIS , ODER  UNGLEICHER HANDLE --> Template bei aktivierten Zahlarten laden
		  // 		$this->responseData = null;


		   	 $type->requested = null;
			 $type->responseData = null;
			 $type->handle = null;
			 $type->warenkorb = null;
			  foreach($lockedPayments as $payment)
			  {

				$htmlcontent = "blank";
				$datepicker = "";
				$optIn = $payment->nOptIn == 1 ? "$('.eap_optin').show();$('.eap_optin input').attr('disabled',false);" : "$('.eap_optin').hide();$('.eap_optin input').attr('disabled',true);";
				//$optIn[0] = "$('#gateway-opt-in').hide();$('#gateway-opt-in input').attr('required',false);";
				//$optIn[1] = "$('#gateway-opt-in').show();$('#gateway-opt-in input').attr('required',true);";
			$string = "<script>
			$('#$payment->cModulId').click(function () {
				    $( 'div.eap_h1').text('Zahlung per ".$payment->cName."');
					$('#eap_hidden_optin').val('');
					".$optIn." 
				$.fancybox({
					'scrolling'     : 'no',
					'overlayOpacity': 0.9,
					'showCloseButton'   : true,
					'href' : '#eap_fancyBox'
				});
			});
			</script>";



				if( ($this->requestParams['art']=="B2C"  && ($this->settingsArray['jtl_eap_tel'] >0 || $this->settingsArray['jtl_eap_geb']>0)) || $this->requestParams["check_firma"])
				{
					pq('#' . $payment->cModulId . " ")->append($string);
				}


			  }
		   }
		   	else if($type->responseData->secure_payment)
		   {
			   $this->disablePaymentMethods(false);
		   }
	   }


}
