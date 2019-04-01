<div class='eap_container' style='width:auto;heigth:auto'><div class='eap_note'>{$eap_notice}</div>
  {if $geb}
<div class='eap_geb'>{$geb_text}  <input type="text"  name="eap_geburtstag" value="{$dob_customer}" id="birthday" class="birthday eap_geburtstag"></div>
<div id='geb_error'  style='display:none'><b style='color:red'>Ihre Eingabe war leider nicht erfolgreich</b></div>
{/if}
  {if $tel}
<div class='eap_tel'>{$tel_text} <input type="text"  name="eap_telefon" value="{$tel_customer}" id="tel" class="tel eap_telefon"></div>
<div id='tel_error' style='display:none'><b style='color:red'>Ihre Eingabe war leider nicht erfolgreich</b></div>
{/if}
<div class='buttons'><button class="btn btn-primary submit eap_back" name="abbrechen" onclick="javascript:parent.jQuery.fancybox.close()">{$btn_close}</button><button class="btn btn-primary submit eap_continue"  onclick="javascript:validategeb({if $tel}true{else}false{/if},{if $geb}true{else}false{/if},'zahlung')" type="button">{$btn_submit}</button></div>
</div>