{literal}
<style type="text/css">
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


-->
</style>


<script type="text/javascript">
  function show(id) {
    document.getElementById(id).style.display = "block";
  }
  function hide(id) {
    document.getElementById(id).style.display = "none";
  }
</script>
{/literal}
<table width="100%" border="0">
  {if $eap_state == 'NOLOGIN'}
   <tr><td colspan="2" style='background-color:#F8898C'>Das BoniGateway ist nicht einsatzbereit (Login)</td></tr>
  {elseif $eap_state=='NOPROJECT'}
  <tr><td colspan="2" style='background-color:#F8898C'>Das BoniGateway ist nicht einsatzbereit (Keine Projekte definiert)</td></tr>
  {elseif $eap_state=='COMERR'}
   <tr><td colspan="2" style='background-color:#F8898C'>Das BoniGateway ist nicht einsatzbereit (Kommunikationsfehler <a href='mailto:support@eaponline.de'>Support</a>)</td></tr>
  {elseif $eap_state=='LOGIN'}
  <tr><td colspan="2" style='background-color:#CEFFAA'>Das BoniGateway ist mit den folgenden Projekten einsatzbereit <a onmouseover="show('projekte');" onmouseout="hide('projekte');">[anzeigen]</a></td></tr>
  <tr id='projekte' style="display:none"><td><strong>Projekte Privatpersonen </strong>
    <table>
   {foreach from=$eap_projekte item=projekte}
  <tr><td colspan="2">{$projekte->bezeichnung}</td></tr>
   {/foreach}
  </table></td><td valign="top"><strong>Projekte Firmen </strong>
    <table>
      {foreach from=$eap_projekteb2b item=projekteb2b}
  <tr><td colspan="2">{$projekteb2b->bezeichnung}</td></tr>
   {/foreach}
  {/if}             
  </table></td></tr></table>



<div id="eap_info" style="">
<iframe frameborder="0" src='https://www.eaponline.de/gwpage?version={$gwVersion}' width='100%' scrolling='no' height='800px'></iframe>
</div>