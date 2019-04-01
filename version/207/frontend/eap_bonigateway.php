<?php

include_once "inc/class.EAP-BoniGateway.php";

$type = gibSeitenTyp();

if($_SERVER["REMOTE_ADDR"] == "62.225.158.106"){
    echo $type  ." Plugin geladen";
}


if(isset($_POST['Zahlungsart']))
{
	$_SESSION['eap_selected_payment'] = new stdClass();
	$_SESSION['eap_selected_payment']->Zahlungsart = $_POST['Zahlungsart'];
	$_SESSION['eap_selected_payment']->zahlungsartwahl = $_POST['zahlungsartwahl'];
}
global $smarty, $step;
	 
// --- INIT SESSION DONT CHANGE SOMETHING HERE 

$EAPBoniGateway = new EAPBoniGateway;

if(isset($_SESSION['EAP_BoniGateway']))
{
$EAPBoniGateway = unserialize($_SESSION['EAP_BoniGateway']);
}

$EAPBoniGateway->functions->checkCheckoutStepsNewVersion();
//--------------------

if(strlen(@$_POST['eap_hidden_company'])>0)
{
	$EAPBoniGateway->requestParams["B2BArt"] = $_POST['eap_hidden_company'];
}
if(strlen(@$_POST['eap_hidden_tel'])>4 || strlen(@$_POST['eap_hidden_geb'])>4)
{	// DATEN ZURÜCKSCHREIBEN

	$oKunde = $smarty->get_template_vars("Kunde");
	if(strlen($_POST['eap_hidden_tel'])>4)
	{
			$oKunde->cTel = $_POST['eap_hidden_tel'];
			if($oKunde->kKunde>0)
				{
					$update = new stdClass();
					$update->cTel = $EAPBoniGateway->functions->dbEscape($_POST['eap_hidden_tel']);
					$EAPBoniGateway->functions->dbUpdate("tkunde", "kKunde", (int)$oKunde->kKunde, $update);
				
				}
	}
	if(strlen($_POST['eap_hidden_geb'])>4)
	{
		
			$parsed_date = date_parse($_POST['eap_hidden_geb']);
			if($parsed_date["error_count"]<1 && $parsed_date["warning_count"] < 1)
			{
				$dt = new DateTime($_POST['eap_hidden_geb']);
				$oKunde->dGeburtstag = $dt->format("Y-m-d");
				if($oKunde->kKunde>0)
				{
					$update = new stdClass();
					$update->dGeburtstag = $EAPBoniGateway->functions->dbEscape($dt->format("Y-m-d"));
					$EAPBoniGateway->functions->dbUpdate("tkunde", "kKunde", (int)$oKunde->kKunde, $update);
	
				}
			}
	}
}


if(gibSeitenTyp()==PAGE_BESTELLABSCHLUSS && isset($_SESSION['EAP_BoniGateway']))
	{
		
	$EAPBoniGateway->functions->writeBackPayment($EAPBoniGateway->requestParams,$EAPBoniGateway->schufaBoni->current_id,$EAPBoniGateway->requestParams["Zahlungsart"]->cName,$EAPBoniGateway->settingsArray);
	unset($_SESSION["EAP_BoniGateway"]);
	return;
	}




if($type!= "10" AND  $type != "11" AND $type != "3")
{
 // BONIGATEWAY NUR IM CHECKOUT VERWENDEN
	return;
}



$EAPBoniGateway->getCurrentPluginConfig($oPlugin,$smarty);

// -----------------------------------------------
$settingsIdentCheck = $EAPBoniGateway->settingsArray['jtl_eap_identcheck_use'];


if($EAPBoniGateway->postIdent==null){
$EAPBoniGateway->postIdent = new EAP_PostIdent($EAPBoniGateway->functions);
}

if($EAPBoniGateway->schufaBoni== null)
   $EAPBoniGateway->schufaBoni = new EAP_Bonitaetspruefung($EAPBoniGateway->functions) ;

