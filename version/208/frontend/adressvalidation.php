<?php
/**
 * Created by PhpStorm.
 * User: J.Perzewski
 * Date: 19.03.2019
 * Time: 10:01
 */

//print_r($oPlugin);
$requestData = $_POST['io'];
global $smarty;


if(isset($requestData['adressvalidation'])) {
    header('Content-Type: application/json');

    $sessionvalue = $_SESSION['eap_adressvalidation'];
    $inputParams = $requestData["adressvalidation"];


    $inputHandle = md5(print_r($inputParams, true));

    if ($sessionvalue != null) {

        $sessionhandle = md5(print_r($sessionvalue["request"], true));
    }
    include_once dirname(__FILE__) . '/inc/class.EAP-BoniGateway.php';

        $EAPBonigateway = new EAPBoniGateway();

        $EAPBonigateway->getCurrentPluginConfig($oPlugin, $smarty);
         $smarty->assign('eap_adressvalidation_descriptiontext',$EAPBonigateway->sprachArr['eap_adressvalidation_descriptiontext']);

        $settings = $EAPBonigateway->settingsArray;

        header('Content-Type: application/json');
        $sessionhandle = null;
        $submit_address = $settings["jtl_eap_postdirekt_personaldata"] == 1 ? true : false;

        if ($settings["jtl_eap_postdirekt"] == 1 && $sessionhandle != $inputHandle && $settings != null) {
            try {
                $actionRequired = false;
                $responseBilling = array();
                $responseShipping = array();
                $requested = true;

                $soapclient = new SoapClient("https://api.eaponline.de/bonigateway.php?wsdl", array("trace" => 1, "encoding" => "utf-8"));
                if (count($inputParams["billing"]) == 6) {

                    if ($inputParams["billing"]["country"] == "DE") {
                        $responseBilling = $soapclient->getPostdirekt($settings["jtl_eap_userid"], md5($settings["jtl_eap_passwort"]), $inputParams["billing"]["firstname"], $inputParams["billing"]["lastname"], $inputParams["billing"]["city"], $inputParams["billing"]["street"], $inputParams["billing"]["zipcode"]);

                        $responseBillingData = [
                            "firstname" => $submit_address ? $responseBilling->Vorname : $inputParams["billing"]["firstname"],
                            "lastname" => $submit_address ? $responseBilling->Nachname : $inputParams["billing"]["lastname"],
                            "street" => str_replace($responseBilling->hausnummer,"",$responseBilling->Strasse),
                            "city" => $responseBilling->Ort,
                            "zipcode" => $responseBilling->PLZ,
                            "streetnumber" => $responseBilling->hausnummer,
                            "requestID" => $responseBilling->nachbehandlungID,
                            "correctionRequired" => !$responseBilling->secure_payment && $responseBilling->nachbehandlungID > 0 ? md5($inputParams["billing"]["city"] . $inputParams["billing"]["street"] . $inputParams["billing"]["zipcode"]) != md5($responseBilling->Ort . $responseBilling->Strasse . $responseBilling->PLZ) ? true : false : false,
                        ];
                    }
                }
                if (count($inputParams["shipping"]) == 7 && $inputParams["shipping"]["useShippingAdress"] == true) {
                    if ($inputParams["shipping"]["country"] == "DE") {
                        $responseShipping = $soapclient->getPostdirekt($settings["jtl_eap_userid"], md5($settings["jtl_eap_passwort"]), $inputParams["shipping"]["firstname"], $inputParams["shipping"]["lastname"], $inputParams["shipping"]["city"], $inputParams["shipping"]["street"], $inputParams["shipping"]["zipcode"]);
                        $responseShippingData = [
                            "firstname" => $submit_address ? $responseShipping->Vorname : $inputParams["shipping"]["firstname"],
                            "lastname" => $submit_address ? $responseShipping->Nachname : $inputParams["shipping"]["lastname"],
                            "street" => str_replace($responseShipping->hausnummer,"",$responseShipping->Strasse),
                            "streetnumber" => $responseShipping->hausnummer,
                            "city" => $responseShipping->Ort,
                            "zipcode" => $responseShipping->PLZ,
                            "requestID" => $responseShipping->nachbehandlungID,
                            "correctionRequired" => !$responseShipping->secure_payment && $responseShipping->nachbehandlungID > 0 ? md5($inputParams["shipping"]["city"] . $inputParams["shipping"]["street"] . $inputParams["shipping"]["zipcode"]) != md5($responseShipping->Ort . $responseShipping->Strasse . $responseShipping->PLZ) ? true : false : false,
                        ];
                    }
                }

            } catch (Exception $e) {

            }

            $responseData = [
                "request" => $inputParams,
                "billing" => $responseBillingData,
                "shipping" => $responseShippingData,
                "requested" => $requested
            ];
        } else {
            // IF NO DATA HAS CHANGED RETURN SESSION STORED VALUE;
            $responseData = $sessionvalue;
        }


    $actionRequired = false;
    $_SESSION['eap_adressvalidation']= $responseData;
    $smarty->assign('requestparams',$inputParams);

    $smarty->assign('responseparams',$responseData);



 //  echo  $this->pluginSettings->cFrontendPfad."/tpl/adressvalidation.tpl";

    $parsedTpl =  $smarty->fetch($EAPBonigateway->pluginSettings->cFrontendPfad."/tpl/adressvalidation.tpl");
    //$parsedTpl.=print_r($responseData,true);
    //$parsedTpl = print_r($inputParams,true);


    $array = [
        "actionRequired" => $responseData["billing"]["correctionRequired"] == true || $responseData["shipping"]["correctionRequired"] == true ? true : true,
        "htmlModal" => $parsedTpl,
    ];

    echo json_encode($array);
    exit;

    }




