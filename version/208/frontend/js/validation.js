function validategeb(requestTel,requestGeb,form)
{
		var fehler = "";
		var dataerror = false;

		if(requestGeb)
		{
			
			var geb = document.getElementsByClassName('eap_geburtstag')[0].value
				if(geb.length<10) 
			{
				document.getElementById('geb_error').style.display = "table-row";	
				dataerror = true;
			}
			else
			{
				document.getElementById('geb_error').style.display = "none";
				document.getElementsByName('eap_hidden_geb')[0].value = geb; 
			}
		}
		if(requestTel)
		{
			var telnr = document.getElementsByClassName('eap_telefon')[0].value
				if(telnr.length<5)
			{
				document.getElementById('tel_error').style.display = "table-row";	
				dataerror = true;
			}
			else
			{
			document.getElementById('tel_error').style.display = "none";
			document.getElementsByName('eap_hidden_tel')[0].value = telnr; 
			}
		}
		

		if(!dataerror)
	{
		//document.zahlung.submit();
		 parent.jQuery.fancybox.close()
		
		 $("#"+form).submit();
	}
}


function checkCompanyOrder()
{
	try
	{
		if($("select[name='companyList']").val().length>0)
		{
			
			$("input[name='eap_geburtstag']").prop("disabled",true);
			$("input[name='eap_geburtstag']").prop("required",false);
			if($("select[name='companyList']").val()=="Einzelunternehmen" || $("select[name='companyList']").val()=="Privatkunde" || $("select[name='companyList']").val()=="GBR"|| $("select[name='companyList']").val()=="EK")
			{
				$("input[name='eap_geburtstag']").prop("disabled",false);
				$("input[name='eap_geburtstag']").prop("required",true);
				$(".EAPhideDateOfBirth").css("display","table-row");
			}else
			{
				$(".EAPhideDateOfBirth").css("display","none");
			}
		}
	}catch(Exception)
	{
	}
	
	
}
function getValidation(form)
{
	var geb = $("input[name='eap_geburtstag']");
	var geb_valid = Date.parse(geb.val());

	var tel = $("input[name='eap_telefon']");
	var company = $("select[name='companyList']");
	var opt_in = $("input[name='eap_optin']");

	if(geb.length>0)
	{
		if(geb.prop("disabled")==false && geb.val().length<10)
		{
			document.getElementById('geb_error').style.display = "table-row";
			return;
		}
		else
		{ 
			$(form+" #eap_hidden_geb").val(geb.val());
			document.getElementById('geb_error').style.display = "none";	
		}
	}
	
	
	if(tel.length>0)
	{
		if(tel.prop("disabled")==false && tel.val().length<5)
		{
			document.getElementById('tel_error').style.display = "table-row";
			return;
		}
		else {
			document.getElementById('tel_error').style.display = "none";	
			$(form+" #eap_hidden_tel").val(tel.val()); 
		}
	}
	
	if(company.length>0)
	{	
			if(company.prop("disabled")==false && company.val()=="select")
		{
			document.getElementById('company_error').style.display = "table-row";
			return;
		}
		else { 
			document.getElementById('company_error').style.display = "none";
			$(form+" #eap_hidden_company").val(company.val());
		}
	}
	if(opt_in.prop("disabled")==false){
		if(opt_in.prop("checked")){
			document.getElementById('optin_error').style.display = "none";
			$(form+" #eap_hidden_optin").val('1');
		}else{
			document.getElementById('optin_error').style.display = "table-row";
			return;
		}
	}

	$(form).submit();
}
$(document).ready(function() {
	checkCompanyOrder();
	$( "select[name='companyList']" ).change(function(e) {
		 checkCompanyOrder();
	});
});

function redirectAdresse()
{
	window.location.href = 'bestellvorgang.php?editRechnungsadresse=1';
	
}