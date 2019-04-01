<div class='eap_container' style='width:auto;heigth:auto'>
{if $check_firma}
<p><br />{$EAP_COMPANY_NOTICE}</p>
<select class='form-control' name="companyList">
<option {if $COMPANY_DROPDOWN_PRESELECT == "Privatkunde"} selected {/if} value='Privatkunde'>Kein Unternehmen ( Privatkunde )</option>
<option {if $COMPANY_DROPDOWN_PRESELECT == "Einzelunternehmen"} selected {/if} value='Einzelunternehmen'>Einzelunternehmen (Gewerbetreibende)</option>
<option {if $COMPANY_DROPDOWN_PRESELECT == "GBR"} selected {/if} value='GBR'>GbR (Gesellschaft b&uuml;rgerlichen Rechts)</option>
<option {if $COMPANY_DROPDOWN_PRESELECT == "EK"} selected {/if} value='EK'>eK (eingetragener Kaufmann)</option>
<option {if $COMPANY_DROPDOWN_PRESELECT == "UG"} selected {/if} value='UG'>UG (haftungsbeschr&auml;nkt)  & Co. KG</option>
<option {if $COMPANY_DROPDOWN_PRESELECT == "GMBH"} selected {/if} value='GMBH'>GmbH (Gesellschaft mit beschr&auml;nkter Haftung)</option>
<option {if $COMPANY_DROPDOWN_PRESELECT == "GMBHCOKG"} selected {/if} value='GMBHCOKG'>GmbH & Co. KG</option>
<option {if $COMPANY_DROPDOWN_PRESELECT == "GMBHCOOHG"} selected {/if} value='GMBHCOOHG'>GmbH & Co. OHG</option>
<option {if $COMPANY_DROPDOWN_PRESELECT == "AG"} selected {/if} value='AG'>AG (Aktiengesellschaft)</option>
<option {if $COMPANY_DROPDOWN_PRESELECT == "AGCOKG"} selected {/if} value='AGCOKG'>AG & Co. KG</option>
<option {if $COMPANY_DROPDOWN_PRESELECT == "AGCOOHG"} selected {/if} value='AGCOOHG'>AG & Co. OHG</option>
<option {if $COMPANY_DROPDOWN_PRESELECT == "OHG"} selected {/if} value='OHG'>OHG (Offene Handelsgesellschaft)</option>
<option {if $COMPANY_DROPDOWN_PRESELECT == "KG"} selected {/if} value='KG'>KG (Kommanditgesellschaft)</option>
<option {if $COMPANY_DROPDOWN_PRESELECT == "KGAA"} selected {/if} value='KGAA'>KGaA (Kommanditgesellschaft auf Aktien)</option>
<option {if $COMPANY_DROPDOWN_PRESELECT == "EG"} selected {/if} value='EG'>eG (Genossenschaft)</option>
<option {if $COMPANY_DROPDOWN_PRESELECT == "LTDCOKG"} selected {/if} value='LTDCOKG'>Limited & Co. KG</option>
<option {if $COMPANY_DROPDOWN_PRESELECT == "STIFTUNG"} selected {/if} value='STIFTUNG'>Stiftung & Co. KG</option>
<option {if $COMPANY_DROPDOWN_PRESELECT == "STIFTUNGGMBH"} selected {/if} value='STIFTUNGGMBH'>Stiftung GmbH & Co. KG</option>
<option {if $COMPANY_DROPDOWN_PRESELECT == "sonstiges"} selected {/if} value='sonstiges'>Sonstiges</option>
</select>
<div id='company_error' style='display:none'><b style='color:red'>Ihre Eingabe war leider nicht erfolgreich</b></div>
{/if}
{if $geb}
<div class='EAPhideDateOfBirth' style="display:table-row">
{if ($geb || $tel) && $check_firma } <hr /> {/if}
<div class='eap_note'>{$eap_notice}</div>
<div class='eap_geb'>{$geb_text}  <input type="text"  name="eap_geburtstag" value="{$dob_customer}" id="birthday" placeholder="Format: 23.12.1999" required class="birthday eap_geburtstag form-control datepicker"></div>
<div id='geb_error'  style='display:none'><b style='color:red'>Ihre Eingabe war leider nicht erfolgreich</b></div>
</div>
{/if}
{if $tel}
<div class='eap_tel'>{$tel_text} <input type="text"  name="eap_telefon" value="{$tel_customer}" id="tel" placeholder="Format : 12345 55555" required class="tel eap_telefon form-control"></div>
<div id='tel_error' style='display:none'><b style='color:red'>Ihre Eingabe war leider nicht erfolgreich</b></div>
{/if}
<div class='eap_optin'><div style='width:25px;float:left;'><input type='checkbox' disabled id='eap_optin' name='eap_optin' /></div><div><b>{$EAP_CUSTOM_OPTIN}</b></div>
<div id='optin_error' style='display:none'><b style='color:red'>Ihre Eingabe war leider nicht erfolgreich</b></div></div>
<div class='buttons'><button class="btn btn-primary submit eap_back" name="abbrechen" onclick="javascript:parent.jQuery.fancybox.close()">{$btn_close}</button><button class="btn btn-primary submit eap_continue"  onclick="javascript:getValidation('{$selector_zahlung}')" type="submit">{$btn_submit}</button></div>

</div>
