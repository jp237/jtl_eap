<?php
// Variablen die bei jedem Plugin existieren
// $GLOBALS['smarty']               Smarty Template Engine Object
// $GLOBALS['oPlugin']              Plugin Object
global $smarty, $oPlugin;


require_once($oPlugin->cFrontendPfad . 'inc/class.JTL-Shop.EAP.php');
$oEAPPruefung = new EAPPruefung(0);
$stepPlugin = "eap_info";

// Aktiven Tab ermitteln
$oTab =  $oEAPPruefung->dbQuery("SELECT * FROM tpluginadminmenu WHERE kPlugin=" . (int)$oPlugin->kPlugin . " AND cDateiname='eap_info.php'",1);
$smarty->assign('eap_info_tab', $oTab->kPluginAdminMenu);
$smarty->assign("URL_SHOP", URL_SHOP);
$smarty->assign("gwVersion", $oPlugin->nVersion);
$smarty->assign("URL_ADMINMENU", URL_SHOP . "/" . PFAD_PLUGIN . $oPlugin->cVerzeichnis . "/" . PFAD_PLUGIN_VERSION . $oPlugin->nVersion . "/" . PFAD_PLUGIN_ADMINMENU);
$smarty->assign("stepPlugin", $stepPlugin);


$smarty->assign('eap_gw_user', $oPlugin->oPluginEinstellungAssoc_arr['jtl_eap_userid']);
$smarty->assign('eap_gw_passwd', md5($oPlugin->oPluginEinstellungAssoc_arr['jtl_eap_passwort']));

$gwusr = $oPlugin->oPluginEinstellungAssoc_arr['jtl_eap_userid'];
$gwpwd = md5($oPlugin->oPluginEinstellungAssoc_arr['jtl_eap_passwort']);




			try
					{
					$client = new SoapClient("https://webservice.inkasso-vop.de/webservice.php?wsdl",array('cache_wsdl' => WSDL_CACHE_NONE));
					$result = $client->getGatewayLogin($gwusr,$gwpwd);
                 	if($result->status=="True")
                    {
						
						$smarty->assign("eap_state","LOGIN");
						 $projekte = $client->getProject($gwusr,$gwpwd,"1"); 
						 
						 if(isset($projekte[0]->Error))
						 {
						  $smarty->assign("eap_state","NOPROJECT");
						 }
						 else
						 {
							 
							 $encProjects= array();
							 $b2bProjects = array();
							 $b2bcount = 0;
							 $b2ccount = 0;
							 for($i=0;$i<count($projekte);$i++)
							 {
								 if($projekte[$i]->projecttype == "B2C")
								 {
								$encProjects[$b2ccount] = new stdClass();
								$encProjects[$b2ccount]->bezeichnung = utf8_decode($projekte[$i]->bezeichnung);
								$encProjects[$b2ccount]->row = $b2ccount;
								$b2ccount++;
								 }
								 else
								 {
									 $b2bProjects[$b2bcount] = new stdClass();
									 $b2bProjects[$b2bcount]->bezeichnung = utf8_decode($projekte[$i]->bezeichnung);
									 $b2bProjects[$b2bcount]->row = $b2bcount;
									 $b2bcount++;
								 }
							 }
							 
					  
						 $smarty->assign("eap_projekte",$encProjects);
						 $smarty->assign("eap_projekteb2b",$b2bProjects);
						}
					}
					else
					{
						$smarty->assign("eap_state","NOLOGIN");
					}
					}
					catch(Exception $e)
					{
						
						$smarty->assign("eap_state","COMERR");
						$smarty->assign("eap_exception",$e);
					}
					

$smarty->display($oPlugin->cAdminmenuPfad . "template/eap.tpl");

