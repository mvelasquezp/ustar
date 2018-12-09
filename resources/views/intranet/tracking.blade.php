<!DOCTYPE html>
<html>
	<head>
		<title>Servicios | Distribución</title>
		@include("common.styles")
		<link rel="stylesheet" type="text/css" href="{{ asset('css/datepicker.min.css') }}">
		<style type="text/css">
			.table-responsive{max-height:1000px !important;}
			.map-canvas{height:480px;width:640px;}
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
							<input type="text" class="form-control form-control-sm datepicker" id="fdesde" placeholder="Desde" style="width:7em;">
							<input type="text" class="form-control form-control-sm datepicker" id="fhasta" placeholder="Hasta" style="width:7em;">
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
							<label class="form-control-sm" for="documento">
								Doc.Union&nbsp;<input type="text" class="form-control form-control-sm" id="documento" placeholder="Orden, guía" style="width:10em;">
							</label>
							<!-- -->
							<label class="form-control-sm" for="refcli">
								Ref.Cliente&nbsp;<input type="text" class="form-control form-control-sm" id="refcli" placeholder="GR/Factura/Boleta" style="width:10em;">
							</label>
							<!-- -->
							<label class="form-control-sm" for="destinatario">
								Destinatario&nbsp;<input type="text" class="form-control form-control-sm" id="destinatario" placeholder="Nombre/RUC-DNI/Empresa" style="width:20em;">
							</label>
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
				<div class="floating-buttons-container">
					<a id="btn-xls" href="#" class="btn btn-success btn-circle" title="Exportar a XLS"><i class="fa fa-file-excel-o"></i></a>
					<!--a id="btn-correo" href="#" class="btn btn-danger btn-circle" title="Enviar por correo"><i class="fa fa-envelope-o"></i></a-->
				</div>
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
    	<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBKznc28sCbcKDuJh2AHpFohCItP5YIwKk" async defer></script>
		<script type="text/javascript">
			var data;
			$(".datepicker").datepicker({
				autoclose: true,
				format: "dd/mm/yyyy",
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
                        	var ipunto = { lat:0, lon:0 };
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
								if(row.latitud && row.longitud) {
									ipunto.lat = row.latitud;
									ipunto.lon = row.longitud;
								}
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
									var url;
									if(img.nomimg.indexOf(".tif") == -1) {
										url = "{{ url('viewer') }}/" + img.rutaimg.replace(/\/\//g,"@") + img.nomimg;
									}
									else {
										url = "http://trackingnatura.unionstar.com.pe/wstar/modulos/busquedas/viewer.php?r=" + img.rutaimg.replace(/\/\//g,"@") + img.nomimg;
									}
									var div = $("<div/>").addClass("carousel-item").css("height","480px").append(
										$("<img/>").addClass("d-block w-100").attr({
											"src": url,
											"alt": img.nomimg
										})
									);
									if(j == 0) {
										li.addClass("active");
										div.addClass("active");
									}
									carr_indicators.append(li);
									carr_inner.append(div);
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
								).append(
									$("<li/>").addClass("nav-item").append(
										$("<a/>").addClass("nav-link").attr({
											"id": "mapa-tab-" + idx,
											"data-toggle": "tab",
											"href": "#mapa-" + idx,
											"role": "tab",
											"aria-controls": "#mapa-" + idx,
											"aria-selected": true
										}).html("Mapa")
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
								).append(
									$("<div/>").addClass("tab-pane fade").attr({
										"id": "mapa-" + idx,
										"role": "tabpanel",
										"aria-labelledby": "mapa-tab-" + idx
									}).append(
										$("<div/>").addClass("col-sm-12 col-md-8 col-lg-6").append(
											$("<div/>").attr("id", "map-canvas-" + idx)
										)
									)
								)
							);
							if(ipunto.lat != 0 && ipunto.lon != 0) {
								DibujarMapa("map-canvas-" + idx, ipunto);
							}
							else {
								$("#map-canvas-" + idx).append(
									$("<p/>").addClass("text-danger mb-2").html("No hay información geográfica del tracking")
								)
							}
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
				if(page == 1) {
					$("#pager-first").addClass("disabled");
					$("#pager-prev").addClass("disabled");
				}
				else {
					$("#pager-first").removeClass("disabled");
					$("#pager-prev").removeClass("disabled");
				}
				//
				var IndiceActual = page + 1;
				var LimitePaginas = 3;
				$(".page-numerator").hide();
				$("#pager").children("li").eq(IndiceActual).addClass("active").show();
				for(var i = 1; i <= LimitePaginas; i++) {
					if(IndiceActual - i > 1) $("#pager").children("li").eq(IndiceActual - i).show();
				}
				for(var i = 1; i <= LimitePaginas; i++) {
					if(IndiceActual + i < data.pages + 2) $("#pager").children("li").eq(IndiceActual + i).show();
				}
				//
				if(page == data.pages) {
					$("#pager-last").addClass("disabled");
					$("#pager-next").addClass("disabled");
				}
				else {
					$("#pager-last").removeClass("disabled");
					$("#pager-next").removeClass("disabled");
				}
				data.currentPage = page;
			}
			function BuildPager(pages) {
				var pager = $("#pager");
				pager.empty();
				pager.append(
					$("<li/>").attr("id","pager-first").addClass("page-item").append(
						$("<a/>").addClass("page-link").attr("href","#").data("goto","first").append(
							$("<i/>").addClass("fa fa-step-backward")
						)
					)
				).append(
					$("<li/>").attr("id","pager-prev").addClass("page-item").append(
						$("<a/>").addClass("page-link").attr("href","#").data("goto","prev").append(
							$("<i/>").addClass("fa fa-caret-left")
						)
					)
				);
				for(var i = 1; i <= pages; i++) {
					pager.append(
						$("<li/>").addClass("page-item page-numerator").append(
							$("<a/>").addClass("page-link").attr("href","#").data("goto",i).html(i)
						)
					);
				}
				pager.append(
					$("<li/>").attr("id","pager-next").addClass("page-item").append(
						$("<a/>").addClass("page-link").attr("href","#").data("goto","next").append(
							$("<i/>").addClass("fa fa-caret-right")
						)
					)
				).append(
					$("<li/>").attr("id","pager-last").addClass("page-item").append(
						$("<a/>").addClass("page-link").attr("href","#").data("goto","last").append(
							$("<i/>").addClass("fa fa-step-forward")
						)
					)
				);
				$(".page-link").on("click", function(event) {
					event.preventDefault();
					var idx = $(this).data("goto");
					var goTo = 0;
					switch(idx) {
						case "first":goTo = 1;break;
						case "prev":goTo = data.currentPage - 1;break;
						case "next":goTo = data.currentPage + 1;break;
						case "last":goTo = data.pages;break;
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
			$("#dv-table").children("div").children("div").children("div").css("height", (window.innerHeight - 275) + "px");
			$("#btn-xls").on("click", function(event) {
				event.preventDefault();
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
				var loader = window.open("{{ url('export') }}", "_blank", "height=420,scrollbars=no,titlebar=no,toolbar=no,width=540");
				$.post("{{ url('tracking/ajax/export') }}", p, function(response) {
					if(response.state == "success") {
						setTimeout(function() {
							loader.location.href = "{{ url('download') }}/" + response.id;
							setTimeout(function() {
								loader.close();
							}, 10000);
						}, 1000);
					}
					else alert(response.message);
				}, "json");
			});
			//
	        DibujarMapa = (div, coords) => {
	            $("#" + div).addClass("map-canvas").empty();
	            var map = new google.maps.Map(document.getElementById(div), {
	                center: {lat: coords.lat, lng: coords.lon},
	                zoom: 18
	            });
	            var marker = new google.maps.Marker({
	                position: {lat:coords.lat, lng:coords.lon},
	                map: map,
	                title: "Punto de entrega"
	            });
	        }
		</script>
	</body>
</html>