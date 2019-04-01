<?php
// Variablen die bei jedem Plugin existieren
// $GLOBALS['smarty']               Smarty Template Engine Object
// $GLOBALS['oPlugin']              Plugin Object
global $smarty, $oPlugin;


require_once($oPlugin->cFrontendPfad . 'inc/class.JTL-Shop.EAP.php');

$oEAPPruefung = new EAPPruefung(0);

$nEintraege = 25;

// Formular wurde abgeschickt
if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    if (isset($_POST['btnEAPLimitLog']))
    {
        $nEintraege = (int)$_POST['nEintraege'];
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    if (isset($_POST['btnEAPflushlog']))
    {
      $oEAPPruefung->dbQuery("TRUNCATE TABLE xplugin_jtl_eap_fulllog",3);
    }
}
    




$smarty->assign('nEintraege', $nEintraege);

$stepPlugin = 'eap_log';
$oTab =  $oEAPPruefung->dbQuery("SELECT * FROM tpluginadminmenu WHERE kPlugin=" . (int)$oPlugin->kPlugin . " AND cDateiname='eap_log.php'",1);
$smarty->assign('eap_log_tab', $oTab->kPluginAdminMenu);

$groupedTransactions =   $oEAPPruefung->dbQuery("SELECT sessToken FROM `xplugin_jtl_eap_fulllog` group by sessToken order by logid desc LIMIT " . (int)$nEintraege,2);
$output = array();
$id = 0;
foreach($groupedTransactions as $transaction)
{
	$values =  $oEAPPruefung->dbQuery("SELECT * FROM `xplugin_jtl_eap_fulllog` where sessToken ='".$oEAPPruefung->dbEscape($transaction->sessToken)."' order by sessToken,tstamp desc LIMIT " . (int)$nEintraege,2);
	$id++;
	$output[$id] = new stdClass();
	$output[$id]->token = @$transaction->sessToken;
	$output[$id]->counter = count($values);
	$output[$id]->entrys =   $oEAPPruefung->dbQuery("SELECT * FROM `xplugin_jtl_eap_fulllog` where sessToken ='".$oEAPPruefung->dbEscape($transaction->sessToken)."' order by sessToken,tstamp desc LIMIT " .(int) $nEintraege,2);
}

$smarty->assign('logarray',$output);
$smarty->assign("URL_SHOP", URL_SHOP);
$smarty->assign("URL_ADMINMENU", URL_SHOP . "/" . PFAD_PLUGIN . $oPlugin->cVerzeichnis . "/" . PFAD_PLUGIN_VERSION . $oPlugin->nVersion . "/" . PFAD_PLUGIN_ADMINMENU);
$smarty->assign("stepPlugin", $stepPlugin);

$smarty->display($oPlugin->cAdminmenuPfad . "template/eap.tpl");
