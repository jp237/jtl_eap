{literal}
<script type="text/javascript">
  function showhide(id) {
	
    if(document.getElementById(id).style.visibility == "inherit")
	{
		document.getElementById("lbl"+id).value = "[anzeigen]";
		document.getElementById(id).style.visibility = "collapse";
	}
	else
	{	
		document.getElementById("lbl"+id).value = "[verbergen]";
		document.getElementById(id).style.visibility = "inherit";
	}
  }
  function hide(id) {
    document.getElementById(id).style.display = "none";
  }
</script>
<style type="text/css">
.eap_tbl table {
	font: 85% 'Arial Unicode MS', sans-serif;padding: 0; margin: 0; border-collapse: collapse; color: #333; background: #FFF;
	}

.eap_tbl table a {
	color: #FFF; text-decoration: none; 
	}  

.eap_tbl table a:visited {
	color: #FFF;
	}

.eap_tbl table a:hover {
	color: #FFF;
	}  

.eap_tbl table caption {
	text-align: left; text-transform: uppercase;  padding-bottom: 10px; font: 200% 'Arial Unicode MS', sans-serif;
	}

.eap_tbl table thead th {
	background: #454545; padding: 4px 2px; color: #fff; text-align: left; font-weight: normal;
}

.eap_tbl table tbody, table thead {
	border-left: 1px solid #FFF; border-right: 1px solid #FFF;
	}


.eap_tbl table tbody tr {
	border-bottom:1px solid #D7D7D7;
}

.eap_tbl table tbody tr.odd {
	background: #F0F2F4;
	}

.eap_tbl table tbody  tr:hover {
	background: #EAECEE; color: #111;
	}

.eap_tbl table tfoot td, table tfoot th, table tfoot tr {
	text-align: left; font: 120%  'Arial Unicode MS', sans-serif; text-transform: uppercase; background: #fff; padding: 10px;
	}

</style>
{/literal}

<form method="post">
    <input type="hidden" name="kPlugin" value="{$oPlugin->kPlugin}">
    <input type="hidden" name="kPluginAdminMenu" value="{$eap_log_tab}" />
    Die letzten:&nbsp;
    <input type="text" class='form-control'  name="nEintraege" value="{$nEintraege}" size="4">&nbsp;Eintr&auml;ge
    <input type="submit" class='form-control'  name="btnEAPLimitLog" value="anzeigen">
   
    </form>
    
     <hr /><div class="eap_tbl">
    <table style='width:100%'>
        <thead><tr>
            <th>
      <strong>Datum</strong></th>
             <th><strong>
            Person/Firma
        </strong></th>
            <th><strong>Art</strong></th>
            <th><strong>Information</strong>
            <th><strong>Zahlungsart (Abschluss)</strong></th>
            <th><strong>Warenkorbh&ouml;he</strong></th>
            <th><strong>Weitere Pr&uuml;fungen</strong></th>
             </tr>
         </thead>
            {foreach from=$logarray item=data}
            <tr>
              <td>{$data->entrys[0]->tstamp}</td>
              <td>{$data->entrys[0]->customer_vname} {$data->entrys[0]->customer_nname} {$data->entrys[0]->customer_firma}</td>
              <td>{if $data->entrys[0]->ergebnis}<div style='color:red'>{$data->entrys[0]->cArt} - Nicht OK</div> {else}<div style='color:green'>{$data->entrys[0]->cArt} - OK</div>{/if}</td>
              <td>{$data->entrys[0]->responseText} {$data->entrys[0]->scoreInfo}</td>
              <td>{if !$data->entrys[0]->abschluss} <div style='color:red'>{$data->entrys[0]->zahlungsart}</div>               
                 {else} <div style='color:green'>{$data->entrys[0]->abschluss}</div>{/if}</td>
              <td>{$data->entrys[0]->warenkorb}</td>
              <td>{if $data->counter > 1 } <a style='color:blue' id='lbl{$data->token}' onclick="showhide('{$data->token}');"> [anzeigen]</a>{/if}</td>
    		</tr>
             {if $data->counter > 1}
                <tbody style='visibility:collapse' id="{$data->token}">
                  {foreach from=$data->entrys item=sub name=sub}
                   {if $smarty.foreach.sub.index>0} <tr>
                          <td>{$sub->tstamp}</td>
                          <td>{$sub->customer_vname} {$sub->customer_nname} {$sub->customer_firma}</td>
                          <td>{if $sub->ergebnis}<div style='color:red'>{$sub->cArt} - Nicht OK</div> {else}<div style='color:green'>{$sub->cArt} - OK</div>{/if}</td>
                          <td>{$sub->responseText}{$sub->scoreInfo}</td>
                          <td>{if !$sub->abschluss} <div style='color:red'>{$sub->zahlungsart}</div>               
                             {else}<div style='color:green'>{$sub->abschluss}</div>{/if}</td>
                          <td>{$sub->warenkorb}</td>
                          <td></td> 
                    </tr>
                    {/if}
                    {if $smarty.foreach.sub.index == $data->counter-1}
                    <tr>
                      <td colspan="7"><div align="center">----------------------------------------------------------------------------------------------------------------------------------------------</div></td></tr>
                    {/if}
                    {/foreach}  
                </tbody>
            {/if}
    {/foreach} 
    </table>
    </div>
    <hr />
    
 
    <form method="post">
    <input type="hidden" name="kPlugin" value="{$oPlugin->kPlugin}">
    <input type="hidden" name="kPluginAdminMenu" value="{$eap_log_tab}" />
    <input type="submit"  class='form-control'  name="btnEAPflushlog" value="Log leeren">
</form>
