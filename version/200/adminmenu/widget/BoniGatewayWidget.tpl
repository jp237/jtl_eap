<div class="widget-custom-data">
  <table width="100%" class="table">
  <tr><td style="background-color:#{$state_color}" colspan="3">{$state_text}</td></tr>
	  <tr>
			<td width="7%">&nbsp;</td>
			<td width="55%"><strong>Heute</strong></td>
			<td width="38%"><strong>Monat</strong></td>
		</tr>
		<tr>
			<td>OK</td>
			<td>{$heute->ok}</td>
			<td>{$monat->ok}</td>
		</tr>
		<tr>
			<td>Nicht OK</td>
			<td>{$heute->nichtok}</td>
			<td>{$monat->nichtok}</td>
		</tr>
		<tr>
			<td>Abgebrochen</td>
			<td>{$heute->abbruch}</td>
			<td>{$monat->abbruch}</td>
		</tr>
		<tr>
		  <td>Gesamt</td>
		  <td><strong>{$heute->gesamt}</strong></td>
		  <td><strong>{$monat->gesamt}</strong></td>
    </tr>
		<tr>
		  <td colspan="3">Detailiertere Statistiken finden Sie im <a target="_new" href='https://gateway.eaponline.de'>EAP-BoniGateway</a> </td>
		
    </tr>
	</table>
</div>