<!DOCTYPE html>
<html>
	<head>
		<style type="text/css">
			* {
				font-family: sans-serif;
			}
			@page {
				margin: 160px 50px;
			}
			header {
				border-bottom: 1px solid #404040;
				font-size: 10px;
				position: fixed;
				left: 0px;
				top: -160px;
				right: 0px;
				height: 60px;
				text-align: left;
			}
			header p{
				margin: 10px 0;
			}
			table,table td{
				border:1px solid #d0d0d0;
				border-spacing: 0;
				border-collapse: collapse;
				padding: 4px;
			}
			table p {
				margin: 0;
			}
			#content{
				font-size: 11px;
				margin-top: -100px;
				position: absolute;
			}
			table>thead>tr>th{
				background-color: #e8e8e8;
			}
		</style>
	</head>
	<body>
		<header>
			<img src="{{ asset('images/ustar_logo.png') }}" style="height:32px;margin-top:5px;">
			<p style="font-size:8px;color:#a0a0a0;margin:0;">{{ $datos->direccion }}<br>Tlfs.: {{ $datos->tlf1 }}, {{ $datos->tlf2 }}</p>
		</header>
		<div id="content">
			<table style="border:none;width:100%;">
				<tr>
					<th width="85%" style="vertical-align:middle;">
						<p style="text-align:center;margin-bottom:5px;font-size:16px;vertical-align:middle;"><b>MANIFIESTO DE CARGA</b></p>
					</th>
					<th width="15%" style="background-color:#d0d0d0;border:1px solid #a0a0a0;text-align:center;">
						<b style="font-size:12px;">N° <b style="font-size:16px;">{{ $cabecera->NroManifiesto }}</b></b>
					</th>
				</tr>
			</table>
			<table style="width:100%;">
				<tr>
					<td width="15%">Despacho:</td>
					<td width="85%" colspan="3">
						<p>{{ $cabecera->DtmManifiesto }}</p>
					</td>
				</tr>
				<tr>
					<td style="text-align:left;vertical-align:top;background-color:#d0d0d0;" colspan="4">
						<p style="text-align:center;font-size:12px;font-weight:bold;">INFORMACION SOBRE EL ORIGEN</p>
					</td>
				</tr>
				<tr>
					<td width="10%">Agencia:</td>
					<td width="40%">
						<p>{{ $cabecera->origen }}</p>
					</td>
					<td width="10%">Despachador:</td>
					<td width="40%">
						<p>{{ $cabecera->NomDespacha }}</p>
					</td>
				</tr>
				<tr>
					<td style="text-align:left;vertical-align:top;background-color:#d0d0d0;" colspan="4">
						<p style="text-align:center;font-size:12px;font-weight:bold;">INFORMACION SOBRE EL DESTINO</p>
					</td>
				</tr>
				<tr>
					<td width="10%">Agencia:</td>
					<td width="40%">
						<p>{{ $cabecera->destino }}</p>
					</td>
					<td width="10%">Agente:</td>
					<td width="40%">
						<p>{{ $cabecera->NomPersonal }}</p>
					</td>
				</tr>
			</table>
			<table style="margin-top:1px;width:98%;">
				<thead>
					<tr>
						<th width="10%">Guia/Ing</th>
						<th width="5%">Remito</th>
						<th width="8%">Fecha</th>
						<th width="27%">Cliente</th>
						<th width="15%">Servicio</th>
						<th width="20%">Contenido</th>
						<th width="5%">Cantidad</th>
						<th width="10%">Peso</th>
					</tr>
				</thead>
				<tbody>
					@foreach($detalle as $idx => $fila)
					<tr>
						<td>{{ $fila->CodGuia }}</td>
						<td>{{ $idx + 1 }}</td>
						<td>{{ $fila->DtmGuia }}</td>
						<td>{{ $fila->AbrAreaCliente }}</td>
						<td>{{ $fila->AbrServicio }}</td>
						<td>{{ $fila->AbrTipoEnvio }}</td>
						<td style="text-align:right;">{{ $fila->CanEnvios }}</td>
						<td style="text-align:right;">{{ number_format($fila->CanPeso,2) }}</td>
					</tr>
					@endforeach
				</tbody>
			</table>
			<table style="border:none;width:100%;">
				<tr>
					<td width="10%" style="border:none;">Observaciones</td>
					<td width="90%" style="border:none;border-bottom:1px solid #808080;"></td>
				</tr>
			</table>
			<table style="border:none;width:100%;margin-top:40px;">
				<tr>
					<td width="75%" style="border:none;"></td>
					<td width="25%" style="border:none;border-bottom:1px solid #808080;"></td>
				</tr>
				<tr>
					<td style="border:none;"></td>
					<td style="border:none;text-align:center;">Firma del despachador</td>
				</tr>
			</table>
		</div>
	</body>
</html>