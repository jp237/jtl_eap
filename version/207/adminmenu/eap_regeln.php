<?php
// Variablen die bei jedem Plugin existieren
// $GLOBALS['smarty']               Smarty Template Engine Object
// $GLOBALS['oPlugin']              Plugin Object
global $smarty, $oPlugin;

require_once($oPlugin->cFrontendPfad . 'inc/class.JTL-Shop.EAP.php');

$oEAPPruefung = new EAPPruefung(0);


if(isset($_POST['btnOptIn'])){
$update  = new stdClass();
$update->nOptIn = (int) $_POST['nOptIn'];
$oEAPPruefung->dbUpdate("xplugin_jtl_eap_zahlungsarten","kZahlungsart",(int)$_POST['kZahlungsart'],$update);
}

if(isset($_POST['addwhitelist'] ))
{
	$oEAPPruefung->updatewhiteListEntry((int) $_POST['whitelistkKunde'],false);
}
if(isset($_POST['removewhitelist'] ))
{
	$oEAPPruefung->updatewhiteListEntry((int) $_POST['whitelistkKunde'],true);
}
// Formular wurde abgeschickt
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	   if(@$_POST['versand_freigeben'])
	{
		$oEAPPruefung->neueVersandRegel($_POST['kVersandart'],0);
	}
	if(@$_POST['versand_ausschliessen'])
	{
		$oEAPPruefung->neueVersandRegel(0,$_POST['kVersandart']);
	}
    if (isset($_POST['btnEdit']))
    {
        if (isset($_POST['bAktiv']))
        {

        	// Neue Regel anlegen
        	$nReturnCode = $oEAPPruefung->neueRegel($_POST['kZahlungsart'], $_POST['nMaxScore']);
        	
        	if ($nReturnCode)
        	{
        	//	$smarty->assign("cEAPFehler", $oEAPPruefung->mappeFehlercode($nReturnCode));
        	}
        	else
        	{
        		$smarty->assign("cEAPHinweis", "Die Einstellung wurde erfolgreich &uuml;bernommen!");
        	}
        }
        else 
        {
        	// Bestehende Regel entfernen
        	if ($_POST['kZahlungsart']>0)
        	{
        		$bOk = $oEAPPruefung->entferneRegel($_POST['kZahlungsart']);
        		
        		if ($bOk)
					$smarty->assign("cEAPHinweis", "Die Pr&uuml;fung wurde erfolgreich deaktiviert!");
				else
					$smarty->assign("cEAPFehler", "Die Pr&uuml;fung konnte nicht deaktiviert werden!");
        	}
        	else
        		$smarty->assign("cEAPFehler", "Es ist ein Fehler aufgetreten!");
        }
    }
	
	
			if(isset($_POST['identfreigabe'])){
			$oEAPPruefung->updateRegel("ident",$_POST["kKundengruppe"],1);
		}
			if(isset($_POST['identpruefen'])){
			$oEAPPruefung->updateRegel("ident",$_POST["kKundengruppe"],0);
			$oEAPPruefung->updateRegel("move",$_POST["kKundengruppe"],0);
		}
			if(isset($_POST['bonifreigabe'])){
			$oEAPPruefung->updateRegel("boni",$_POST["kKundengruppe"],1);
		}
			if(isset($_POST['bonipruefen'])){
			$oEAPPruefung->updateRegel("boni",$_POST["kKundengruppe"],0);
		}
			if(isset($_POST['bonipruefen'])){
			$oEAPPruefung->updateRegel("boni",$_POST["kKundengruppe"],0);
		}
		if(isset($_POST['identMove'])){
			$oEAPPruefung->updateRegel("move",$_POST["kKundengruppe"],1);
		}
			if(isset($_POST['moveAufheben'])){
			$oEAPPruefung->updateRegel("move",$_POST["kKundengruppe"],0);
		}
}

