{if $IDENT_FAILED || $POSTID_REQUEST}
<div class='eap_container'><div class='eap_note'><strong>{if $jtl_eap_identcheck_required} {$jtl_eap_identcheck_required} {else}{$identcheck_failed_headline}{/if}</strong></div>
{if $QBIT_FAILED}
<div class='qbit_failed'>{$identcheck_failed_msg}
{if $identcheck_qbit_output}<div class='qbit_output'><em>{$identcheck_qbit_dataerror_msg} :</em><strong> {$identcheck_qbit_dataerror}</strong></div>
{/if}</div>
{/if}
{if $POSTID_REQUEST}
<div class='postIdent'>{if $postident_notice_highcart} <br /><strong>{$postident_notice_highcart}<br /></strong>{/if}<br />{$postident_notice_identcheck}{$postident_notice_agecheck}
{if $postident_ausweisen}<div class>{$postident_ausweisen}<br /><button border="0" type="button" class="eap_continue_postident_ausweisen" onclick="document.getElementById('eap_cmd').value='idcard2Request';this.form.submit();"></button></div>{/if}
{if $postident_register}<div class>{$postident_register}<br /><button border="0" type="button" class="eap_continue_postident_register" onclick="window.open('https://postident.deutschepost.de/nutzerportal/register')"></button></div>{/if}
{if $postident_identify}<div class>{$postident_identify}<br /><button border="0" type="button" class="eap_continue_postident_identifizieren" onclick="document.getElementById('eap_cmd').value='idcard2Request';this.form.submit();"></button></div>{/if}
</div>
{/if}
</div>
{else}
<div class="eap_container">{$identcheck_notice}
<div class='eap_geb'>{$geb_text}  <input type="text"  name="eap_geburtstag" value="{$dob_customer}" id="birthday" class="birthday eap_geburtstag"></div>
<div id='geb_error' style='display:none'><b style='color:red'>Ihre Eingabe war leider nicht erfolgreich</b></div>
<div class='buttons'>{if !$POSTID_REQUEST}<button class="btn btn-primary submit eap_continue" onclick="javascript:validategeb(false,true,'eap_identcheck')" type="button">{$btn_submit}</button>
  <p>{/if}</p>
  <p>&nbsp;</p>
  <p>&nbsp;</p>
  <p>&nbsp;</p>
  <p>&nbsp;</p>
  <p>&nbsp;</p>
  <p>&nbsp;</p>
  <p>&nbsp;</p>
  <p>&nbsp;</p>
  <p>&nbsp;</p>
</div>
</div>
{/if}
