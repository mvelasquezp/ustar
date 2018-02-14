<!DOCTYPE html>
<html>
	<head>
		<title>Indicadores | Distribución | Entregas</title>
		@include("common.styles")
		<style type="text/css">
			.div-chart{height:400px;width:100%}
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
							<label class="form-control-sm" for="ciclo">
								Ciclo&nbsp;
								<input type="text" id="trg-ciclo" class="form-control form-control-sm" placeholder="Seleccione" style="width:12em;">
							</label>
							<!-- -->
							@if(strcmp($usuario->tp_cliente,'admin') == 0)
							<label class="form-control-sm" for="oficina">
								Oficina&nbsp;
								<input type="text" id="trg-oficina" class="form-control form-control-sm" placeholder="Seleccione" style="width:12em;">
							</label>
							@else
							<input type="hidden" id="oficina" class="ch-of" value="{{ $ofcs[0]->codigo }}">
							@endif
							<!-- -->
							<label class="form-control-sm" for="producto">
								Producto&nbsp;
								<input type="text" id="trg-producto" class="form-control form-control-sm" placeholder="Seleccione" style="width:12em;">
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
						<h5 class="modal-title text-primary">Seleccione centros de costo</h5>
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
		<div class="container">
			<div class="row">
				<div class="col-4" style="max-height:840px;overflow-y:auto">
					<table class="table table-striped table-sm">
						<thead>
							<tr>
								<th>Estado</th>
								<th>Motivo</th>
								<th>Día visita</th>
								<th>Cantidad</th>
							</tr>
						</thead>
						<tbody id="chart-tbody-1"></tbody>
					</table>
				</div>
				<div class="col-8">
					<div class="row">
						<div class="col">
							<div id="chart-1" class="div-chart"></div>
						</div>
					</div>
					<div class="row">
						<div class="col">
							<div id="chart-2" class="div-chart"></div>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-4"></div>
				<div class="col-8"></div>
			</div>
		</div>
		<!-- JS -->
		@include("common.scripts")
		@include("common.js-ciclos")
		@include("common.js-oficinas")
		@include("common.js-productos")
		<script type="text/javascript" src="{{ asset('js/highcharts.js') }}"></script>
		<script type="text/javascript" src="{{ asset('js/modules/drilldown.js') }}"></script>
		<script type="text/javascript">
			var data;
			//
			$("#btn-form").on("click", function(e) {
				e.preventDefault();
				$("#loader-busqueda").fadeIn(150);
				var p = {
					_token: "{{ csrf_token() }}",
					ccl: arr_ciclos,
					ofc: arr_ofcs,
					prd: arr_prds,
					loc: document.getElementById("tplocal").checked ? 'S' : 'N',
					nac: document.getElementById("tpnacional").checked ? 'S' : 'N',
					int: document.getElementById("tpinternacional").checked ? 'S' : 'N'
				};
				$("#btn-form").hide();
				$.post("{{ url('indicadores/ajax/buscar') }}", p, function(response) {
					if(response.state == "success") {
						var data1 = response.data.data1;
						var data2 = response.data.data2;
						//colores
						var colors = ["#4caf50","#1976d2","#ff9800","#d32f2f","#00bcd4","#fdd835","#512da8","#8bc34a","#607d8b","#ffc107","#3f51b5","#cddc39","#ff5722","#6a1b9a","#039be5","#009688","#795548","#c2185b","#9e9e9e"];
						//grafico 1
						var ds1 = [];
						var dt1 = [];
						var id_idx = 0;
						var curr_estado = "";
						var curr_motivo = "";
						var curr_arr = [];
						var curr_tot = 0;
						var curr_global = 0;
						//grafico 2
						var ds2 = [];
						var dt2 = [];
						var id_idc = 0;
						var curr_dia = "";
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
							//grafico 2
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
						    	pie: {
						            allowPointSelect: true,
						            cursor: 'pointer',
						            dataLabels: { enabled: false },
						            showInLegend: true
						        }
						    },
						    tooltip: {
						        headerFormat: '<span style="font-size:11px">{series.name}</span><br>',
						        pointFormat: '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.0f}</b> unds.<br/>'
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
					}
					else alert(response.msg);
					$("#loader-busqueda").fadeOut(150);
					$("#btn-form").show();
				}, "json").fail(function(err) {
					$("#loader-busqueda").fadeOut(150);
					$("#btn-form").show();
				});
			});
		</script>
	</body>
</html>