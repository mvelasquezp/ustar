<!DOCTYPE html>
<html>
	<head>
		<title>Servicios | Distribución</title>
		@include("common.styles")
		<link rel="stylesheet" type="text/css" href="{{ asset('css/datepicker.min.css') }}">
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
								<input type="text" id="trg-oficina" class="form-control form-control-sm" placeholder="Seleccione" style="width:7em;">
							</label>
							<!-- -->
							<label class="form-control-sm" for="ccosto">
								C. Costo&nbsp;
								<input type="text" id="trg-ccosto" class="form-control form-control-sm" placeholder="Seleccione" style="width:7em;">
							</label>
							@else
							<input type="hidden" id="oficina" class="ch-of" value="{{ $ofcs[0]->codigo }}">
							<input type="hidden" id="ccosto" class="ch-cc" value="{{ $ccts[0]->codigo }}">
							@endif
							<!-- -->
							<label class="form-control-sm" for="producto">
								Producto&nbsp;
								<input type="text" id="trg-producto" class="form-control form-control-sm" placeholder="Seleccione" style="width:7em;">
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
									<th>#</th>
									<th>Origen</th>
									<th>FechaIng</th>
									<th>Docing</th>
									<th>Remito</th>
									<th>Servicio</th>
									<th>Contenido</th>
									<th>Cliente</th>
									<th>Cant</th>
									<th>Peso</th>
									<th>Observaciones</th>
									<th>Referencia</th>
									<th>Contacto</th>
									<th>C.Costo</th>
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
		<!-- modal - centros de costo -->
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
			var arr_ofcs = ["Todos"];
			var arr_ccs = ["Todos"];
			var arr_prds = ["Todos"];
			//iniciar controles
			document.getElementById("trg-oficina").value = "Todos";
			document.getElementById("trg-ccosto").value = "Todos";
			document.getElementById("trg-producto").value = "Todos";
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
								$("<a/>").addClass("btn btn-primary btn-sm").attr({href:"#",role:"button"}).data("idx",i).data("loaded","0").data("state","hidden").html(i + 1).on("click", toggleRow)
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
			//modal oficina
			$("#trg-oficina").on("click", function() {
				$("#modal-oficina").modal("show");
			});
			$("#ch-of-all").change(function() {
				$(".ch-of").prop("checked", document.getElementById("ch-of-all").checked);
			});
			$(".ch-of").change(function() {
				$(".ch-of-all").prop("checked", false);
			});
			$("#modal-oficina").on("hide.bs.modal", function() {
				arr_ofcs = new Array();
				if(document.getElementById("ch-of-all").checked) {
					arr_ofcs = [document.getElementById("ch-of-all").value];
					document.getElementById("trg-oficina").value = "Todos";
				}
				else {
					var of_all = $(".ch-of:checked");
					var sseleccion = "";
					$.each(of_all, function() {
						var input = $(this);
						arr_ofcs.push(input.val());
						sseleccion += (sseleccion == "" ? "" : ",") + input.data("label");
					});
					document.getElementById("trg-oficina").value = sseleccion;
				}
			});
			//modal ccosto
			$("#trg-ccosto").on("click", function() {
				$("#modal-ccosto").modal("show");
			});
			$("#ch-cc-all").change(function() {
				$(".ch-cc").prop("checked", document.getElementById("ch-cc-all").checked);
			});
			$(".ch-cc").change(function() {
				$(".ch-cc-all").prop("checked", false);
			});
			$("#modal-ccosto").on("hide.bs.modal", function() {
				arr_ccs = new Array();
				if(document.getElementById("ch-cc-all").checked) {
					arr_ccs = [document.getElementById("ch-cc-all").value];
					document.getElementById("trg-ccosto").value = "Todos";
				}
				else {
					var of_all = $(".ch-cc:checked");
					var sseleccion = "";
					$.each(of_all, function() {
						var input = $(this);
						arr_ccs.push(input.val());
						sseleccion += (sseleccion == "" ? "" : ",") + input.data("label");
					});
					document.getElementById("trg-ccosto").value = sseleccion;
				}
			});
			//modal producto
			$("#trg-producto").on("click", function() {
				$("#modal-producto").modal("show");
			});
			$("#ch-pr-all").change(function() {
				$(".ch-pr").prop("checked", document.getElementById("ch-pr-all").checked);
			});
			$(".ch-pr").change(function() {
				$(".ch-pr-all").prop("checked", false);
			});
			$("#modal-producto").on("hide.bs.modal", function() {
				arr_prds = new Array();
				if(document.getElementById("ch-pr-all").checked) {
					arr_prds = [document.getElementById("ch-pr-all").value];
					document.getElementById("trg-producto").value = "Todos";
				}
				else {
					var pr_all = $(".ch-pr:checked");
					var sseleccion = "";
					$.each(pr_all, function() {
						var input = $(this);
						arr_prds.push(input.val());
						sseleccion += (sseleccion == "" ? "" : ",") + input.data("label");
					});
					document.getElementById("trg-producto").value = sseleccion;
				}
			});
		</script>
	</body>
</html>