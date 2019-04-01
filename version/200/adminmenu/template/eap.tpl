{if $stepPlugin == "eap_regeln"}
    {assign var=cTPLPfad value="`$oPlugin->cAdminmenuPfad`template/tpl_inc/eap_regeln.tpl"}
    {include file="$cTPLPfad"}
{elseif $stepPlugin == "eap_log"}
    {assign var=cTPLPfad value="`$oPlugin->cAdminmenuPfad`template/tpl_inc/eap_log.tpl"}
    {include file="$cTPLPfad"}
{elseif $stepPlugin == "eap_kontakt"}
    {assign var=cTPLPfad value="`$oPlugin->cAdminmenuPfad`template/tpl_inc/eap_kontakt.tpl"}
    {include file="$cTPLPfad"}
{elseif $stepPlugin == "eap_info"}
    {assign var=cTPLPfad value="`$oPlugin->cAdminmenuPfad`template/tpl_inc/eap_info.tpl"}
    {include file="$cTPLPfad"}
{/if}