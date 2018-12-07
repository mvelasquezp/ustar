<!DOCTYPE html>
<html>
	<head>
		<title>Servicios | Distribución</title>
		@include("common.styles")
		<link rel="stylesheet" type="text/css" href="{{ asset('css/datepicker.min.css') }}">
		<link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap-select.css') }}">
		<style type="text/css">
			.table-responsive{max-height:1000px !important;}
			.col-sorter{
				padding-left:12px !important;padding-right:12px !important;background-repeat:no-repeat;background-position:right center;background-size:12px 24px;text-align:center;cursor:pointer;-webkit-touch-callout: none;-webkit-user-select: none;-khtml-user-select: none;-moz-user-select: none;-ms-user-select: none;user-select: none;
			}
			.col-sorted-asc{background-image:url("{{ asset('images/icons/ic_sort_up.png') }}");}
			.col-sorted-desc{background-image:url("{{ asset('images/icons/ic_sort_down.png') }}");}
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
							@if(strcmp($usuario->tp_cliente,'admin') == 0)
							<label class="form-control-sm" for="oficina">
								Oficina&nbsp;
								<!--input type="text" id="trg-oficina" class="form-control form-control-sm" placeholder="Seleccione" style="width:7em;"-->
								<select class="selectpicker" multiple data-live-search="true">
									@foreach($ofcs as $idx => $oficina)
									@if($idx != 0)
									<option value="{{ $oficina->codigo }}">{{ $oficina->descripcion }}</option>
									@endif
									@endforeach
								</select>
							</label>
							<!-- -->
							<label class="form-control-sm" for="ccosto">
								C. Costo&nbsp;
								<!--input type="text" id="trg-ccosto" class="form-control form-control-sm" placeholder="Seleccione" style="width:7em;"-->
								<select class="selectpicker" multiple data-live-search="true">
									@foreach($ccts as $idx => $ccosto)
									@if($idx != 0)
									<option value="{{ $ccosto->codigo }}">{{ $ccosto->descripcion }}</option>
									@endif
									@endforeach
								</select>
							</label>
							@else
							<input type="hidden" id="oficina" class="ch-of" value="{{ $ofcs[0]->codigo }}">
							<input type="hidden" id="ccosto" class="ch-cc" value="{{ $ccts[0]->codigo }}">
							@endif
							<!-- -->
							<label class="form-control-sm" for="producto">
								Producto&nbsp;
								<!--input type="text" id="trg-producto" class="form-control form-control-sm" placeholder="Seleccione" style="width:7em;"-->
								<select class="selectpicker" multiple data-live-search="true">
									@foreach($prds as $idx => $producto)
									@if($idx != 0)
									<option value="{{ $producto->codigo }}">{{ $producto->descripcion }}</option>
									@endif
									@endforeach
								</select>
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
		<div id="dv-table" class="container-fluid">
			<div class="row">
				<div class="col">
					<div class="table-responsive">
						<table class="table table-sm table-striped">
							<thead class="thead-dark">
								<tr>
									<th class="col-sorter" data-sort="0" data-idx="25">#</th>
									<th class="col-sorter" data-sort="0" data-idx="0">Origen</th>
									<th class="col-sorter" data-sort="0" data-idx="1">FechaIng</th>
									<th class="col-sorter" data-sort="0" data-idx="2">Docing</th>
									<th class="col-sorter" data-sort="0" data-idx="3">Remito</th>
									<th class="col-sorter" data-sort="0" data-idx="5">Servicio</th>
									<th class="col-sorter" data-sort="0" data-idx="18">Contenido</th>
									<th class="col-sorter" data-sort="0" data-idx="6">Cliente</th>
									<th class="col-sorter" data-sort="0" data-idx="16">Cant</th>
									<th class="col-sorter" data-sort="0" data-idx="17">Peso</th>
									<th class="col-sorter" data-sort="0" data-idx="19">Observaciones</th>
									<th class="col-sorter" data-sort="0" data-idx="20">Referencia</th>
									<th class="col-sorter" data-sort="0" data-idx="23">Contacto</th>
									<th class="col-sorter" data-sort="0" data-idx="24">C.Costo</th>
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
		<!-- modal - centros de costo -->
		<div id="modal-ccosto" class="modal fade" tabindex="-1" role="dialog">
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
							@foreach($ccts as $idx => $ccosto)
							@if($idx == 0)
							<label class="list-group-item d-flex justify-content-between align-items-center active">
								{{ $ccosto->descripcion }}
								<span><input id="ch-cc-all" type="checkbox" value="{{ $ccosto->codigo }}" checked="checked"></span>
							</label>
							@else
							<label class="list-group-item d-flex justify-content-between align-items-center">
								{{ $ccosto->descripcion }}
								<span><input id="ch-cc-{{ $idx }}" class="ch-cc" type="checkbox" value="{{ $ccosto->codigo }}" data-label="{{ $ccosto->descripcion }}" checked="checked"></span>
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
			//
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
						rem: data.rows[idx].remito
					};
					$.post("{{ url('servicios/distribucion/ajax/detalle') }}", p, function(response) {
						if(response.state == "success") {
							var rows = response.data.rows;
							var tbody = $("<tbody/>");
							for(var i in rows) {
								var row = rows[i];
								tbody.append(
									$("<tr/>").append(
										$("<td/>").html(row.control)
									).append(
										$("<td/>").html(row.destinatario)
									).append(
										$("<td/>").html(row.direccion)
									).append(
										$("<td/>").html(row.ciudad)
									).append(
										$("<td/>").html(row.motivo)
									).append(
										$("<td/>").html(row.detallemotivo)
									).append(
										$("<td/>").html(row.docident)
									).append(
										$("<td/>").html(row.ultvisita)
									).append(
										$("<td/>").html(row.DocRef)
									)
								);
							}
							var td = tr.children("td").eq(1);
							td.empty().append(
								$("<table/>").addClass("table table-striped table-sm").append(
									$("<thead/>").addClass("bg-danger text-light").append(
										$("<tr/>").append(
											$("<th/>").html("Control")
										).append(
											$("<th/>").html("Destinatario")
										).append(
											$("<th/>").html("Direccion")
										).append(
											$("<th/>").html("Ciudad")
										).append(
											$("<th/>").html("Motivo")
										).append(
											$("<th/>").html("Detallemotivo")
										).append(
											$("<th/>").html("Docident")
										).append(
											$("<th/>").html("Ultvisita")
										).append(
											$("<th/>").html("DocRef")
										)
									)
								).append(tbody)
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
								$("<a/>").addClass("btn btn-primary btn-sm").attr({href:"#",role:"button"}).data("idx",i).data("loaded","0").data("state","hidden").html(fila.pos).on("click", toggleRow)
							)
						).append(
							$("<td/>").html(fila.origen)
						).append(
							$("<td/>").html(fila.fechaing)
						).append(
							$("<td/>").html(fila.docing)
						).append(
							$("<td/>").html(fila.remito)
						).append(
							$("<td/>").html(fila.servicio)
						).append(
							$("<td/>").html(fila.contenido)
						).append(
							$("<td/>").html(fila.cliente)
						).append(
							$("<td/>").html(fila.canenvios)
						).append(
							//$("<td/>").html(parseFloat(fila.peso).toLocaleString(locales,local2digits))
							$("<td/>").html(fila.peso)
						).append(
							$("<td/>").html(fila.TxtObserv)
						).append(
							$("<td/>").html(fila.refcli)
						).append(
							$("<td/>").html(fila.nomcontacto)
						).append(
							$("<td/>").html(fila.nomccosto)
						)
					).append(
						$("<tr/>").addClass("tr-detalle")
					).append(
						$("<tr/>").addClass("tr-detalle").append(
							$("<td/>")
						).append(
							$("<td/>").attr("colspan",17).append(
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
					ofc: arr_ofcs,
					ccs: arr_ccs,
					prd: arr_prds,
					loc: document.getElementById("tplocal").checked ? 'S' : 'N',
					nac: document.getElementById("tpnacional").checked ? 'S' : 'N',
					int: document.getElementById("tpinternacional").checked ? 'S' : 'N'
				};
				$.post("{{ url('servicios/distribucion/ajax/buscar') }}", p, function(response) {
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
			$("#dv-table").children("div").children("div").children("div").css("height", (window.innerHeight - 235) + "px");
			$("#btn-xls").on("click", function(event) {
				event.preventDefault();
				var p = {
					_token: "{{ csrf_token() }}",
					dsd: document.getElementById("fdesde").value,
					hst: document.getElementById("fhasta").value,
					ofc: arr_ofcs,
					ccs: arr_ccs,
					prd: arr_prds,
					loc: document.getElementById("tplocal").checked ? 'S' : 'N',
					nac: document.getElementById("tpnacional").checked ? 'S' : 'N',
					int: document.getElementById("tpinternacional").checked ? 'S' : 'N'
				};
				var loader = window.open("{{ url('export') }}", "_blank", "height=420,scrollbars=no,titlebar=no,toolbar=no,width=540");
				$.post("{{ url('servicios/distribucion/ajax/export') }}", p, function(response) {
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
			//nuevos
			function OrdenarAscendente(posicion) {
				const tam = data.rows.length;
				for(var i = 0; i < tam -1; i++) {
					for(var j = i; j < tam; j++) {
						if(data.rows[j][Object.keys(data.rows[j])[posicion]] < data.rows[i][Object.keys(data.rows[i])[posicion]]) {
							var aux = data.rows[j];
							data.rows[j] = data.rows[i];
							data.rows[i] = aux;
						}
					}
				}
				RenderTable(1);
			}
			function OrdenarDescendente(posicion) {
				const tam = data.rows.length;
				for(var i = 0; i < tam -1; i++) {
					for(var j = i; j < tam; j++) {
						if(data.rows[j][Object.keys(data.rows[j])[posicion]] > data.rows[i][Object.keys(data.rows[i])[posicion]]) {
							var aux = data.rows[j];
							data.rows[j] = data.rows[i];
							data.rows[i] = aux;
						}
					}
				}
				RenderTable(1);
			}
			$(".col-sorter").on("click", function() {
				const th = $(this);
				const orden = th.data("sort");
				const posicion = th.data("idx");
				$(".col-sorter").data("sort",0).removeClass("col-sorted-asc").removeClass("col-sorted-desc");
				if(orden == 0 || orden == -1) {
					th.addClass("col-sorted-asc");
					OrdenarAscendente(posicion);
					th.data("sort", 1);
				}
				else {
					th.addClass("col-sorted-desc");
					OrdenarDescendente(posicion);
					th.data("sort", -1);
				}
			});
			//$(".multiselect").multiselect();
			//
			$("#fdesde").val("01/12/2017");
			$("#fhasta").val("31/12/2017");
		</script>
		<script type="text/javascript" src="{{ asset('js/bootstrap-select.js') }}"></script>
		@include("common.js-oficinas")
		@include("common.js-ccostos")
		@include("common.js-productos")
	</body>
</html>