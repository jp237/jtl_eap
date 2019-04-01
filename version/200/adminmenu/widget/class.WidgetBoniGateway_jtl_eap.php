<?php

require_once PFAD_ROOT . PFAD_ADMIN . PFAD_INCLUDES . PFAD_WIDGETS . 'class.WidgetBase.php';

class WidgetBoniGateway_jtl_eap extends WidgetBase {
	
	public function dbQuery($query,$type) 		{ 		
		return version_compare(JTL_VERSION, 400, '>=') == false ? $GLOBALS["DB"]->executeQuery($query,$type) : Shop::DB()->query($query,$type); 	
		}
		public function dbEscape($value)
		{
			return version_compare(JTL_VERSION, 400, '>=') == false ? $this->dbEscape($value) : Shop::DB()->escape($value);
		}
	
	    public function init()
    {
			$gwusr = $this->oPlugin->oPluginEinstellungAssoc_arr['jtl_eap_userid'];
			$gwpwd = md5($this->oPlugin->oPluginEinstellungAssoc_arr['jtl_eap_passwort']);
			$projecteCount = 0;
			$login["color"] = "FC696C";
			$login["text"] = "Nicht einsatzbereit (Login) $gwusr";
	
			
			try
					{
					$client = new SoapClient("https://webservice.inkasso-vop.de/webservice.php?wsdl",array('cache_wsdl' => WSDL_CACHE_NONE) );
					$result = $client->getGatewayLogin($gwusr,$gwpwd);
                 	if($result->status=="True")
                    {
						 $projekte = $client->getProject($gwusr,$gwpwd,"1"); 
						 $projecteCount = count($projekte);
						 if($projecteCount<1) $login["Text"] = "Nicht einsatzbereit ( Projekte ) ";
						 else
						 {
							 $login["color"] = "BBFFBB";
							 $login["text"] = "Einsatzbereit ( $projecteCount Projekte )";
						 }
						 
					}
					}catch(Exception $e)
					{
						$login['text'] = "Nicht einsatzbereit. Kommunikationsfehler";
					}
					$this->oSmarty->assign("state_color", $login["color"]);
					$this->oSmarty->assign('state_text', $login["text"]);
					
					$heuteArr = $this->dbQuery("SELECT * FROM xplugin_jtl_eap_fulllog
            													WHERE xplugin_jtl_eap_fulllog.tstamp LIKE '%".$this->dbEscape(date("d.m.Y"))."%'	",2);
					$monatArr = $this->dbQuery("SELECT * FROM xplugin_jtl_eap_fulllog
            																WHERE xplugin_jtl_eap_fulllog.tstamp LIKE '%".$this->dbEscape(date("m.Y"))."%'",2);
					
			$heute = new stdClass();
			$heute->ok = 0;
			$heute->nichtok = 0;
			$heute->abbruch = 0;
			
			
					foreach($heuteArr as $value)
					{
						if($value->abschluss=="") $heute->abbruch++;
						if(substr($value->ergebnis,0,9) == " Nicht ok")
						$heute->nichtok++;
						if(substr($value->ergebnis,0,3) == " OK")
						$heute->ok++;
					}
			$monat = new stdClass();
			$monat->ok = 0;
			$monat->nichtok = 0;
			$monat->abbruch = 0;
					foreach($monatArr as $value)
					{
						if($value->abschluss=="") $monat->abbruch++;
						if(substr($value->ergebnis,0,9) == " Nicht ok")
						$monat->nichtok++;
						if(substr($value->ergebnis,0,3) == " OK")
						$monat->ok++;
						
					}
					
				
					$monat->gesamt = count($monatArr);
					$heute->gesamt = count($heuteArr);
					
					$this->oSmarty->assign("heute", $heute);
					$this->oSmarty->assign("monat", $monat);
    }

  
    public function getContent()
    {
	
		
     return $this->oSmarty->fetch(dirname(__FILE__) . '/BoniGatewayWidget.tpl');;
    
	}
}