$oZahlungsarten_arr = $oEAPPruefung->dbQuery("SELECT tzahlungsart.*, xplugin_jtl_eap_zahlungsarten.nMaxScore, xplugin_jtl_eap_zahlungsarten.nMaxName ,xplugin_jtl_eap_zahlungsarten.nOptIn
									from tversandartzahlungsart left join tzahlungsart on tversandartzahlungsart.kZahlungsart = tzahlungsart.kZahlungsart left outer join
									 xplugin_jtl_eap_zahlungsarten on xplugin_jtl_eap_zahlungsarten.kZahlungsart = tzahlungsart.kZahlungsart group by kZahlungsart",2);


$oKundengruppen_arr = $oEAPPruefung->leseKundengruppen();
$smarty->assign('oKundengruppen_arr', $oKundengruppen_arr);


$oEAPPruefung->createKundenGruppenEntry();
$gruppen_arr = $oEAPPruefung->leseKundengruppen();

$oWhitelist_arr =  $oEAPPruefung->dbQuery("SELECT tkunde.cKundenNr,tkunde.cVorname,tkunde.cNachname,tkunde.cOrt,IFNULL(nBoni,0) as nBoni,tkunde.kKunde,tkunde.cStrasse FROM `xplugin_jtl_eap_whitelist` LEFT JOIN tkunde ON `tkunde`.`kKunde` = `xplugin_jtl_eap_whitelist`.`kKunde` ",2);
$decrypted_whitelist = array();


foreach($oWhitelist_arr as $whitelistEntry)
{
	$entry = new stdClass();
	$entry->kKunde = $whitelistEntry->kKunde;
	$entry->nBoni = $whitelistEntry->nBoni;
	$entry->cKundenNr = $whitelistEntry->cKundenNr;
	$entry->cVorname = $whitelistEntry->cVorname;
	$entry->cNachname = $oEAPPruefung->getDecrypted($whitelistEntry->cNachname);
	$entry->cStrasse = $oEAPPruefung->getDecrypted($whitelistEntry->cStrasse);
	$entry->cOrt = $whitelistEntry->cOrt;
	$decrypted_whitelist[] = $entry;
}

$suchergebnisse = array();
$decrypted_searcherg = array();
if(isset($_POST['suchekunde']))
{
	
	$select = "SELECT tkunde.cKundenNr,tkunde.cVorname,tkunde.cNachname,tkunde.cOrt,IFNULL(nBoni,0) as nBoni,tkunde.kKunde,tkunde.cStrasse FROM tkunde 
										 LEFT OUTER JOIN `xplugin_jtl_eap_whitelist` ON `tkunde`.`kKunde` = `xplugin_jtl_eap_whitelist`.`kKunde` where tkunde.nRegistriert = 1 ";
	if(strlen($_POST['cVorname'])>0) $select.= " AND tkunde.cVorname LIKE '%".$oEAPPruefung->dbEscape($_POST['cVorname'])."%' ";
	if(strlen($_POST['cOrt'])>0) $select.= " AND tkunde.cOrt LIKE '%".$oEAPPruefung->dbEscape($_POST['cOrt'])."%' ";		
	if(strlen($_POST['cKundenNr'])>0) $select.= " AND tkunde.cKundenNr = '".$oEAPPruefung->dbEscape($_POST['cKundenNr'])."' ";	
					 
	$select .="									  LIMIT 0 , 30 ";
	
	$searcherg = $oEAPPruefung->dbQuery($select,2);

	foreach($searcherg as $whitelistEntry)
	{
		$entry = new stdClass();
		$entry->kKunde = $whitelistEntry->kKunde;
		$entry->nBoni = $whitelistEntry->nBoni;
		$entry->cKundenNr = $whitelistEntry->cKundenNr;
		$entry->cVorname = $whitelistEntry->cVorname;
		$entry->cNachname = $oEAPPruefung->getDecrypted($whitelistEntry->cNachname);
		$entry->cStrasse = $oEAPPruefung->getDecrypted($whitelistEntry->cStrasse);
		$entry->cOrt = $whitelistEntry->cOrt;
		$decrypted_searcherg[] = $entry;
	}
}

$smarty->assign('whitelist_arr', $decrypted_whitelist);
$smarty->assign('searcherg_arr', $decrypted_searcherg);


if ($gruppen_arr)
    $smarty->assign('oKundengruppen_arr', $gruppen_arr);

$versandarten = $oEAPPruefung->getShippingMethods();

if($versandarten)
	$smarty->assign('versandarten_arr',$versandarten);


$smarty->assign('oZahlungsarten_arr',$oZahlungsarten_arr);

$stepPlugin = 'eap_regeln';
$oTab =  $oEAPPruefung->dbQuery("SELECT * FROM tpluginadminmenu WHERE kPlugin=" . (int)$oPlugin->kPlugin . " AND cDateiname='eap_regeln.php'",1);
$smarty->assign('eap_regeln_tab', $oTab->kPluginAdminMenu);


$smarty->assign("URL_SHOP", URL_SHOP);
$smarty->assign("URL_ADMINMENU", URL_SHOP . "/" . PFAD_PLUGIN . $oPlugin->cVerzeichnis . "/" . PFAD_PLUGIN_VERSION . $oPlugin->nVersion . "/" . PFAD_PLUGIN_ADMINMENU);
$smarty->assign("stepPlugin", $stepPlugin);

$smarty->display($oPlugin->cAdminmenuPfad . "template/eap.tpl");

