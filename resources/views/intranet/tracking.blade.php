<!DOCTYPE html>
<html>
	<head>
		<title>Servicios | Distribución</title>
		@include("common.styles")
		<link rel="stylesheet" type="text/css" href="{{ asset('css/datepicker.min.css') }}">
		<style type="text/css">
			.form-container{margin:10px;padding:5px}
			label.form-control-sm{padding:5px}
			.table-responsive{max-height:400px;}
			#dv-table{display:none;}
			#loader-busqueda{background-color:#e8e8e8;bottom:10px;box-shadow:1px 1px 3px #808080;display:none;left:10px;position:absolute}
			#loader-busqueda>div{display:table;height:48px;width:256px}
			#loader-busqueda>div>*{display:table-cell;vertical-align:middle}
			#loader-busqueda>div>img{height:32px;margin:8px}
			#loader-busqueda>div>p{font-size:12px}
			.tr-detalle{display:none}
			.tr-detalle>td>.dv-loader{display:table;padding:10px;width:100%}
			.tr-detalle>td>.dv-loader>*{display:inline-block;vertical-align:middle}
			.tr-detalle>td>.dv-loader>img{height:64px;width:64px}
			.tr-detalle>td>.dv-loader>p{font-size:14px;margin:0;text-align:left}
			/* ----------------- */
			.vmsl-container{
				background-color: #1565c0;
				height:320px;
				width: 240px;
				position: absolute;
				box-shadow: 1px 1px 3px #d0d0d0;
				overflow-y: auto;
			}
			.vmsl-container>ul{list-style:none;text-align:left;margin:2px;padding:0;}
			.vmsl-container>ul>li{padding:2px 8px;text-align:left;}
			.vmsl-container>ul>li>label{display:block;color:#e3f2fd;cursor:pointer;}
			.vmsl-container>ul>li>label:hover{color:#f8f8f8;}
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
							<label class="form-control-sm">Fecha</label>
							<input type="text" class="form-control form-control-sm datepicker" id="fdesde" placeholder="Desde" style="width:6em;">
							<input type="text" class="form-control form-control-sm datepicker" id="fhasta" placeholder="Hasta" style="width:6em;">
							<!-- -->
							<label class="form-control-sm" for="producto">Producto</label>
							<select class="form-control form-control-sm" id="producto" style="max-width:90px;">
								@foreach($prds as $producto)
								<option value="{{ $producto->codigo }}">{{ $producto->descripcion }}</option>
								@endforeach
							</select>
							<!-- -->
							@if(strcmp($usuario->tp_cliente,'admin') == 0)
							<label class="form-control-sm" for="oficina">Oficina</label>
							<select class="form-control form-control-sm" id="oficina" style="max-width:90px;">
								@foreach($ofcs as $oficina)
								<option value="{{ $oficina->codigo }}">{{ $oficina->descripcion }}</option>
								@endforeach
							</select>
							<!-- -->
							@else
							<input type="hidden" id="oficina" value="{{ $ofcs[0]->codigo }}">
							@endif
							<!-- -->
							<label class="form-control-sm" for="documento">Doc.Union</label>
							<input type="text" class="form-control form-control-sm" id="documento" placeholder="Orden, guía" style="width:5em;">
							<!-- -->
							<label class="form-control-sm" for="refcli">Ref.Cliente</label>
							<input type="text" class="form-control form-control-sm" id="refcli" placeholder="GR/Factura/Boleta" style="width:5em;">
							<!-- -->
							<label class="form-control-sm" for="destinatario">Destinatario</label>
							<input type="text" class="form-control form-control-sm" id="destinatario" placeholder="Nombre/RUC-DNI/Empresa" style="width:8em;">
							<!-- -->
							&nbsp;<button id="btn-form" type="button" class="btn btn-success btn-sm"><i class="fa fa-search"></i> Buscar</button>
						</form>
					</div>
				</div>
			</div>
		</div>
		<div id="dv-table" class="container-fluid">
			<div class="row">
				<div class="col">
					<div class="table-responsive">
						<table class="table table-sm table-striped">
							<thead class="thead-dark">
								<tr>
									<th>#</th>
									<th>FechaIng</th>
									<th>CodGuia</th>
									<th>Remito</th>
									<th>Control</th>
									<th>Nombre</th>
									<th>Direccion</th>
									<th>Ciudad</th>
									<th>Dedestinatario</th>
									<th>Tp.Envio</th>
									<th>Contenido</th>
									<th>Servicio</th>
									<th>Motivo</th>
									<th>Ult.Visita</th>
									<th>NroDocu.</th>
									<th>Cuenta</th>
									<th>Comprobante</th>
									<th>Empresa</th>
									<th>DetalleMotivo</th>
									<th>Origen</th>
								</tr>
							</thead>
							<tbody id="main-tbody"></tbody>
						</table>
					</div>
				</div>
			</div>
			<nav aria-label="Page navigation example">
				<ul id="pager" class="pagination pagination-sm justify-content-center">
					<li class="page-item disabled">
						<a class="page-link" href="#" tabindex="-1">Previous</a>
					</li>
					<li class="page-item active"><a class="page-link" href="#">1</a></li>
					<li class="page-item"><a class="page-link" href="#">2</a></li>
					<li class="page-item"><a class="page-link" href="#">3</a></li>
					<li class="page-item">
						<a class="page-link" href="#">Next</a>
					</li>
				</ul>
			</nav>
		</div>
		<!-- loader de búsqueda -->
		<div id="loader-busqueda">
			<div>
				<img src="{{ asset('images/icons/buscando.svg') }}">
				<p>Cargando datos. Por favor, espere...</p>
			</div>
		</div>
		<!-- JS -->
		@include("common.scripts")
		<script type="text/javascript" src="{{ asset('js/datepicker.min.js') }}"></script>
		<script type="text/javascript">
			var data;
			$(".datepicker").datepicker({
				autoclose: true,
				format: "yyyy-mm-dd",
				language:"es",
				todayHighlight: true
			});
			function toggleRow(event) {
				event.preventDefault();
				var a = $(this);
				var tr = a.parent().parent().next().next();
				if(a.data("state") == "hidden") {
					tr.fadeIn(150);
					a.data("state","showed");
				}
				else {
					tr.fadeOut(150);
					a.data("state","hidden");
				}
				if(a.data("loaded") == "0") {
					a.data("loaded","1");
					var idx = parseInt(a.data("idx"));
					var p = {
						_token: "{{ csrf_token() }}",
						agn: data.rows[idx].autogen,
						prc: data.rows[idx].remito,
						ctr: data.rows[idx].control
					};
					$.post("{{ url('tracking/ajax/detalle') }}", p, function(response) {
						if(response.state == "success") {
							var rows = response.data.rows;
							var imgs = response.data.imgs;
							//arma la tabla detalle
							var tbody = $("<tbody/>");
							for(var i in rows) {
								var row = rows[i];
								tbody.append(
									$("<tr/>").append(
										$("<td/>").html(row.fecha)
									).append(
										$("<td/>").html(row.estado)
									).append(
										$("<td/>").html(row.observ)
									)
								);
							}
							var table = $("<table/>").addClass("table table-striped table-sm").append(
								$("<thead/>").addClass("bg-success text-light").append(
									$("<tr/>").append(
										$("<th/>").html("Fecha")
									).append(
										$("<th/>").html("Estado")
									).append(
										$("<th/>").html("Detalle")
									)
								)
							).append(tbody);
							//arma el carrusel de imagenes
							var carrusel;
							if(imgs.length > 0) {
								var carr_indicators = $("<ol/>").addClass("carousel-indicators");
								var carr_inner = $("<div/>").addClass("carousel-inner");
								for(var j in imgs) {
									var img = imgs[j];
									var li = $("<li/>").attr({
										"data-target": "#carrusel-" + idx,
										"data-slide-to": j
									});
									var div = $("<div/>").addClass("carousel-item").css("height","480px").append(
										$("<img/>").addClass("d-block w-100").attr({
											"src": "{{ url('viewer') }}/" + img.rutaimg.replace(/\/\//g,"@") + img.nomimg,
											"alt": img.nomimg
										})
									);
									if(j == 0) {
										li.addClass("active");
										div.addClass("active");
									}
									carr_indicators.append(li);
									carr_inner.append(div);
//console.log();
								}
								carrusel = $("<div/>").addClass("carousel slide").attr({
									"data-ride": "carousel",
									"id": "carrusel-" + idx
								}).append(carr_indicators).append(carr_inner).append(
									$("<a/>").addClass("carousel-control-prev").attr({
										"href": "#carrusel-" + idx,
										"role": "button",
										"data-slide": "prev"
									}).append(
										$("<span/>").addClass("carousel-control-prev-icon").attr("aria-hidden", true)
									).append(
										$("<span/>").addClass("sr-only").html("Anterior")
									)
								).append(
									$("<a/>").addClass("carousel-control-next").attr({
										"href": "#carrusel-" + idx,
										"role": "button",
										"data-slide": "next"
									}).append(
										$("<span/>").addClass("carousel-control-next-icon").attr("aria-hidden", true)
									).append(
										$("<span/>").addClass("sr-only").html("Siguiente")
									)
								)
							}
							else {
								carrusel = $("<div/>").append(
									$("<p/>").html("No hay imágenes registradas para este cargo")
								);
							}
							//inserta todo el contenido
							var td = tr.children("td").eq(1);
							td.empty().append(
								$("<ul/>").addClass("nav nav-tabs").attr("role","tablist").append(
									$("<li/>").addClass("nav-item").append(
										$("<a/>").addClass("nav-link active").attr({
											"id": "seguimiento-tab-" + idx,
											"data-toggle": "tab",
											"href": "#seguimiento-" + idx,
											"role": "tab",
											"aria-controls": "#seguimiento-" + idx,
											"aria-selected": true
										}).html("Seguimiento")
									)
								).append(
									$("<li/>").addClass("nav-item").append(
										$("<a/>").addClass("nav-link").attr({
											"id": "galeria-tab-" + idx,
											"data-toggle": "tab",
											"href": "#galeria-" + idx,
											"role": "tab",
											"aria-controls": "#galeria-" + idx,
											"aria-selected": true
										}).html("Imagenes")
									)
								)
							).append(
								$("<div/>").addClass("tab-content").append(
									$("<div/>").addClass("tab-pane fade show active").attr({
										"id": "seguimiento-" + idx,
										"role": "tabpanel",
										"aria-labelledby": "seguimiento-tab-" + idx
									}).css("padding","5px").append(
										$("<div/>").addClass("row").append(
											$("<div/>").addClass("col-sm-12 col-md-8 col-lg-6").append(table)
										)
									)
								).append(
									$("<div/>").addClass("tab-pane fade").attr({
										"id": "galeria-" + idx,
										"role": "tabpanel",
										"aria-labelledby": "galeria-tab-" + idx
									}).append(
										$("<div/>").addClass("col-sm-12 col-md-8 col-lg-6").append(carrusel)
									)
								)
							);
						}
						else {
							alert(response.message);
							a.data("loaded","0");
						}
					}, "json");
				}
			}
			function RenderTable(page) {
				var tbody = $("#main-tbody");
				var end = page * data.rowsPerPage;
				var start = end - data.rowsPerPage;
				if(end > data.records) end = data.records;
				tbody.empty();
				for(var i = start; i < end; i++) {
					var fila = data.rows[i];
					tbody.append(
						$("<tr/>").append(
							$("<td/>").append(
								$("<a/>").addClass("btn btn-primary btn-sm").attr({href:"#",role:"button"}).data("idx",i).data("loaded","0").data("state","hidden").html(i + 1).on("click", toggleRow)
							)
						).append(
							$("<td/>").html(fila.fecing)
						).append(
							$("<td/>").html(fila.codguia)
						).append(
							$("<td/>").html(fila.remito)
						).append(
							$("<td/>").html(fila.control)
						).append(
							$("<td/>").html(fila.nombre)
						).append(
							$("<td/>").html(fila.direccion)
						).append(
							$("<td/>").html(fila.ciudad)
						).append(
							$("<td/>").html(fila.idedestin)
						).append(
							$("<td/>").html(fila.tipoenvio)
						).append(
							$("<td/>").html(fila.contenido)
						).append(
							$("<td/>").html(fila.servicio)
						).append(
							$("<td/>").html(fila.motivo)
						).append(
							$("<td/>").html(fila.ultvisita)
						).append(
							$("<td/>").html(fila.nrodocu)
						).append(
							$("<td/>").html(fila.cuenta)
						).append(
							$("<td/>").html(fila.comprobante)
						).append(
							$("<td/>").html(fila.empresa)
						).append(
							$("<td/>").html(fila.detallemotivo)
						).append(
							$("<td/>").html(fila.origen)
						)
					).append(
						$("<tr/>").addClass("tr-detalle")
					).append(
						$("<tr/>").addClass("tr-detalle").append(
							$("<td/>")
						).append(
							$("<td/>").attr("colspan",19).append(
								$("<div/>").addClass("dv-loader").append(
									$("<img/>").attr("src","{{ asset('images/icons/loader.svg') }}")
								).append(
									$("<p/>").html("Cargando datos...")
								)
							)
						)
					);
				}
				$("#pager").children(".active").removeClass("active");
				if(page == 1) $("#pager-prev").addClass("disabled");
				else $("#pager-prev").removeClass("disabled");
				$("#pager").children("li").eq(page).addClass("active");
				if(page == data.pages) $("#pager-next").addClass("disabled");
				else $("#pager-next").removeClass("disabled");
				data.currentPage = page;
			}
			function BuildPager(pages) {
				var pager = $("#pager");
				pager.empty();
				pager.append(
					$("<li/>").attr("id","pager-prev").addClass("page-item").append(
						$("<a/>").addClass("page-link").attr("href","#").data("goto","prev").html("Anterior")
					)
				);
				for(var i = 1; i <= pages; i++) {
					pager.append(
						$("<li/>").addClass("page-item").append(
							$("<a/>").addClass("page-link").attr("href","#").data("goto",i).html(i)
						)
					);
				}
				pager.append(
					$("<li/>").attr("id","pager-next").addClass("page-item").append(
						$("<a/>").addClass("page-link").attr("href","#").data("goto","next").html("Siguiente")
					)
				);
				$(".page-link").on("click", function(event) {
					event.preventDefault();
					var idx = $(this).data("goto");
					var goTo = 0;
					switch(idx) {
						case "prev":goTo = data.currentPage - 1;break;
						case "next":goTo = data.currentPage + 1;break;
						default: goTo = parseInt(idx);
					}
					RenderTable(goTo);
				});
				RenderTable(1);
			}
			$("#btn-form").on("click", function(event) {
				var a = $(this);
				a.hide();
				$("#loader-busqueda").fadeIn(150);
				var p = {
					_token: "{{ csrf_token() }}",
					dsd: document.getElementById("fdesde").value,
					hst: document.getElementById("fhasta").value,
					ofc: [document.getElementById("oficina").value],
					prd: [document.getElementById("producto").value],
					doc: document.getElementById("documento").value,
					ref: document.getElementById("refcli").value,
					dst: document.getElementById("destinatario").value
				};
				$.post("{{ url('tracking/ajax/buscar') }}", p, function(response) {
					if(response.state == "success") {
						rows = response.data.rows;
						var numberOfRows = rows.length;
						var rowsPerPage = 50;
						var numberOfPages = Math.floor(numberOfRows / rowsPerPage) + (numberOfRows % rowsPerPage > 0 ? 1 : 0);
						data = {
							records: numberOfRows,
							rowsPerPage: rowsPerPage,
							pages: numberOfPages,
							currentPage: 1,
							rows: rows
						};
						BuildPager(numberOfPages);
						$("#dv-table").fadeIn(150);
					}
					else alert(response.message);
					a.show();
					$("#loader-busqueda").fadeOut(150);
				}, "json");
			});
			//funcion para calcular la posicion del control
		</script>
	</body>
</html>