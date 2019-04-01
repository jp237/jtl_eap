<?php
// Variablen die bei jedem Plugin existieren
// $GLOBALS['smarty']               Smarty Template Engine Object
// $GLOBALS['oPlugin']              Plugin Object
global $smarty, $oPlugin;

require_once($oPlugin->cFrontendPfad . 'inc/class.JTL-Shop.EAP.php');

$oEAPPruefung = new EAPPruefung(0);


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

$oZahlungsarten_arr = $oEAPPruefung->dbQuery("SELECT tzahlungsart.*, xplugin_jtl_eap_zahlungsarten.nMaxScore, xplugin_jtl_eap_zahlungsarten.nMaxName 
									from tversandartzahlungsart left join tzahlungsart on tversandartzahlungsart.kZahlungsart = tzahlungsart.kZahlungsart left outer join
									 xplugin_jtl_eap_zahlungsarten on xplugin_jtl_eap_zahlungsarten.kZahlungsart = tzahlungsart.kZahlungsart group by kZahlungsart",2);


$oKundengruppen_arr = $oEAPPruefung->leseKundengruppen();
$smarty->assign('oKundengruppen_arr', $oKundengruppen_arr);


$oEAPPruefung->createKundenGruppenEntry();
$gruppen_arr = $oEAPPruefung->leseKundengruppen();

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

