<!DOCTYPE html>
<html>
	<head>
		<title>Carga masiva</title>
		@include("common.styles")
		<link rel="stylesheet" type="text/css" href="{{ asset('css/datepicker.min.css') }}">
		<style type="text/css">
			.table-responsive{max-height:1000px !important;}
			.map-canvas{height:480px;width:640px;}
			.images-ifr {height:640px;width:1080px;border:1px solid #888}
		</style>
	</head>
	<body>
		@include("common.navbar")
		<!-- PAGINA -->
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-xs-10 col-sm-8 col-md-6 col-lg-4">
					<div class="form-container alert alert-secondary" role="alert">
						<form id="form-filtro" method="post" class="form-inline d-flex">
							<label class="form-control-sm">Fecha</label>
							<input type="text" class="form-control form-control-sm datepicker" id="fecha" placeholder="Día" style="width:12em;margin-right:auto;">
							<button id="btn-form" type="button" class="btn btn-success btn-sm"><i class="fa fa-search"></i> Buscar</button>
						</form>
					</div>
				</div>
			</div>
		</div>
		<div id="ct-lista" class="container-fluid d-none">
			<div class="row">
				<div class="col">
					<table id="tabla-registros" class="table table-striped table-hover">
						<thead>
							<tr>
								<th>ID</th>
								<th>Secuencia</th>
								<th>Nro. pedido</th>
								<th>Fecha</th>
								<th>Respuesta</th>
								<th>Motivo cliente</th>
								<th>Oficina</th>
								<th>Motivo Union</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
		</div>
		<!-- loader de búsqueda -->
		<div id="loader-busqueda">
			<div>
				<img src="{{ asset('images/icons/buscando.svg') }}">
				<p>Buscando datos. Por favor, espere...</p>
			</div>
		</div>
		<!-- JS -->
		@include("common.scripts")
		<script type="text/javascript" src="{{ asset('js/datepicker.min.js') }}"></script>
		<script type="text/javascript">
			let data;
			let hoy = '{{ date("d/m/Y") }}';

			function buscar (event) {
				event.preventDefault();
				$('#ct-lista').removeClass('d-none');
				let fecha = document.getElementById('fecha').value;
				if (fecha.length == 0) {
					alert('Seleccione una fecha válida');
					return;
				}
				let tbody = $('#tabla-registros tbody');
				tbody.empty().append(
					$('<tr>').append(
						$('<td>').text('Cargando datos. Por favor, espere...').attr('colspan',7)
					)
				);
				$.ajax({
					url: '{{ url("masivos/ajax/reporte") }}',
					method: 'get',
					data: { fecha: fecha },
					dataType: 'json',
					success: function (result) {
						let registros = result.registros;
						let numregistros = registros.length;
						tbody.empty();
						for (let i = 0; i < numregistros; i++) {
							let registro = registros[i];
							tbody.append(
                                $('<tr>').append(
                                    $('<td>').text([registro.codautogen,registro.nroproceso,registro.nrocontrol].join('-'))
                                ).append(
                                    $('<td>').text(registro.secuencia)
                                ).append(
                                    $('<td>').text(registro.pedido)
                                ).append(
                                    $('<td>').text(registro.fecha_envio)
                                ).append(
                                    $('<td>').text(registro.Respuesta_ws)
                                ).append(
                                    $('<td>').text(registro.motivo_cliente)
                                ).append(
                                    $('<td>').text(registro.oficina)
                                ).append(
                                    $('<td>').text(registro.motivo_union)
                                )
                            );
						}
					}
				});
			}
			function init () {
				$(".datepicker").datepicker({
					autoclose: true,
					format: "dd/mm/yyyy",
					language:"es",
					todayHighlight: true,
					endDate: hoy
				}).on('hide', function (date) {
					let fecha = document.getElementById('fecha').value;
					if (fecha.length == 0) document.getElementById('fecha').value = hoy;
				});
				$('#fecha').val(hoy);
				$('#btn-form').on('click', buscar);
                $('#btn-form').trigger('click');
			}

			$(init);
		</script>
	</body>
</html>