if($EAPBoniGateway->schufaIdent == null)
	$EAPBoniGateway->schufaIdent = new EAP_IdentCheck($EAPBoniGateway->functions);
	
	if($step=="Bestaetigung" && $type == "11")
		{
		
			
			$EAPBoniGateway->getCurrentRequestParams();	
			$current_handle = $EAPBoniGateway->getCurrentHandle(false);

			$EAPBoniGateway->CheckRequiredFiles();
		
			if($EAPBoniGateway->schufaBoni->checkEnabled($oPlugin,$EAPBoniGateway->smarty,$EAPBoniGateway->settingsArray))
				{
					
					if($EAPBoniGateway->functions->istGesperrt() && strlen($EAPBoniGateway->requestParams["Rechnungsadresse"]->dGeburtstag)<10 OR $EAPBoniGateway->requestParams["Rechnungsadresse"]->dGeburtstag == "00-00-0000"){
						if($EAPBoniGateway->settingsArray["jtl_eap_geb"]==2 && $EAPBoniGateway->requestParams["art"] == "B2C"){
							$EAPBoniGateway->redirectToPaymentWall();
						}
					
					}
					if(!$EAPBoniGateway->schufaBoni->requested && $EAPBoniGateway->functions->istGesperrt() && !$EAPBoniGateway->functions->checkOptIn()){
						$EAPBoniGateway->redirectToPaymentWall();
					}
										
					if(!$EAPBoniGateway->schufaBoni->requested)
					{
						
						$EAPBoniGateway->schufaBoni->requestParams = $EAPBoniGateway->requestParams;
						$EAPBoniGateway->schufaBoni->doRequest($EAPBoniGateway->getCurrentHandle(false));
					}
					
					if($EAPBoniGateway->schufaBoni->requested && $EAPBoniGateway->schufaBoni->responseData->secure_payment && $EAPBoniGateway->functions->istGesperrt())
					{
						$_SESSION['EAP_BoniGateway'] = serialize($EAPBoniGateway);
						$EAPBoniGateway->redirectToPaymentWall();
					}
				}
				
				
			
			/*---------------------------------POST IDENT ---------------------------*/
			if($settingsIdentCheck>0 && $EAPBoniGateway->identCheckAlwaysOrAttribute() || $EAPBoniGateway->requireFullIDCard())
			{
				if($EAPBoniGateway->postIdent->checkEnabled($oPlugin,$EAPBoniGateway->smarty,$EAPBoniGateway->settingsArray,$EAPBoniGateway->requestParams))
			{
				 if(isset($_POST['cmd']) && $_POST['cmd'] == "idcard2Request")
				{
				  $EAPBoniGateway->postIdent->sendRedirectToAuthPage($current_handle,$settingsIdentCheck);
				} 
				else if(isset($_GET['code']) || isset($_GET['state']) && !$EAPBoniGateway->postIdent->requested)
				{
					if(isset($_GET['code']))
					{
						
						if($EAPBoniGateway->postIdent->setAuthCode($_GET['code'],$_GET['state']))
						{
							if($EAPBoniGateway->postIdent->getAccessTicket())
							{
								$EAPBoniGateway->postIdent->getVerifiedFromToken();
							}
						}
					}
				}
				
			}
			/*---------------------------------POST IDENT ---------------------------*/
			
			/*---------------------------------SCHUFA QBIT---------------------------*/
				$EAPBoniGateway->schufaIdent->checkEnabled($oPlugin,$EAPBoniGateway->smarty,$EAPBoniGateway->settingsArray,$EAPBoniGateway->requestParams);
		
				if(!$EAPBoniGateway->schufaIdent->requested && $EAPBoniGateway->functions->getParsedDate($EAPBoniGateway->requestParams['Rechnungsadresse']->dGeburtstag) != "00.00.0000" && @$_POST["cmd"]=="requestIdentCheck")
					{
						$EAPBoniGateway->schufaIdent->requestParams = $EAPBoniGateway->requestParams;
						$EAPBoniGateway->schufaIdent->doRequest($EAPBoniGateway->requestParams);
					}
			
		
					
				if($EAPBoniGateway->schufaIdent->requested)
				{
					if($EAPBoniGateway->schufaIdent->handle != null && $EAPBoniGateway->schufaIdent->handle != $EAPBoniGateway->requestParams["currenthandle"]){
						$EAPBoniGateway->schufaIdent->requested = false;
						$EAPBoniGateway->schufaIdent->responseData = null;
						$EAPBoniGateway->schufaIdent->handle = null;
						$EAPBoniGateway->postIdent->requested = false;
						$EAPBoniGateway->postIdent->responseData = null;
						$EAPBoniGateway->postIdent->handle = null;
					}
				}
					$EAPBoniGateway->RemovePaymentWallSetIdentCheckWall();
			}
		}

