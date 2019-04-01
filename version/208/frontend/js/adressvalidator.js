
var data_submitted = false;

   $(document).ready(function() {
       var formSelector = $('#firstName').closest('form');
        $(formSelector).submit( function(formval) {
            if( !data_submitted &&  $('#firm').val() == ''){
                var validationModalTemplate = $('#eap_validation_modal_template');
                if(validationModalTemplate.length == 0){
                    $('body').append("<div id='eap_validation_modal_template'></div>");
                }
               if($('#eap_invoice_address_postdirekt_type').length == 0){
                   $(formSelector).append("<input type='hidden' name='eap_invoice_address_postdirekt_type' id='eap_invoice_address_postdirekt_type' value='0'>")
               }
                if($('#eap_shipping_address_postdirekt_type').length == 0){
                    $(formSelector).append("<input type='hidden' name='eap_shipping_address_postdirekt_type' id='eap_shipping_address_postdirekt_type' value='0'>")
                }
                formval.preventDefault();
                var abortSubmit = false;
                var params = {
                    adressvalidation : {
                        billing: {
                            firstname: $('#firstName').val(),
                            lastname: $('#lastName').val(),
                            street: $('#street').val() + " " + $('#streetnumber').val(),
                            zipcode: $('#postcode').val(),
                            city: $('#city').val(),
                            country: $('#country').val(),
                        },
                        shipping: {
                            useShippingAdress: !$('#checkout_register_shipping_address').is(':checked'),
                            firstname: $('#register-shipping_address-firstName').val(),
                            lastname: $('#register-shipping_address-lastName').val(),
                            street: $('#register-shipping_address-street').val() + " " + $('#register-shipping_address-streetnumber').val(),
                            zipcode: $('#register-shipping_address-postcode').val(),
                            city: $('#register-shipping_address-city').val(),
                            country: $('#register-shipping_address-country').val(),
                        },
                    },
                };
                $.ajax({
                    dataType : "json",
                    type : 'post',
                    data : jQuery.param({ io : params }),
                    contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
                    url: "/io.php",
                    beforeSend: function( xhr ) {
                        xhr.overrideMimeType( "text/plain; charset=x-user-defined" );
                    }
                })
                    .done(function( data ) {
                        if(data.actionRequired == true) {
                            $('#eap_validation_modal_template').html(data.htmlModal);
                            $.fancybox({
                                'scrolling': 'no',
                                'overlayOpacity': 0.9,
                                'width': "600px",
                                'showCloseButton': true,
                                'href': '#eap-adressvalidation'
                            });
                        }else{
                            data_submitted = true;
                            $( formSelector).submit();
                        }
                    });
            }
        });

    });


