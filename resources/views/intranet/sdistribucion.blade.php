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
							@if(strcmp($usuario->tp_cliente,'admin') == 0)
							<label class="form-control-sm" for="oficina">Oficina</label>
							<select class="form-control form-control-sm" id="oficina" style="max-width:90px;">
								@foreach($ofcs as $oficina)
								<option value="{{ $oficina->codigo }}">{{ $oficina->descripcion }}</option>
								@endforeach
							</select>
							<!-- -->
							<label class="form-control-sm" for="ccosto">C. Costo</label>
							<select  id="ccosto" style="display:none;">
								@foreach($ccts as $ccosto)
								<option value="{{ $ccosto->codigo }}">{{ $ccosto->descripcion }}</option>
								@endforeach
							</select>
							<input type="text" id="trg-ccosto" class="form-control form-control-sm" placeholder="Seleccione" style="width:8em;">
							<!--div class="vmsl-container">
								<ul>
									<li><label><input type="checkbox" id="trg-ccosto-chb-all" value="0"> Seleccionar todo</label></li>
									@foreach($ccts as $ccosto)
									<li><label><input type="checkbox" name="trg-ccosto-chb" value="{{ $ccosto->codigo }}"> {{ $ccosto->descripcion }}</label></li>
									@endforeach
								</ul>
							</div-->
							@else
							<input type="hidden" id="oficina" value="{{ $ofcs[0]->codigo }}">
							<input type="hidden" id="ccosto" value="{{ $ccts[0]->codigo }}">
							@endif
							<!-- -->
							<label class="form-control-sm" for="producto">Producto</label>
							<select class="form-control form-control-sm" id="producto" style="max-width:90px;">
								@foreach($prds as $producto)
								<option value="{{ $producto->codigo }}">{{ $producto->descripcion }}</option>
								@endforeach
							</select>
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
					ofc: document.getElementById("oficina").value,
					ccs: document.getElementById("ccosto").value,
					prd: document.getElementById("producto").value,
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
			//funcion para calcular la posicion del control
			function getOffset( el ) {
			    var _x = 0;
			    var _y = 0;
			    while( el && !isNaN( el.offsetLeft ) && !isNaN( el.offsetTop ) ) {
			        _x += el.offsetLeft - el.scrollLeft;
			        _y += el.offsetTop - el.scrollTop;
			        el = el.offsetParent;
			    }
			    return { top: _y, left: _x };
			}
			//insertar el multi select
			var pst = getOffset(document.getElementById("trg-ccosto"));
			var filas = {!! json_encode($ccts) !!};
			console.log(pst);
			var ul = $("<ul/>");
			for(var i in filas) {
				var fila = filas[i];
				if(i == 0) {
					ul.append(
						$("<li/>").append(
							$("<label/>").append(
								$("<input/>").attr({type:"checkbox",value:fila.codigo,id:"trg-ccosto-chb-all"})
							).append(" Seleccionar todo")
						)
					)
				}
				else {
					ul.append(
						$("<li/>").append(
							$("<label/>").append(
								$("<input/>").attr({type:"checkbox",value:fila.codigo,name:"trg-ccosto-chb"})
							).append(" " + fila.descripcion)
						)
					)
				}
			}
			var div = $("<div/>").addClass("vmsl-container").append(ul).css({
				"top": (pst.top - 114 + 25) + "px",
				"left": (pst.left - 225) + "px"
			});
			$(div).insertAfter($("#trg-ccosto"));
		</script>
	</body>
</html>