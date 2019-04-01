<style type="text/css">
<!--
{literal}
ul.eap_liste {
	list-style-type: none;
    padding: 0px;
}

li.aktiv_head {
	float:left;
	width:120px;
	padding: 2px;
	padding-bottom:5px;
	font-weight:bold;
	border-bottom:1px solid #c0c0c0;
}

li.name_head {
	float:left;
	width:250px;
	padding: 2px;
	padding-bottom:5px;
	font-weight:bold;
	border-bottom:1px solid #c0c0c0;
}

li.score_head {
	float:left;
	width:200px;
	padding: 2px;
	padding-bottom:5px;
	font-weight:bold;
	border-bottom:1px solid #c0c0c0;
}

li.aktion_head {
	float:none;
	padding: 2px;
	padding-bottom:5px;
	font-weight:bold;
	border-bottom:1px solid #c0c0c0;
}

li.aktiv {
	float:left;
	width:120px;
	padding: 2px;
}

li.name {
	float:left;
	width:250px;
	padding: 2px;
}

li.score {
	float:left;
	width:200px;
	padding: 2px;
}

li.aktion {
	float:none;
	padding: 2px;
	text-align:center;
	border-bottom:1px solid #c0c0c0;
}

{/literal}
-->
</style>

<h1>Zahlungsarten</h1>
<div class="eap_tbl">
<table><tr><td width="397"></td><td width="918"></td></tr>

<thead>
<tr><th><strong>Zahlartname</strong></th><th><strong>Aktion</strong></th></tr></thead>
   {foreach from=$oZahlungsarten_arr item=oZahlungsart}
    <form method="post">
    <input type="hidden" name="kPlugin" value="{$oPlugin->kPlugin}">
    <input type="hidden" name="kPluginAdminMenu" value="{$eap_regeln_tab}" />
    <input type="hidden" name="kZahlungsart" value="{$oZahlungsart->kZahlungsart}" />
<tr  {if $oZahlungsart->nMaxScore} style='background-color:#CEFFAA'{/if}><td>{$oZahlungsart->cName}</td><td>


{if $oZahlungsart->nMaxScore}<input type="hidden" name="nMaxScore" value="0"> <input type="hidden" name="bAktivDisable" value="" />   <input style='width:150px'  class='form-control' type="submit" name="btnEdit" value="Deaktivieren"> {else} <input style='width:150px' type="hidden" name="nMaxScore" value="3"> <input type="hidden" name="bAktiv" value="on" />   <input class='form-control' style='width:150px'  type="submit" name="btnEdit" value="Aktivieren">{/if} 
          
          </td></tr>
          </form>
{/foreach}

</table>

</div>

<hr />
<h1>Kundengruppen</h1>
<div class="eap_tbl">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <thead><tr>
    <th><strong> Kundengruppe</strong></th>
    <th><strong>Bonit&auml;tspr&uuml;fung/DeviceSecure</strong></th>
    <th><strong>Identit&auml;tspr&uuml;fung</strong></th>
    <th><strong>In Kundengruppe verschieben</strong></th>
  </tr>
  </thead>
   {foreach from=$oKundengruppen_arr item=oKundengruppe}
    <form method="post">
    <input type="hidden" name="kPlugin" value="{$oPlugin->kPlugin}">
    <input type="hidden" name="kPluginAdminMenu" value="{$eap_regeln_tab}" />
    <input type="hidden" name="kKundengruppe" value="{$oKundengruppe->kGruppe}" />
   <tr><td>{$oKundengruppe->cName}</td><td> {if $oKundengruppe->nBoni== 1}
    <input type="submit" style='width:150px' class='form-control'  name="bonipruefen" value="Pr&uuml;fen"/> 
   {else} 
   <input type="submit" style='width:150px' class='form-control'  name="bonifreigabe"  value="Ausschliessen" />
   {/if} </td><td>{if $oKundengruppe->nIdent== 1}<input class='form-control'  style='width:150px' type="submit" name="identpruefen" value="Pr&uuml;fen"/>
{else}
<input type="submit"style='width:150px' class='form-control'   name="identfreigabe"  value="Ausschliessen"/>
    {/if} </td>
       <td>{if $oKundengruppe->nIdent== 1}{if $oKundengruppe->nIdentMove == 0} <input type="submit" class='form-control'  name="identMove" style='width:auto'  value="in Kundengruppe verschieben" /> {else} <input  class='form-control' style='width:150px'  type="submit" name="moveAufheben" value="Nicht verschieben" /> {/if}
    {/if} </td></tr>
   </form>
   {/foreach}
  </table>
</div>

<hr />
<h1>Versandarten - Identit&auml;tspr&uuml;fung</h1>
<div class="eap_tbl">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <thead><tr>
    <th><strong> Versandart</strong></th>
    <th><strong>Aktion</strong></th>
  </tr>
  </thead>
   {foreach from=$versandarten_arr item=versandart}
    <form method="post">
    <input type="hidden" name="kPlugin" value="{$oPlugin->kPlugin}">
    <input type="hidden" name="kPluginAdminMenu" value="{$eap_regeln_tab}" />

    <input type="hidden" name="kVersandart"  value="{$versandart->kVersandart}" />
   <tr>
     <td>{$versandart->cName} </td><td> {if $versandart->checkValue > 0}
    <input class='form-control'  type="submit" style='width:150px'  name="versand_freigeben" value="Freigeben"/> 
   {else} 
   <input class='form-control'  type="submit" style='width:150px' name="versand_ausschliessen"  value="Ausschliessen" />
   {/if} </td>
   </form>
   {/foreach}
  </table>
</div>
