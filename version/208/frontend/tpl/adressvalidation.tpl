<div id="eap-adressvalidation" style="min-width: 600px !important;">
    <script type="text/javascript">
        function handleInput(){
            parent.jQuery.fancybox.close();
            var getForm = $('#firstName').closest('form');
            if($('input[name=update-billing-adress]:checked').val() == 1){
                $('#eap_invoice_address_postdirekt_type').val('1');
                $('#firstName').val('{$responseparams.billing.firstname}');
                $('#lastName').val('{$responseparams.billing.lastname}');
                $('#street').val('{$responseparams.billing.street}');
                $('#city').val('{$responseparams.billing.city}');
                $('#postcode').val('{$responseparams.billing.zipcode}');
            }
            if($('input[name=update-shipping-adress]:checked').val() == 1){
                $('#eap_shipping_address_postdirekt_type').val('1');
                $('#register-shipping_address-firstName').val('{$responseparams.shipping.firstname}');
                $('#register-shipping_address-lastName').val('{$responseparams.shipping.lastname}');
                $('#register-shipping_address-street').val('{$responseparams.shipping.street}');
                $('#register-shipping_address-city').val('{$responseparams.shipping.city}');
                $('#register-shipping_address-postcode').val('{$responseparams.shipping.zipcode}');
            }
            data_submitted = true;
            $(getForm).submit();
        }
    </script>
    {$eap_adressvalidation_descriptiontext}
    {if $responseparams.billing.correctionRequired == true}
        <div  style="margin-top:15px;" class="confirm--inner-container block">
            <div class="payment--method-list has--border is--rounded block">
                <h3 class="payment--method-headline panel--title is--underline">Rechnungsadresse wählen</h3>
                <div class="panel--body is--wide block-group">
                    <div class="block method">
                        <div class="method--input ">
                            <input type="radio" name="update-billing-adress" id="billing_address_old"  value="0">  <label class="method--name is--strong" for="billing_address_old">{$responseparams.billing.firstname} {$requestparams.billing.lastname} {$requestparams.billing.street} {$requestparams.billing.streetnumber} {$requestparams.billing.zipcode} {$requestparams.billing.city} ( Ihre Eingabe ) </label>
                        </div>
                        <div class="method--input ">
                            <input type="radio" name="update-billing-adress" id="billing_address_new" checked value="1">  <label class="method--name is--strong" for="billing_address_new">{$responseparams.billing.firstname} {$responseparams.billing.lastname} {$responseparams.billing.street} {$responseparams.billing.streetnumber} {$responseparams.billing.zipcode} {$responseparams.billing.city}  </label>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    {/if}
    {if $responseparams.shipping.correctionRequired == true}
        <div style="margin-top:15px;" class="confirm--inner-container block">
            <div class="payment--method-list  has--border is--rounded block">
                <h3 class="payment--method-headline panel--title is--underline">Lieferadresse wählen</h3>
                <div class="panel--body is--wide block-group">
                    <div class="block method">
                        <div class="method--input ">
                            <input type="radio" name="update-shipping-adress" id="shipping_address_old" value="0">  <label class="method--name is--strong" for="shipping_address_old">{$requestparams.shipping.firstname} {$requestparams.shipping.lastname}  {$requestparams.shipping.street} {$requestparams.shipping.streetnumber} {$requestparams.shipping.zipcode} {$requestparams.shipping.city} ( Ihre Eingabe ) </label>
                        </div>
                        <div class="method--input ">
                            <input type="radio" name="update-shipping-adress" id="shipping_address_new" checked value="1">  <label class="method--name is--strong" for="shipping_address_new">{$responseparams.responseparams.firstname} {$requestparams.shipping.lastname}  {$responseparams.shipping.street} {$responseparams.shipping.streetnumber} {$responseparams.shipping.zipcode} {$responseparams.shipping.city}  </label>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    {/if}
    <div style="float:right">
        <button type="submit" style="margin-top:15px"  class="register--submit btn is--primary is--large is--icon-right has--error" onclick="handleInput()" name="Submit">Weiter <i class="icon--arrow-right"></i></button>
    </div>
</div>