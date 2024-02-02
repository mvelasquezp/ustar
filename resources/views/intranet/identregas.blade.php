<!DOCTYPE html>
<html>
	<head>
		<title>Indicadores | Distribución | Entregas</title>
		@include("common.styles")
		<link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap-select.css') }}">
		<style type="text/css">
			#chart-container{display:none}
			.div-chart{height:400px;width:100%}
			hr{margin:10px 0}
	        .tag{border-radius:4px;display:inline-block;height:24px;width:48px}
	        .tag-rojo{background-color:#e53935}
	        .tag-amarillo{background-color:#fbc02d}
	        .tag-verde{background-color:#4caf50}
	        #modal-extra-container{height:480px;width:100%}
		</style>
	</head>
	<body>
		@include("common.navbar")
		<!-- PAGINA -->
		<div class="container">
			<div class="row">
				<div class="col">
					<div class="form-container alert alert-secondary" role="alert">
						<form id="form-filtro" method="post" class="form-inline">
							<label class="form-control-sm" for="trg-ciclo">
								Ciclo&nbsp;
								<!--input type="text" id="trg-ciclo" class="form-control form-control-sm" placeholder="Seleccione" style="width:12em;"-->
								<select id="trg-ciclo" class="selectpicker" multiple data-live-search="true">
									<option value="0">Todos</option>
									@foreach($ciclos as $idx => $ciclo)
									<option value="{{ $ciclo->ciclo }}">{{ $ciclo->ciclo }}</option>
									@endforeach
								</select>
							</label>
							<!-- -->
							@if(strcmp($usuario->tp_cliente,'admin') == 0)
							<label class="form-control-sm" for="trg-oficina">
								Oficina&nbsp;
								<!--input type="text" id="trg-oficina" class="form-control form-control-sm" placeholder="Seleccione" style="width:12em;"-->
								<select id="trg-oficina" class="selectpicker" multiple data-live-search="true">
									@foreach($ofcs as $idx => $oficina)
									<option value="{{ $oficina->codigo }}">{{ $oficina->descripcion }}</option>
									@endforeach
								</select>
							</label>
							@else
							<input type="hidden" id="trg-oficina" class="ch-of" value="{{ $ofcs[0]->codigo }}">
							@endif
							<!-- -->
							<label class="form-control-sm" for="trg-producto">
								Producto&nbsp;
								<!--input type="text" id="trg-producto" class="form-control form-control-sm" placeholder="Seleccione" style="width:12em;"-->
								<select id="trg-producto" class="selectpicker" multiple data-live-search="true">
									@foreach($prds as $idx => $producto)
									<option value="{{ $producto->codigo }}">{{ $producto->descripcion }}</option>
									@endforeach
								</select>
							</label>
							<!-- -->
							<label class="form-control-sm" for="nrdoc">
								Documento
								<input type="text" id="nrdoc" class="form-control form-control-sm ml-2" placeholder="Seleccione" style="width:6em;">
							</label>
							<!-- -->
							<div class="form-check-inline">
								<label class="form-check-label" for="tplocal">
									&nbsp;&nbsp;&nbsp;<input class="form-check-input" type="checkbox" id="tplocal" value="S" checked="checked">
									&nbsp;Local
								</label>
							</div>
							<!-- -->
							<div class="form-check-inline">
								<label class="form-check-label" for="tpnacional">
									&nbsp;<input class="form-check-input" type="checkbox" id="tpnacional" value="S" checked="checked">
									&nbsp;Nacional
								</label>
							</div>
							<!-- -->
							<div class="form-check-inline">
								<label class="form-check-label" for="tpinternacional">
									&nbsp;<input class="form-check-input" type="checkbox" id="tpinternacional" value="S" checked="checked">
									&nbsp;Internac.
								</label>
							</div>
							<!-- -->
							<button id="btn-form" type="button" class="btn btn-success btn-sm"><i class="fa fa-search"></i> Buscar</button>
						</form>
					</div>
				</div>
			</div>
		</div>
		<!-- modal - ciclo -->
		<div id="modal-oficina" class="modal fade" tabindex="-1" role="dialog">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title text-primary">Seleccione oficinas</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<ul class="list-group">
							@foreach($ofcs as $idx => $oficina)
							@if($idx == 0)
							<label class="list-group-item d-flex justify-content-between align-items-center active">
								{{ $oficina->descripcion }}
								<span><input id="ch-of-all" type="checkbox" value="{{ $oficina->codigo }}" checked="checked"></span>
							</label>
							@else
							<label class="list-group-item d-flex justify-content-between align-items-center">
								{{ $oficina->descripcion }}
								<span><input id="ch-of-{{ $idx }}" class="ch-of" type="checkbox" value="{{ $oficina->codigo }}" data-label="{{ $oficina->descripcion }}" checked="checked"></span>
							</label>
							@endif
							@endforeach
						</ul>
					</div>
				</div>
			</div>
		</div>
		<div id="chart-container" class="container">
			<div class="row">
				<!--div class="col-4" style="max-height:840px;overflow-y:auto">
					<table class="table table-striped table-sm">
						<thead>
							<tr>
								<th>Estado</th>
								<th>Motivo</th>
								<th>DíaVisita</th>
								<th>Cantidad</th>
							</tr>
						</thead>
						<tbody id="chart-tbody-1"></tbody>
					</table>
				</div>
				<div class="col-8"-->
				<div class="col-12">
					<div class="row">
						<div class="col">
							<div id="chart-1" class="div-chart"></div>
						</div>
					</div>
					<div class="row"><div class="col"><hr></div></div>
					<div class="row">
						<div class="col">
							<div id="chart-2" class="div-chart"></div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col">
					<hr>
					<div id="chart-3" style="height:960px"></div>
				</div>
			</div>
			<container id="map-container" style="width:100%">
		        <div class="row mt-2">
		            <div class="col-xs-8 col-xs-offset-2">
		                <h4 class="text-dark">Entregas por distrito</h4><br>
		            </div>
		        </div>
				<div class="row">
					<div class="col-4">
						<p>Elija un distrito del mapa para ver el detalle</p>
	                    <h3 class="text-warning">Leyenda</h3>
	                    <table class="table">
	                        <tbody>
	                            <tr>
	                                <td>Efectividad de entrega &gt;= 98%</td>
	                                <td><span class="tag tag-verde"></span></td>
	                            </tr>
	                            <tr>
	                                <td>96% &lt;= Efectividad de entrega &lt; 98%</td>
	                                <td><span class="tag tag-amarillo"></span></td>
	                            </tr>
	                            <tr>
	                                <td>Efectividad de entrega &lt; 96%</td>
	                                <td><span class="tag tag-rojo"></span></td>
	                            </tr>
	                        </tbody>
	                    </table>
					</div>
					<div class="col-8">
						<div id="map-container" style="width:100%;height:640px">
							<div id="map-canvas" style="width:100%;height:640px"></div>
						</div>
					</div>
				</div>
			</container>
		</div>
		<!-- loader de búsqueda -->
		<div id="loader-busqueda">
			<div>
				<img src="{{ asset('images/icons/buscando.svg') }}">
				<p>Cargando datos. Por favor, espere...</p>
			</div>
		</div>
		@if(strcmp($usuario->tp_cliente,'admin') == 0)
		<!-- modal - seleccion de oficina -->
		<div id="modal-oficina" class="modal fade" tabindex="-1" role="dialog">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title text-primary">Seleccione oficinas</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<ul class="list-group">
							@foreach($ofcs as $idx => $oficina)
							@if($idx == 0)
							<label class="list-group-item d-flex justify-content-between align-items-center active">
								{{ $oficina->descripcion }}
								<span><input id="ch-of-all" type="checkbox" value="{{ $oficina->codigo }}" checked="checked"></span>
							</label>
							@else
							<label class="list-group-item d-flex justify-content-between align-items-center">
								{{ $oficina->descripcion }}
								<span><input id="ch-of-{{ $idx }}" class="ch-of" type="checkbox" value="{{ $oficina->codigo }}" data-label="{{ $oficina->descripcion }}" checked="checked"></span>
							</label>
							@endif
							@endforeach
						</ul>
					</div>
				</div>
			</div>
		</div>
		@endif
		<!-- modal - productos -->
		<div id="modal-producto" class="modal fade" tabindex="-1" role="dialog">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title text-primary">Seleccione productos</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<ul class="list-group">
							@foreach($prds as $idx => $producto)
							@if($idx == 0)
							<label class="list-group-item d-flex justify-content-between align-items-center active">
								{{ $producto->descripcion }}
								<span><input id="ch-pr-all" type="checkbox" value="{{ $producto->codigo }}" checked="checked"></span>
							</label>
							@else
							<label class="list-group-item d-flex justify-content-between align-items-center">
								{{ $producto->descripcion }}
								<span><input id="ch-pr-{{ $idx }}" class="ch-pr" type="checkbox" value="{{ $producto->codigo }}" data-label="{{ $producto->descripcion }}" checked="checked"></span>
							</label>
							@endif
							@endforeach
						</ul>
					</div>
				</div>
			</div>
		</div>
		<!-- modal - ciclos -->
		<div id="modal-ciclo" class="modal fade" tabindex="-1" role="dialog">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title text-primary">Seleccione ciclos</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<ul class="list-group">
							@foreach($ciclos as $idx => $ciclo)
							<label class="list-group-item d-flex justify-content-between align-items-center">
								{{ $ciclo->ciclo }}
								<span><input id="ch-cl-{{ $idx }}" class="ch-cl" type="checkbox" value="{{ $ciclo->ciclo }}" data-label="{{ $ciclo->ciclo }}"></span>
							</label>
							@endforeach
						</ul>
					</div>
				</div>
			</div>
		</div>
	    <!--End-->
	    <div class="modal fade" id="modal-info" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	        <div class="modal-dialog" role="document">
	            <div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title text-primary" id="modal-info-label">Mostrando detalles</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
	                <div class="modal-body">
	                    <div class="row">
	                        <div class="col-lg-8 col-lg-offset-2" id="modal-info-container">
	                            <p>Cargando datos. Por favor, espere...</p>
	                        </div>
	                    </div>
	                </div>
	                <div class="modal-footer">
	                    <button type="button" class="btn btn-search" data-dismiss="modal">Cerrar</button>
	                </div>
	            </div>
	        </div>
	    </div>
	    <!-- -->
	    <div class="modal fade" id="modal-extra" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	        <div class="modal-dialog" role="document">
	            <div class="modal-content">
	                <div class="modal-header">
	                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	                    <h4 class="modal-title" id="modal-extra-label">Mostrando detalles</h4>
	                </div>
	                <div class="modal-body">
	                    <div class="row">
	                        <div class="col-lg-12">
	                            <input type="hidden" id="me-dia">
	                            <input type="hidden" id="me-estado">
	                            <div id="modal-extra-container">
	                                <p>Cargando datos. Por favor, espere...</p>
	                            </div>
	                        </div>
	                    </div>
	                </div>
	                <div class="modal-footer">
	                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
	                </div>
	            </div>
	        </div>
	    </div>
		<!-- JS -->
		@include("common.scripts")
		<script type="text/javascript" src="{{ asset('js/highcharts.js') }}"></script>
		<script type="text/javascript" src="{{ asset('js/modules/drilldown.js') }}"></script>
    	<script src="{{ asset('js/distritos.js') }}"></script>
		<script type="text/javascript">
			var data, data1, data2, data3, arr_ciclos = ["Todos"], arr_ofcs = ["Todos"], arr_prds = ["Todos"], arr_distritos, polygons = [];
	        var styledMapType, map;
	        var settings = {
	            colors: {
	                red:"#e53935",
	                yellow:"#fbc02d",
	                green:"#4caf50"
	            },
	            stroke: 0.8,
	            fill: 0.4,
	            weight: 1
	        };
			//
			$("#btn-form").on("click", function(e) {
				$("#chart-container").fadeOut(150);
				e.preventDefault();
				remove_polygons();
				$("#loader-busqueda").fadeIn(150);
				var p = {
					_token: "{{ csrf_token() }}",
					ccl: arr_ciclos,
					ofc: arr_ofcs,
					prd: arr_prds,
					loc: document.getElementById("tplocal").checked ? 'S' : 'N',
					nac: document.getElementById("tpnacional").checked ? 'S' : 'N',
					int: document.getElementById("tpinternacional").checked ? 'S' : 'N',
					doc: document.getElementById("nrdoc").value
				};
				$("#btn-form").hide();
				$.post("{{ url('indicadores/ajax/buscar') }}", p, function(response) {
					if(response.state == "success") {
						$("#chart-container").fadeIn(150);
						data1 = response.data.data1;
						data2 = response.data.data2;
						data3 = response.data.data3;
						//mapas
	                    arr_distritos = response.data.datamap;
						//colores
						var colors = ["#4caf50","#1976d2","#ff9800","#d32f2f","#00bcd4","#fdd835","#512da8","#8bc34a","#607d8b","#ffc107","#3f51b5","#cddc39","#ff5722","#6a1b9a","#039be5","#009688","#795548","#c2185b","#9e9e9e"];
						//dias visita
						var dias = [];
						for(var x in data2) {
							var fila = data2[x];
							var x_dia = parseInt(fila.diavisita);
							if(dias.indexOf(x_dia) == -1) dias.push(x_dia);
						}
						dias.sort(function (a, b) {  return a - b;  });
						//grafico 1
						var ds1 = [];
						var dt1 = [];
						var id_idx = 0;
						var curr_estado = "";
						var curr_motivo = "";
						var curr_arr = [];
						var curr_tot = 0;
						var curr_global = 0;
						for(var i in data1) {
							var dato = data1[i];
							//grafico 1
							if(dato.motivo != curr_motivo) {
								if(i > 0) {
									curr_arr.push([curr_motivo, curr_tot]);
								}
								curr_tot = 0;
								curr_motivo = dato.motivo;
								if(dato.estado != curr_estado) {
									ds1.push({
										name: curr_estado,
										id: "it-" + id_idx,
										data: curr_arr
									});
									dt1.push({
										name: curr_estado,
										y: curr_global,
										drilldown: "it-" + id_idx,
										color: colors[id_idx]
									});
									curr_estado = dato.estado;
									id_idx++;
									curr_arr = [];
									curr_global = 0;
								}
							}
							curr_tot += dato.cant;
							curr_global += dato.cant;
							//insertar fila en la tabla
							$("#chart-tbody-1").append(
								$("<tr/>").append(
									$("<td/>").append(
										$("<a/>").attr("href","#").addClass("btn btn-xs text-light").css("background-color",colors[id_idx]).html(dato.estado)
									)
								).append(
									$("<td/>").html(dato.motivo)
								).append(
									$("<td/>").addClass("text-right").html(dato.diavisita)
								).append(
									$("<td/>").addClass("text-right").html(dato.cant)
								)
							);
						}
						curr_arr.push([curr_motivo, curr_tot]);
						ds1.push({
							name: curr_estado,
							id: "it-" + id_idx,
							data: curr_arr
						});
						ds1.shift();
						dt1.push({
							name: curr_estado,
							y: curr_global,
							drilldown: "it-" + id_idx,
							color: colors[id_idx]
						});
						dt1.shift();
						Highcharts.chart("chart-1", {
							chart: { type: 'pie' },
						    title: { text: 'Efectividad de entregas' },
						    subtitle: { text: 'Click en el gráfico para mostrar detalles.' },
						    plotOptions: {
			                    series: {
			                        dataLabels: {
			                            enabled: true,
			                            format: '{point.name}: {point.y} [{point.percentage:.2f}%]'
			                        }
			                    },
						    	pie: {
						            allowPointSelect: true,
						            cursor: 'pointer',
						            dataLabels: { enabled: false },
						            showInLegend: true
						        }
						    },
						    tooltip: {
						        headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
						        pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y} [{point.percentage:.2f}%]</b> del total<br/>'
						    },
						    series: [{
						        name: 'Estados',
						        colorByPoint: true,
						        data: dt1
						    }],
						    drilldown: {
						    	series: ds1
						    }
						});
						//grafico 2
						var id_id2 = 0;
						var ds2 = [];
						var j_estado = "";
						var ndias = dias.length;
						var j_data = new Array();
						for(var j = 0; j < ndias; j++) j_data[j] = 0;
						for(var j in data2) {
							var fila = data2[j];
							if(j_estado != fila.estado) {
								var j_row = {
									name: j_estado,
									data: j_data,
									color: colors[id_id2]
								};
								ds2.push(j_row);
								j_estado = fila.estado;
								j_data = new Array();
								id_id2++;
								for(var j = 0; j < ndias; j++) j_data[j] = 0;
							}
							j_data[dias.indexOf(parseInt(fila.diavisita))] += fila.cant;
						}
						ds2.push({
							name: j_estado,
							data: j_data,
							color: colors[id_id2]
						});
						ds2.shift();
						Highcharts.chart("chart-2", {
						    chart: { type: 'column' },
						    title: { text: 'Entregas por estado y día de entrega' },
						    xAxis: {
						    	categories: dias,
						    	title: "Día de Visita"
						    },
						    yAxis: {
						        min: 0,
						        title: { text: 'Cantidad' },
						        stackLabels: {
						            enabled: true,
						            style: { fontWeight: 'bold', color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray' }
						        }
						    },
						    legend: {
						        align: 'center',
						        x: -30,
						        verticalAlign: 'bottom',
						        y: 25,
						        floating: true,
						        backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || 'white',
						        borderColor: '#CCC',
						        borderWidth: 1,
						        shadow: false
						    },
						    tooltip: {
						        headerFormat: '<span style="font-size:10px">{point.x}</span><table>',
			                    pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
			                        '<td style="padding:0"><b>{point.y} unids</b> [{point.percentage:.2f}%]</td></tr>',
			                    footerFormat: '</table>',
			                    shared: true,
			                    useHTML: true
						    },
						    plotOptions: {
						        column: {
						            stacking: 'normal'
						        }
						    },
						    series: ds2
						});
						//grafico 3
						var ciudades = [];
						var id_id3 = 0;
						var ds3 = [];
						var k_estado = "";
						for(var x in data3) {
							var xfila = data3[x];
							var x_ciudad = xfila.ciudad;
							if(ciudades.indexOf(x_ciudad) == -1) ciudades.push(x_ciudad);
						}
						ciudades.sort(function (a, b) {  return a - b;  });
						var nciudades = ciudades.length;
						var k_data = new Array();
						for(var j = 0; j < nciudades; j++) k_data[j] = 0;
						for(var j in data3) {
							var kfila = data3[j];
							if(k_estado != kfila.estado) {
								var k_row = {
									name: k_estado,
									data: k_data,
									color: colors[id_id3]
								};
								ds3.push(k_row);
								k_estado = kfila.estado;
								k_data = new Array();
								id_id3++;
								for(var j = 0; j < nciudades; j++) k_data[j] = 0;
							}
							k_data[ciudades.indexOf(kfila.ciudad)] += kfila.cant;
						}
						ds3.push({
							name: k_estado,
							data: k_data,
							color: colors[id_id3]
						});
						ds3.shift();
						Highcharts.chart("chart-3", {
						    chart: { type: 'bar' },
						    title: { text: 'Entregas por estado y ciudad' },
			                tooltip: {
			                    headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
			                    pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
			                        '<td style="padding:0"><b>{point.y} unids</b> [{point.percentage:.2f}%]</td></tr>',
			                    footerFormat: '</table>',
			                    shared: true,
			                    useHTML: true
			                },
						    xAxis: { categories: ciudades },
						    yAxis: {
						        min: 0,
						        title: { text: 'Total de entregas' }
						    },
						    legend: { reversed: true },
						    plotOptions: { series: { stacking: 'normal' } },
						    series: ds3
						});
						//dibujar el mapa
						if(p.loc == "S") {
							var idx = 0;
				            for(var i in arr_distritos) {
				                var idistrito = arr_distritos[i];
				                if(idistrito.puntos.length > 0) {
				                    var shapeColor = settings.colors.green;
				                    if(idistrito.perc < 98) shapeColor = settings.colors.yellow;
				                    if(idistrito.perc < 96) shapeColor = settings.colors.red;
				                    var shape = new google.maps.Polygon({
				                        paths: idistrito.puntos,
				                        strokeColor: shapeColor,
				                        strokeOpacity: settings.stroke,
				                        strokeWeight: settings.weight,
				                        fillColor: shapeColor,
				                        fillOpacity: settings.fill,
				                        distrito: {codigo:idistrito.codigo,nombre:idistrito.nombre}
				                    });
				                    polygons[idx] = shape;
				                    polygons[idx].setMap(map);
				                    //inserta listener para los poligonos
				                    polygons[idx].addListener("click", function(event) {
				                        var punto = $(this)[0];
				                        var distrito = punto.distrito;
				                        var p = {
											_token: "{{ csrf_token() }}",
											ccl: arr_ciclos,
											ofc: arr_ofcs,
											prd: arr_prds,
											loc: document.getElementById("tplocal").checked ? 'S' : 'N',
											nac: document.getElementById("tpnacional").checked ? 'S' : 'N',
											int: document.getElementById("tpinternacional").checked ? 'S' : 'N',
											doc: document.getElementById("nrdoc").value,
				                            dst: distrito.codigo
				                        };
				                        $("#modal-info-container").empty().append(
				                            $("<p/>").html("Cargando datos. Por favor, espere...")
				                        );
				                        $("#modal-info-label").html("Distrito de " + distrito.nombre.toUpperCase());
				                        $("#modal-info").modal("show");
				                        $.post("{{ url('indicadores/ajax/if-entrega-distrito') }}", p, function(response) {
				                            if(response.success) {
				                                var tbody = $("<tbody/>");
				                                var data = response.data;
				                                for(var i in data) {
				                                    var restado = data[i];
				                                    tbody.append(
				                                        $("<tr/>").addClass("bg-light").append(
				                                            $("<th/>").attr("colspan",2).html(restado.estado)
				                                        ).append(
				                                            $("<th/>").addClass("text-right").html(restado.cantidad)
				                                        ).append(
				                                            $("<th/>").addClass("text-right").html((100 * restado.cantidad / response.todo).toFixed(2) + "%")
				                                        )
				                                    );
				                                    var motivos = restado.motivos;
				                                    for(var j in motivos) {
				                                        var rmotivo = motivos[j];
				                                        tbody.append(
				                                            $("<tr/>").append(
				                                                $("<td/>").attr("width","10%").html("")
				                                            ).append(
				                                                $("<td/>").attr("width","50%").html(rmotivo.motivo)
				                                            ).append(
				                                                $("<td/>").addClass("text-right").html(rmotivo.cantidad)
				                                            ).append(
				                                                $("<td/>").addClass("text-right").html((100 * rmotivo.cantidad / restado.cantidad).toFixed(2) + "%")
				                                            )
				                                        );
				                                    }
				                                }
				                                $("#modal-info-container").empty().append(
				                                    $("<table/>").addClass("table").append(
				                                        $("<thead/>").append(
				                                            $("<tr/>").append(
				                                                $("<th/>").attr("colspan",2).html("Estado")
				                                            ).append(
				                                                $("<th/>").attr("width","20%").html("Cantidad")
				                                            ).append(
				                                                $("<th/>").attr("width","20%").html("Porcentaje")
				                                            )
				                                        )
				                                    ).append(tbody)
				                                );
				                            }
				                        }, "json");
				                    });
				                }
				                idx++;
				            }
				            //console.log("show #map-container");
				            $("#map-container").show();
						}
						else $("#map-container").hide();
					}
					else alert(response.msg);
					$("#loader-busqueda").fadeOut(150);
					$("#btn-form").show();
				}, "json").fail(function(err) {
					$("#loader-busqueda").fadeOut(150);
					$("#btn-form").show();
				});
			});
			//
	        function remove_polygons() {
	        	if(polygons) {
	        		var tam = polygons.length;
		            for(var i in polygons) {
		                polygons[i].setMap(null);
		            }
		            polygons = [];
	        	}
	        }
	        function initMap() {
	            styledMapType = new google.maps.StyledMapType([
	                {"featureType": "administrative","elementType": "geometry","stylers": [{"visibility": "off"}]},
	                {"featureType": "administrative.land_parcel","stylers": [{"visibility": "off"}]},
	                {"featureType": "administrative.neighborhood","stylers": [{"visibility": "off"}]},
	                {"featureType": "poi","stylers": [{"visibility": "off"}]},
	                {"featureType": "road","stylers": [{"visibility": "off"}]},
	                {"featureType": "road","elementType": "labels","stylers": [{"visibility": "off"}]},
	                {"featureType": "road","elementType": "labels.icon","stylers": [{"visibility": "off"}]},
	                {"featureType": "transit","stylers": [{"visibility": "off"}]},
	                {"featureType": "water","elementType": "labels.text","stylers": [{"visibility": "off"}]}
	            ]);
	            map = new google.maps.Map(document.getElementById("map-canvas"), {
	                center: {lat: -12.091967, lng: -77.027396},
	                zoom: 12,
	                mapTypeControlOptions: {
	                    mapTypeIds: ["roadmap", "satellite", "hybrid", "terrain", "styled_map"]
	                }
	            });
	            map.mapTypes.set("styled_map", styledMapType);
	            map.setMapTypeId("styled_map");
	        }
			$("#trg-ciclo").on("changed.bs.select", function (e, clickedIndex, isSelected, previousValue) {
				var select = $(this);
				arr_ciclos = $(this).val();
				if(clickedIndex == 0) {
					if(isSelected) {
						select.selectpicker("selectAll");
						arr_ciclos = ["Todos"];
					}
					else select.selectpicker("deselectAll");
				}
				console.log(arr_ciclos);
			});
			$("#trg-oficina").on("changed.bs.select", function (e, clickedIndex, isSelected, previousValue) {
				var select = $(this);
				arr_ofcs = $(this).val();
				if(clickedIndex == 0) {
					if(isSelected) {
						select.selectpicker("selectAll");
						arr_ofcs = ["Todos"];
					}
					else select.selectpicker("deselectAll");
				}
				console.log(arr_ofcs);
			});
			$("#trg-producto").on("changed.bs.select", function (e, clickedIndex, isSelected, previousValue) {
				var select = $(this);
				arr_prds = $(this).val();
				if(clickedIndex == 0) {
					if(isSelected) {
						select.selectpicker("selectAll");
						arr_prds = ["Todos"];
					}
					else select.selectpicker("deselectAll");
				}
				console.log(arr_prds);
			});
			$("#map-container").hide();
		</script>
		<script type="text/javascript" src="{{ asset('js/bootstrap-select.js') }}"></script>
    	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBKznc28sCbcKDuJh2AHpFohCItP5YIwKk&callback=initMap" async defer></script>
	</body>
</html>