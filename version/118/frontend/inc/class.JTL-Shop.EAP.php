<?php

	class EAPPruefung extends EAP_Functions
	{
	}
    class EAP_Functions
    {
    	/** URL zur EAP-Schnittstelle **/
        private $url;
		public $checkout_session;
		public $requestID;
		public $global_session;
		
			public function __construct($checkout_session)
		{
			$this->checkout_session = $checkout_session;
		}
		public function dbEscape($value)
		{
			return version_compare(JTL_VERSION, 400, '>=') == false ? $GLOBALS["DB"]->escape($value) : Shop::DB()->escape($value);
		}
		public function dbQuery($query,$type)
		{ 
			return version_compare(JTL_VERSION, 400, '>=') == false ? $GLOBALS["DB"]->executeQuery($query,$type) : Shop::DB()->query($query,$type);
		}
		public function dbUpdate($table,$pkcolumn,$pkvalue,$values)
		{ 
			return version_compare(JTL_VERSION, 400, '>=') == false ? 	$GLOBALS["DB"]->updateRow($table, $pkcolumn, $pkvalue, $values) : Shop::DB()->update($table, $pkcolumn, $pkvalue, $values);
		}
      
	   	public function dbInsert($table,$object)
		{ 
			return version_compare(JTL_VERSION, 400, '>=') == false ? $GLOBALS["DB"]->insertRow($table,$object) : Shop::DB()->insert($table,$object);
		}
		public function createSettingsArray($input)
		{
			$return = array();
			foreach($input as $setting)
			{
				$return[$setting->cName] = $setting->cWert;
			}
			return $return;
		}
		public function lockedPaymentsArray()
		{
			 $oZahlungsartRegeln_arr = $this->dbQuery("SELECT * FROM xplugin_jtl_eap_zahlungsarten
            														JOIN tzahlungsart ON tzahlungsart.kZahlungsart=xplugin_jtl_eap_zahlungsarten.kZahlungsart",2);
			return $oZahlungsartRegeln_arr;
		}
		
		public function neueVersandRegel($del,$insert)
		{
			if($del>0)
			{
				$bOk = $this->dbQuery("DELETE FROM xplugin_jtl_eap_versandarten where kVersandart = ".(int)$del,3);
			}
			
			if($insert>0)
			{
				$oGruppeNeu = new stdClass();
				$oGruppeNeu->kVersandart = (int)$insert;
				$bOk = $this->dbInsert("xplugin_jtl_eap_versandarten", $oGruppeNeu);
			}
		}
		public function getShippingMethods()
		{
			$versandarten = $this->dbQuery("SELECT cName,tversandart.kVersandart,xplugin_jtl_eap_versandarten.kVersandart as checkValue FROM `tversandart` left outer join xplugin_jtl_eap_versandarten on xplugin_jtl_eap_versandarten.kVersandart = tversandart.kVersandart",2);
			return $versandarten;
		}
			public function lockedShipping()
		{
			 $oZahlungsartRegeln_arr = $this->dbQuery("SELECT kVersandart FROM xplugin_jtl_eap_versandarten ",2);
			
			return $oZahlungsartRegeln_arr;
		}
		
		public function getAllActivePaymentsArray($versand)
		{
			
			 $oZahlungsartRegeln_arr = $this->dbQuery("SELECT tzahlungsart.* FROM `tversandartzahlungsart` 
			 											left join tzahlungsart on tversandartzahlungsart.kzahlungsart = tzahlungsart.kzahlungsart where tversandartzahlungsart.kversandart = ".(int)$versand->kVersandart,2);
			return $oZahlungsartRegeln_arr;
		}
		
		public function checkKundengruppeBoni($kKundengruppe)
		{
			$oKundengruppe = $this->dbQuery("SELECT * FROM xplugin_jtl_eap_kundengruppen
        													WHERE nBoni = 1 and kKundengruppe=" . (int)$kKundengruppe,1);
			if ($oKundengruppe) return true;

        	return false;
		}
		
		public function checkkundenGruppeIdent($kKundengruppe)
		{
			$oKundengruppe = $this->dbQuery("SELECT * FROM xplugin_jtl_eap_kundengruppen
        													WHERE nIdent = 1 and kKundengruppe=" .(int)$kKundengruppe,1);

        	if ($oKundengruppe) return true;

        	return false;
		}
		 
		
		public function getParsedDate($inputdata)
		{
				if(strlen($inputdata)<10 OR $inputdata == "00-00-0000" OR $inputdata == "0000-00-00")
				 {
					 // KEIN GEBURTSDATUM VORHANDEN
					 return "00.00.0000";
				 }
				 else
				 {
					 try
					 {
					 $date = new DateTime($inputdata);
					 $geb = $date->format("d.m.Y");
					 }catch(Exception $e)
					 {
						 // echo $oKunde->dGeburtstag;
						 $geb="00.00.0000";
					 }
				 }
				return $geb; 
		}
		public function replaceHTML($inputtext,$replaceUnd = true)
		{
			// $inputtext = htmlentities($inputtext,ENT_NOQUOTES,"ISO-8859-1");
			// FIX JTLSHOP -> 
			if($replaceUnd)
			{	
			 $inputtext = str_replace("&amp;","",$inputtext);
			}
			$inputtext = html_entity_decode($inputtext,ENT_NOQUOTES,"windows-1252");
			// <- FIX JTL-SHOP 
			$inputtext = str_replace(chr(192),"Ae",$inputtext);
			$inputtext = str_replace(chr(193),"Ae",$inputtext);
			$inputtext = str_replace(chr(194),"Ae",$inputtext);
			$inputtext = str_replace(chr(195),"Ae",$inputtext);
			$inputtext = str_replace(chr(197),"Ae",$inputtext);
			$inputtext = str_replace(chr(198),"Ae",$inputtext);
			$inputtext = str_replace(chr(199),"C",$inputtext);
			$inputtext = str_replace(chr(200),"E",$inputtext);
			$inputtext = str_replace(chr(201),"E",$inputtext);
			$inputtext = str_replace(chr(202),"E",$inputtext);
			$inputtext = str_replace(chr(203),"E",$inputtext);
			$inputtext = str_replace(chr(204),"I",$inputtext);
			$inputtext = str_replace(chr(205),"I",$inputtext);
			$inputtext = str_replace(chr(206),"I",$inputtext);
			$inputtext = str_replace(chr(207),"I",$inputtext);
			$inputtext = str_replace(chr(208),"D",$inputtext);
			$inputtext = str_replace(chr(209),"N",$inputtext);
			$inputtext = str_replace(chr(210),"Oe",$inputtext);
			$inputtext = str_replace(chr(211),"Oe",$inputtext);
			$inputtext = str_replace(chr(212),"Oe",$inputtext);
			$inputtext = str_replace(chr(213),"Oe",$inputtext);
			$inputtext = str_replace(chr(217),"Ue",$inputtext);
			$inputtext = str_replace(chr(218),"Ue",$inputtext);
			$inputtext = str_replace(chr(219),"Ue",$inputtext);
			$inputtext = str_replace(chr(221),"Y",$inputtext);
			$inputtext = str_replace(chr(224),"ae",$inputtext);
			$inputtext = str_replace(chr(225),"ae",$inputtext);
			$inputtext = str_replace(chr(226),"ae",$inputtext);
			$inputtext = str_replace(chr(227),"ae",$inputtext);
			$inputtext = str_replace(chr(229),"ae",$inputtext);
			$inputtext = str_replace(chr(230),"ae",$inputtext);
			$inputtext = str_replace(chr(231),"c",$inputtext);
			$inputtext = str_replace(chr(232),"e",$inputtext);
			$inputtext = str_replace(chr(233),"e",$inputtext);
			$inputtext = str_replace(chr(234),"e",$inputtext);
			$inputtext = str_replace(chr(235),"e",$inputtext);
			$inputtext = str_replace(chr(236),"i",$inputtext);
			$inputtext = str_replace(chr(237),"i",$inputtext);
			$inputtext = str_replace(chr(238),"i",$inputtext);
			$inputtext = str_replace(chr(239),"i",$inputtext);
			$inputtext = str_replace(chr(240),"oe",$inputtext);
			$inputtext = str_replace(chr(241),"n",$inputtext);
			$inputtext = str_replace(chr(242),"oe",$inputtext);
			$inputtext = str_replace(chr(243),"oe",$inputtext);
			$inputtext = str_replace(chr(244),"oe",$inputtext);
			$inputtext = str_replace(chr(245),"oe",$inputtext);
			$inputtext = str_replace(chr(249),"ue",$inputtext);
			$inputtext = str_replace(chr(250),"ue",$inputtext);
			$inputtext = str_replace(chr(251),"ue",$inputtext);
			$inputtext = str_replace(chr(253),"y",$inputtext);
			$inputtext = str_replace(chr(255),"y",$inputtext);
			
			return ($inputtext);
		}
		
	
			
			function istGesperrt()
			{
				$gwpruefung = $this->dbQuery("SELECT COUNT(kZahlungsart) as executeGateway FROM xplugin_jtl_eap_zahlungsarten
                													WHERE nMaxScore = 3 AND kZahlungsart=" .(int) $_SESSION['Zahlungsart']->kZahlungsart,1);

				if($gwpruefung->executeGateway>0) return true;
				else return false;													
			}
			
			  public function setzeKundengruppen($kgruppe,$insert)
        {
            	if(!$insert)
			{
				$bOk = $this->dbQuery("DELETE FROM xplugin_jtl_eap_kundengruppen where kKundengruppe = ".(int)$kgruppe,3);
			}
			else
			{
				$oGruppeNeu = new stdClass();
				$oGruppeNeu->kKundengruppe = (int)$kgruppe;
				$bOk = $this->dbInsert("xplugin_jtl_eap_kundengruppen", $oGruppeNeu);
			}

            return 0;
        }
		
	
		public function updateRegel($art,$kundengruppe,$value)
		{
			$regel = new stdClass();
			$regel->kKundengruppe = (int)$kundengruppe;
			if($art == "boni") $regel->nBoni = (int)$value;
			else if($art=="ident") $regel->nIdent = (int)$value;
			else if($art == "move")
			{ $regel->nIdentMove = (int)$value;
				if($value == "1")
				{
					$this->dbQuery("update xplugin_jtl_eap_kundengruppen set nIdentMove = 0",1);
				}
			}
			
			$bOk = $this->dbUpdate("xplugin_jtl_eap_kundengruppen", "kKundengruppe", $kundengruppe, $regel);
			
		}
		public function createKundenGruppenEntry()
		{
			$kundengruppe = $this->leseKundengruppen();
			foreach($kundengruppe as $gruppe)
			{
				if($gruppe->checkValue==null) 
				{
					$regel = new stdClass();
					$regel->nBoni = (int)0;
					$regel->nIdent = (int)0;
	
					$regel->kKundengruppe = (int)$gruppe->kGruppe;
					$bOk = $this->dbInsert("xplugin_jtl_eap_kundengruppen", $regel);
				}
			}
		}
		
		 public function leseKundengruppen($kKundengruppe=0)
        {
            if ($kKundengruppe == 0){
				
                $oKundengruppen_arr = $this->dbQuery("SELECT xplugin_jtl_eap_kundengruppen.kKundengruppe as checkValue, tkundengruppe.cName,tkundengruppe.cName,tkundengruppe.kKundengruppe as kGruppe,IFNULL(xplugin_jtl_eap_kundengruppen.nBoni,0) as nBoni ,IFNULL(xplugin_jtl_eap_kundengruppen.nIdent,0) as nIdent,IFNULL(xplugin_jtl_eap_kundengruppen.nIdentMove,0) as nIdentMove FROM xplugin_jtl_eap_kundengruppen
                                                                    RIGHT OUTER JOIN tkundengruppe ON tkundengruppe.kKundengruppe=xplugin_jtl_eap_kundengruppen.kKundengruppe
                                                                    ORDER by tkundengruppe.kKundengruppe",2);
			}
            else
                $oKundengruppen_arr = $this->dbQuery("SELECT xplugin_jtl_eap_kundengruppen.*, tkundengruppe.cName FROM xplugin_jtl_eap_kundengruppen
                                                                    JOIN tkundengruppe ON tkundengruppe.kKundengruppe=xplugin_jtl_eap_kundengruppen.kKundengruppe
                                                                    WHERE xplugin_jtl_eap_kundengruppen.kKundengruppe=" . (int)$kKundengruppe . "
                                                                    ORDER by kKundengruppe",2);
																	
																	

            return $oKundengruppen_arr;
        }

			function clearDataRechnungsadresse($rechnungsadresse)
			{
				$inputdata = $rechnungsadresse;
				
				
				if(strlen(@$inputdata->dGeburtstag)<10 OR $inputdata->dGeburtstag == "00-00-0000")
				 {
					 $geb="00.00.0000"; // KEIN GEBURTSDATUM VORHANDEN
				 }
				 else
				 {
					 try
					 {
					 $date = new DateTime($inputdata->dGeburtstag);
					 $geb = $date->format("d.m.Y");
					 }catch(Exception $e)
					 {
						// echo $oKunde->dGeburtstag;
						 $geb="00.00.0000";
					 }
				 }
				 
			
				
				$output = array("Vorname" => $this->replaceHTML($inputdata->cVorname),
								"Nachname" => $this->replaceHTML($inputdata->cNachname),
								"Firma" =>   $this->replaceHTML($inputdata->cFirma,false),
								"PLZ" => $inputdata->cPLZ,
								"Ort" =>   $this->replaceHTML($inputdata->cOrt),
								"Strasse" =>   $this->replaceHTML(($inputdata->cStrasse)." ".$inputdata->cHausnummer),
								"nr" => $inputdata->cHausnummer,
								"land" => $inputdata->cLand,
								"geschlecht" => ($inputdata->cAnrede=="m") ? 1 : 2,
								"geb" => $geb,
								"mail" => $this->replaceHTML($inputdata->cMail),
								"ip" => $_SERVER['REMOTE_ADDR'],
								"tel" => $inputdata->cTel);
				
				foreach($output as $key => $val)
				{
				//	echo $val;
				$output[$key] = utf8_encode($output[$key]);
				}
			
				
				return $output;
				//echo mb_detect_encoding("äää");
				
			}
			function clearDataLieferadresse($lieferadresse)
			{
			
				$inputdata = $lieferadresse;
				
				$output = array("Vorname" => $this->replaceHTML($inputdata->cVorname),
								"Nachname" => $this->replaceHTML($inputdata->cNachname),
								"Firma" =>  $this->replaceHTML($inputdata->cFirma,false),
								"PLZ" => $inputdata->cPLZ,
								"Ort" =>  $this->replaceHTML($inputdata->cOrt),
								"Strasse" =>  $this->replaceHTML($inputdata->cStrasse." ".$inputdata->cHausnummer),
								"nr" => $inputdata->cHausnummer,
								"land" => $inputdata->cLand);
				
			foreach($output as $key => $value)
			{
				$output[$key] = utf8_encode($value);
			}
				return $output;
			}

        public function getZahlungsartRegel($kZahlungsart)
        {
            if ((int)$kZahlungsart)
            {
                $oZahlungsartRegel = $this->dbQuery("SELECT * FROM xplugin_jtl_eap_zahlungsarten
                													WHERE kZahlungsart=" . (int)$kZahlungsart,1);

                if ($oZahlungsartRegel)
                    return $oZahlungsartRegel;
            }

            return false;
        }

     
		function writeBackPayment($requestParams)
		{		
		
				$var = new stdClass();
				$var->abschluss = $this->dbEscape($requestParams["Zahlungsart"]->cName);	
				$this->dbUpdate("xplugin_jtl_eap_fulllog","sessToken",  $this->dbEscape(md5($this->checkout_session.session_id())), $var);
				
				/*if((int)$oPlugin->getConf('requestID')>0)
				{
				$oEAPCheck->writeBackPayment($oPlugin->getConf('requestID'),$oPlugin->getConf('payment_name'));
				
				}
				*/
		}
		
		function getResponseCode($responseData)
		{
			if(!$responseData->responseCode) return "";
			
			$codes = array( 0 => "",
							20 => "SCHUFA B2C",
						   21 => "B2C Offlinesuche",
						   17 => "Kreditlimit",
						   22 => "Blackliste",
						   80 => "Ungepr&uuml;ft",
						   30 => "SCHUFA B2B",
						   31 => "B2B Offlinesuche",
						   10000 => "<a style='color:red' href='https://support.eaponline.de/'>[Support]</a>",
						   10001 => "<a style='color:red' href='https://support.eaponline.de/'>[Support]</a>",
						   10002 => "<a style='color:red' href='https://support.eaponline.de/'>[Support]</a>",
						   10003 => "<a style='color:red' href='https://support.eaponline.de/'>[Support]</a>",
						   10004 => "<a style='color:red' href='https://support.eaponline.de/'>[Support]</a>",
						   10005 => "<a style='color:red' href='https://support.eaponline.de/'>[Support]</a>",
						   10006 => "<a style='color:red' href='https://support.eaponline.de/'>[Support]</a>");
						   
			return $codes[$responseData->responseCode];
		}
			
			public function verifiedAndMovedToKundengruppe($type,$nType = 1)
	{
		
		if($type->responseData->verified == true && $type->requestParams["Rechnungsadresse"]->kKunde>0)
		{
			$moveto = $this->dbQuery("SELECT * FROM xplugin_jtl_eap_kundengruppen
            												WHERE nIdentMove = 1",1);
			if($moveto->kKundengruppe>0)
			{
				$identchecklog = new stdClass();
				$identchecklog->kKunde = (int)$type->requestParams["Rechnungsadresse"]->kKunde;
				$identchecklog->handle = $this->dbEscape($type->requestParams["currenthandle"]);
				$identchecklog->tstamp = $this->dbEscape(date("Y-m-d H:i:s"));
				$identchecklog->type = (int)$nType;
				$this->dbInsert("xplugin_jtl_eap_identcheck_log", $identchecklog);	
				$_SESSION['Kunde']->kKundengruppe = (int)$moveto->kKundengruppe ; 
				$this->dbQuery("UPDATE tkunde SET kKundengruppe=".(int)$_SESSION['Kunde']->kKundengruppe." WHERE kKunde=".(int)$type->requestParams["Rechnungsadresse"]->kKunde."", 3);

			}
			
		}
		
	}
		public function createLog($type,$exception = null)
		{
			try
			{
			$eintrag = new stdClass();
			$eintrag->cArt = $this->dbEscape(utf8_decode($type->LOG_NAME));
			$eintrag->sessToken = $this->dbEscape(md5($this->checkout_session.session_id()));
			$eintrag->tstamp = $this->dbEscape(date("d.m.Y H:i:s"));
			$eintrag->customer_vname = $this->dbEscape($type->requestParams["Rechnungsadresse"]->cVorname);
			$eintrag->customer_nname = $this->dbEscape($type->requestParams["Rechnungsadresse"]->cNachname);
			$eintrag->customer_firma = $this->dbEscape($type->requestParams["Rechnungsadresse"]->cFirma);
			$eintrag->warenkorb = $type->requestParams["Warenkorb"];
			$eintrag->zahlungsart =  $this->dbEscape($type->requestParams["Zahlungsart"]->cName);
			$eintrag->pruefung = $this->dbEscape(@$type->requested);
			$eintrag->ergebnis = $this->dbEscape(@$type->responseData->secure_payment);
			$eintrag->error = $this->dbEscape(@$type->responseData->error);
			$eintrag->responseCode = @$type->responseData->responseCode > 0  ? (int)@$type->responseData->responseCode : 0;
			$eintrag->responseText = $this->dbEscape(@$this->getResponseCode($type->responseData));
			
			$eintrag->scoreInfo = $this->dbEscape(@$type->responseData->Scorebereich . "-".@$type->responseData->Scorewert);
			}catch(Exception $e)
			{
			$eintrag->scoreInfo = "";
			}
			if($exception!=null)
			{
			$eintrag->error = $this->dbEscape($exception);	
			}
			$this->schreibeEAPLog($eintrag);
		}
		
         function schreibeEAPLog($oLogEintrag)
        {
	        return $this->dbInsert("xplugin_jtl_eap_fulllog", $oLogEintrag);	
        }

   
        public function neueRegel($kZahlungsart, $nScorewert)
        {
         
            $oEAPRegel = $this->dbQuery("SELECT * FROM xplugin_jtl_eap_zahlungsarten
            												WHERE kZahlungsart=" .(int) $kZahlungsart,1);
			$oRegelNeu = new stdClass();
            if (!$oEAPRegel)
            {
				// Regel anlegen
			
				$oRegelNeu->nMaxScore = (int)$nScorewert;
				$oRegelNeu->kZahlungsart = (int)$kZahlungsart;
				$bOk = $this->dbInsert("xplugin_jtl_eap_zahlungsarten", $oRegelNeu);

				if ($bOk)
					return 0;
            }
            else
            {
            	// Regel ändern
				$oRegelNeu->nMaxScore = (int)$nScorewert;
				$bOk = $this->dbUpdate("xplugin_jtl_eap_zahlungsarten", "kZahlungsart", (int)$kZahlungsart, $oRegelNeu);
				if ($bOk)
					return 0;
            }

			return 5;
        }

        public function entferneRegel($kZahlungsart)
        {
          return $this->dbQuery("DELETE FROM xplugin_jtl_eap_zahlungsarten WHERE kZahlungsart=" . (int)$kZahlungsart,3);
        }

        public function zeigeEAPLog($nEintraege=30)
        {
          return $this->dbQuery("SELECT * FROM `xplugin_jtl_eap_fulllog` order by sessToken,tstamp desc LIMIT " . (int)$nEintraege,2);
        }
      
    }
