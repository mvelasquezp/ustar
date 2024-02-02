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
								<th>Nro. pedido</th>
								<th>Fecha</th>
								<th>Motivo</th>
								<th>Justif.</th>
								<th>Usuario</th>
								<th class="text-left" width="40%">
									<button class="btn btn-success btn-sm d-none" id="btn-upload" style="cursor:pointer;">
										<i class="fa fa-refresh"></i> Comenzar envío
									</button>
									<a href="{{ url('masivos/reporte') }}" class="btn btn-primary btn-sm ml-2 d-none" id="btn-reporte" style="cursor:pointer;">
										<i class="fa fa-eye"></i> Resultados
									</a>
								</th>
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
				<p>Procesando los datos. Por favor, espere...</p>
			</div>
		</div>
		<!-- JS -->
		@include("common.scripts")
		<script type="text/javascript" src="{{ asset('js/datepicker.min.js') }}"></script>
		<script type="text/javascript">
			let data;
			let hoy = '{{ date("d/m/Y") }}';
			let numFilas = -1;

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
					url: '{{ url("masivos/ajax/buscar") }}',
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
									$('<td>').text([registro.codautogen, registro.nroproceso, registro.nrocontrol].join('-'))
								).append(
									$('<td>').text(registro.nropedido)
								).append(
									$('<td>').text(registro.fechahoraevento)
								).append(
									$('<td>').text(registro.DesMotivoEnvio)
								).append(
									$('<td>').text(registro.DesJustiMotivo)
								).append(
									$('<td>').text(registro.nomusuario)
								).append(
									$('<td>').append(
										$('<img>').attr('src','{{ asset("images/icons/res-blank.png") }}').css('height',24)
									).addClass('td-loader')
								).attr('id','row-' + i).data('json',JSON.stringify(registro))
							);
						}
						$('#btn-upload').removeClass('d-none');
						numFilas = numregistros;
					}
				});
			}
			async function enviar_pedido_ws (autogen, proceso, control, pedido, fecha, evento, usuario, motivo, justificacion) {
				try {
					let data = {
						_token: '{{ csrf_token() }}',
						autogen: autogen,
						proceso: proceso,
						control: control,
						pedido: pedido,
						fecha: fecha,
						evento: evento,
						usuario: usuario,
						motivo: motivo,
						justificacion: justificacion
					};
					let ajax = await $.post('{{ url("masivos/ajax/enviar-ws") }}', data);
					return ajax.result;
				}
				catch (exception) {
					console.error(exception);
					return {
						error: exception.responseText
					};
				}
			}
			async function upload (event) {
				event.preventDefault();
				$('#loader-busqueda').fadeIn(250);
				$('#btn-upload').addClass('disabled');
				if (numFilas == -1) return;
				for (let i = 0; i < numFilas; i++) {
					let rowid = '#row-' + i;
					let itd = $(rowid + ' .td-loader');
					let json = JSON.parse($(rowid).data('json'));
					itd.empty().append(
						$('<img>').attr('src','{{ asset("images/icons/ic-loader-green.svg") }}').css('height',24)
					);
					let result = await enviar_pedido_ws(json.codautogen, json.nroproceso, json.nrocontrol, json.nropedido, json.fechahoraevento, json.codevento, json.usuario, json.codmotivoenvio, json.codjustimotivo);
					if (result.hasOwnProperty('error')) {
						itd.empty().append(
							$('<img>').attr('src','{{ asset("images/icons/res-error.png") }}').css('height',24).addClass('d-inline')
						).append(
							$('<small>').text(result.error).addClass('mb-0 text-danger d-inline ml-1')
						);
					}
					else {
						itd.empty().append(
							$('<img>').attr('src','{{ asset("images/icons/res-ok.png") }}').css('height',24).addClass('d-inline')
						).append(
							$('<p>').text('OK').addClass('mb-0 d-inline ml-1')
						);
						setTimeout(function() {
							$('#row-' + i).fadeOut(250, function () {
								$('#row-' + i).remove();
							});
						}, 2500);
					}
				}
				$('#btn-upload').removeClass('disabled');
				$('#btn-reporte').removeClass('d-none');
				$('#loader-busqueda').fadeOut(250);
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
				$('#btn-upload').on('click', upload);
			}

			$(init);
		</script>
	</body>
</html>