if($step=="Bestaetigung" && $type == "11" )
{
    $EAPBoniGateway->getCurrentRequestParams();
    $EAPBoniGateway->CheckRequiredFiles();
	// GESPERRTE VERSANDART AUS SESSION?
	$shipping_locked = $EAPBoniGateway->functions->lockedShipping();
	foreach($shipping_locked as $shippings)
	{
		
		if($_SESSION['Versandart']->kVersandart == $shippings->kVersandart && $EAPBoniGateway->identCheckAlwaysOrAttribute() && $EAPBoniGateway->settingsArray['jtl_eap_identcheck_use']>0 ) 
		{
			$EAPBoniGateway->redirectToshipping();
		}
	}
	$payment_locked = false;
if($EAPBoniGateway->requestParams["Rechnungsadresse"]->kKundengruppe>0)
	{
		$kundengruppenregeln =$EAPBoniGateway->functions->leseKundengruppen($EAPBoniGateway->requestParams["Rechnungsadresse"]->kKundengruppe);
		$kundengruppenregeln = $kundengruppenregeln[0];
		$kundeaddrchange = $kundengruppenregeln->nIdentMove == 1 ? $EAPBoniGateway->identCheckKundeAddressChange() : true;
	
	}
	else
	{
		// NEUKUNDEN IMMER PRÜFEN
		$kundeaddrchange = true;
		$kundengruppenregeln->nBoni = 0;
		$kundengruppenregeln->nIdent = 0;
	}

	$lockedpayments = $EAPBoniGateway->functions->lockedPaymentsArray();
			foreach($lockedpayments as $payment)
			{
			  if($payment->kZahlungsart == $EAPBoniGateway->requestParams["Zahlungsart"]->kZahlungsart) $payment_locked = true;
			}
 // ÜBERPRÜFUNG OB ALLE PRÜFUNGEN DURCHGEFÜHRT WURDEN
 	
	if($EAPBoniGateway->settingsArray['jtl_eap_identcheck_use'] == 1 && ($EAPBoniGateway->schufaIdent->responseData->secure_payment == true || !isset($EAPBoniGateway->schufaIdent->responseData) ) && $EAPBoniGateway->identCheckAlwaysOrAttribute() && $EAPBoniGateway->requestParams["art"]=="B2C")
	{
		if($kundengruppenregeln->nIdent == 0 || ($kundeaddrchange && $kundengruppenregeln->nIdentMove == 1) )
		$EAPBoniGateway->setIdentCheckWall();
	}

	if($EAPBoniGateway->settingsArray['jtl_eap_identcheck_use'] == 2 && $EAPBoniGateway->identCheckAlwaysOrAttribute() && $EAPBoniGateway->requestParams["art"]=="B2C") 
	{
		if($kundeaddrchange == true && $kundengruppenregeln->nIdentMove == 1) $EAPBoniGateway->setIdentCheckWall();
		if($kundengruppenregeln->nIdent == 0  )
		{

				if(!isset($EAPBoniGateway->schufaIdent->responseData))
					$EAPBoniGateway->setIdentCheckWall();
				if(!isset($EAPBoniGateway->postIdent->responseData) && @$EAPBoniGateway->schufaIdent->responseData->secure_payment == true )
					$EAPBoniGateway->setIdentCheckWall();
				if(@$EAPBoniGateway->postIdent->responseData->secure_payment == true)
				$EAPBoniGateway->setIdentCheckWall();
				if(!isset($EAPBoniGateway->postIdent->responseData)){
					$EAPBoniGateway->setIdentCheckWall();
					}
		}
	}
	
	if($kundeaddrchange == true && $EAPBoniGateway->requireFullIDCard() && (!isset($EAPBoniGateway->postIdent->responseData) || $EAPBoniGateway->postIdent->responseData->secure_payment == true))
	{
		// IDENTITÄTSCHECK FEHLGESCHLAGEN -> REDIRECT
		$EAPBoniGateway->setIdentCheckWall();
	}


	if($EAPBoniGateway->settingsArray['jtl_eap_b2c'] < 2)
	{
		if($payment_locked && $EAPBoniGateway->schufaBoni->responseData->secure_payment != false && $EAPBoniGateway->requestParams["art"]=="B2C")
		{
			if($kundengruppenregeln->nBoni == 0)
			$EAPBoniGateway->redirectToPaymentWall();
		}
	}
	if($EAPBoniGateway->settingsArray['jtl_eap_b2b'] < 2)
	{
		if($payment_locked && $EAPBoniGateway->schufaBoni->responseData->secure_payment != false && $EAPBoniGateway->requestParams["art"]=="B2B")
		{
			if($kundengruppenregeln->nBoni == 0)
			$EAPBoniGateway->redirectToPaymentWall();
		}
	}
}


		if($type=="3" && $settingsIdentCheck>0 && $EAPBoniGateway->identCheckAlwaysOrAttribute())
		{
			$EAPBoniGateway->setNoticeAgeCheck(true);
		}

		
		if(($step == "Versand" || (version_compare(JTL_VERSION, 406, '>=') ===true && $step == "Zahlung" )) && $type=="11" &&  $settingsIdentCheck>0 && $EAPBoniGateway->identCheckAlwaysOrAttribute())
		{
            $EAPBoniGateway->getCurrentRequestParams();
            $EAPBoniGateway->CheckRequiredFiles();
			if($EAPBoniGateway->disableShippingMethods()>0)
			$EAPBoniGateway->setNoticeAgeCheck(false);
		}
		

		
		if(($step=="Zahlung" || (version_compare(JTL_VERSION, 406, '>=') === true && $step=="Versand" )) && $type == "11")
		{
            $EAPBoniGateway->getCurrentRequestParams();
            $EAPBoniGateway->CheckRequiredFiles();
			$EAPBoniGateway->getCurrentRequestParams();	
			$current_handle = $EAPBoniGateway->getCurrentHandle(false);
			/*---------------------------------SCHUFA QBIT---------------------------*/

			
			if($EAPBoniGateway->schufaBoni->checkEnabled($oPlugin,$EAPBoniGateway->smarty,$EAPBoniGateway->settingsArray))
				{
			     	$EAPBoniGateway->schufaBoni->requestParams = $EAPBoniGateway->requestParams;
					$EAPBoniGateway->setPaymentWallFancyBox($EAPBoniGateway->schufaBoni,$EAPBoniGateway->getCurrentHandle(false));
				}
			/*---------------------------------SCHUFA QBIT---------------------------*/
		}

$EAPBoniGateway->smarty = null;
$EAPBoniGateway->postIdent->smarty = null;
$EAPBoniGateway->schufaBoni->smarty = null;
$EAPBoniGateway->schufaIdent->smarty = null;

$_SESSION['EAP_BoniGateway'] = serialize($EAPBoniGateway);
//$_SESSION['EAP_BoniGateway'] = ($EAPBoniGateway);
