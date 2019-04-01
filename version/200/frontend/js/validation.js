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

function redirectAdresse()
{
	window.location.href = 'bestellvorgang.php?editRechnungsadresse=1';
	
}