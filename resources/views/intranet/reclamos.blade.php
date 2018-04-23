<!DOCTYPE html>
<html>
	<head>
		<title>Reclamos</title>
		@include("common.styles")
		<link rel="stylesheet" type="text/css" href="{{ asset('css/datepicker.min.css') }}">
		<style type="text/css">
			.table-responsive{max-height:1000px !important;}
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
							<label class="form-control-sm" for="pendientes">
								Pendientes&nbsp;<input type="checkbox" class="form-control form-control-sm" id="pendientes">
							</label>
							<!-- -->
							<label class="form-control-sm" for="procede">
								Procede&nbsp;<input type="checkbox" class="form-control form-control-sm" id="procede">
							</label>
							<!-- -->
							<label class="form-control-sm" for="noprocede">
								No procede&nbsp;<input type="checkbox" class="form-control form-control-sm" id="noprocede">
							</label>
							<!-- -->
							&nbsp;<button id="btn-form" type="button" class="btn btn-success btn-sm"><i class="fa fa-search"></i> Revisar</button>
							&nbsp;&nbsp;&nbsp;<button id="btn-form" type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-edicion" data-mod="I"><i class="fa fa-plus"></i> Nuevo reclamo</button>
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
									<th>Asunto</th>
									<th>Motivo</th>
									<th>Estado</th>
									<th>Registrado</th>
									<th></th>
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
		<!-- modal-edicion -->
		<div id="modal-edicion" class="modal" tabindex="-1" role="dialog">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title">Editar reclamo</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<input type="hidden" id="modal-edicion-mod">
						<input type="hidden" id="modal-edicion-agn">
						<div class="form-group">
							<label for="tpgs">Tipo gestión</label>
							<select id="tpgs" class="form-control">
								<option value="C" selected>Consulta</option>
								<option value="R">Reclamo</option>
								<option value="S">Sugerencia</option>
							</select>
						</div>
						<div class="form-group">
							<label for="mtvo">Motivo gestión</label>
							<select id="mtvo" class="form-control">
								<option value="0" selected disabled>Seleccione</option>
								@foreach($tipos as $tipo)
								<option value="{{ $tipo->codigo }}">{{ $tipo->descripcion }}</option>
								@endforeach
							</select>
						</div>
						<div class="form-group">
							<label for="asnt">Asunto</label>
							<input type="text" class="form-control" id="asnt" placeholder="Ingrese el asunto del reclamo">
						</div>
						<div class="form-group">
							<label for="dscr">Descripción</label>
							<textarea id="dscr" class="form-control" style="resize:none" rows="3"></textarea>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-primary" id="btn-guardar-reclamo"><i class="fa fa-floppy-o"></i> Guardar</button>
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
					</div>
				</div>
			</div>
		</div>
		<!-- JS -->
		@include("common.scripts")
		<script type="text/javascript" src="{{ asset('js/datepicker.min.js') }}"></script>
		<script type="text/javascript">
			$(".datepicker").datepicker({
				autoclose: true,
				format: "dd/mm/yyyy",
				language:"es",
				todayHighlight: true
			});
			//funciones
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
						agn: data.rows[idx].iCodAutogenAtc
					};
					$.post("{{ url('reclamos/ajax/detalle') }}", p, function(response) {
						if(response.state == "success") {
							var rows = response.data.rows;
							//arma la tabla detalle
							var tbody = $("<tbody/>");
							for(var i in rows) {
								var row = rows[i];
								tbody.append(
									$("<tr/>").append(
										$("<td/>").html(row.fecharesultado)
									).append(
										$("<td/>").html(row.detallereclamo)
									).append(
										$("<td/>").html(row.respuesta)
									).append(
										$("<td/>").html(row.estado)
									).append(
										$("<td/>").html(row.registra)
									)
								);
							}
							var table = $("<table/>").addClass("table table-striped table-sm").append(
								$("<thead/>").addClass("bg-success text-light").append(
									$("<tr/>").append(
										$("<th/>").html("Fecha")
									).append(
										$("<th/>").html("Detalle")
									).append(
										$("<th/>").html("Respuesta")
									).append(
										$("<th/>").html("Estado")
									).append(
										$("<th/>").html("Registra")
									)
								)
							).append(tbody);
							//inserta todo el contenido
							var td = tr.children("td").eq(1);
							td.empty().append(
								$("<div/>").addClass("row").append(
									$("<div/>").addClass("col-xs-12 col-md-9 col-6").css("padding","5px").append(
										$("<div/>").addClass("row").append(table)
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
								$("<a/>").addClass("btn btn-info btn-sm").attr({href:"#",role:"button"}).data("idx",i).data("loaded","0").data("state","hidden").html(i + 1).on("click", toggleRow)
							)
						).append(
							$("<td/>").html(fila.fecha)
						).append(
							$("<td/>").html(fila.asunto)
						).append(
							$("<td/>").html(fila.motivo)
						).append(
							$("<td/>").html(fila.estado)
						).append(
							$("<td/>").html(fila.registra)
						).append(
							$("<td/>").append(
								$("<a/>").attr({
									"href": "#",
									"data-estado": fila.estado,
									"data-autogen": fila.iCodAutogenAtc,
									"data-mod": "E",
									"title": "Modificar",
									"data-toggle": "modal",
									"data-target": "#modal-edicion"
								}).addClass("btn btn-primary btn-xs").append(
									$("<i/>").addClass("fa fa-pencil")
								)
							).append("&nbsp;").append(
								$("<a/>").attr({
									"href": "#",
									"data-estado": fila.estado,
									"data-autogen": fila.iCodAutogenAtc,
									"title": "Eliminar"
								}).addClass("btn btn-danger btn-xs").append(
									$("<i/>").addClass("fa fa-remove")
								).on("click", eliminaReclamo)
							).addClass("text-right")
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
			function eliminaReclamo(event) {
				event.preventDefault();
				var a = $(this);
				if(a.data("estado") == "PENDIENTE") {
					if(window.confirm("¿Está seguro de eliminar este reclamo?")) {
						a.hide();
						var p = { _token:"{{ csrf_token() }}", agn:a.data("autogen") };//oliboli
						$.post("{{ url('reclamos/ajax/elimina') }}",p,function(response) {
							if(response.state == "success") $("#btn-form").trigger("click");
							else {
								a.show();
								alert(response.message);
							}
						},"json");
					}
				}
				else alert("Solo puede eliminar un reclamo si su estado es PENDIENTE.");
			}
			function PopulateMotivos(motivos) {
				$("#mtvo").empty();
				for(var i in motivos) {
					var motivo = motivos[i];
					$("#mtvo").append(
						$("<option/>").val(motivo.codigo).html(motivo.descripcion)
					);
				}
			}
			//
			$("#btn-form").on("click", function(event) {
				var a = $(this);
				a.hide();
				$("#loader-busqueda").fadeIn(150);
				var p = {
					_token: "{{ csrf_token() }}",
					dsd: document.getElementById("fdesde").value,
					hst: document.getElementById("fhasta").value,
					pnd: document.getElementById("pendientes").checked ? 1 : 0,
					prc: document.getElementById("procede").checked ? 1 : 0,
					npr: document.getElementById("noprocede").checked ? 1 : 0
				};
				$.post("{{ url('reclamos/ajax/buscar') }}", p, function(response) {
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
			$("#tpgs").on("change", function(event) {
				var p = {
					_token: "{{ csrf_token() }}",
					tpo: document.getElementById("tpgs").value
				};
				$.post("{{ url('reclamos/ajax/combo') }}", p, function(response) {
					PopulateMotivos(response.data.motivos);
				}, "json");
			});
			$("#modal-edicion").on("show.bs.modal", function(args) {
				var mod = args.relatedTarget.dataset.mod;
				var autogen = args.relatedTarget.dataset.autogen;
				document.getElementById("modal-edicion-mod").value = mod;
				document.getElementById("modal-edicion-agn").value = autogen;
				if(mod == "I") {
					$("#modal-edicion .modal-title").html("Nuevo reclamo");
				}
				else {
					$("#modal-edicion .modal-title").html("Editar reclamo");
					//carga los datos del reclamol
					var p = { _token: "{{ csrf_token() }}", agn: autogen };
					$.post("{{ url('reclamos/ajax/dt-reclamo') }}", p, function(response) {
						if(response.state == "success") {
							var reclamo = response.data.reclamo;
							var motivos = response.data.motivos;
							$("#tpgs option[value=" + reclamo.tpg + "]").prop("selected", true);
							PopulateMotivos(motivos);
							$("#mtvo option[value=" + reclamo.mtg + "]").prop("selected", true);
							document.getElementById("asnt").value = reclamo.asg;
							document.getElementById("dscr").value = reclamo.dsg;
						}
						else alert(response.message);
					}, "json");
				}
			});
			$("#modal-edicion").on("hidden.bs.modal", function(args) {
				document.getElementById("modal-edicion-mod").value = "I";
				document.getElementById("modal-edicion-agn").value = "0";
				document.getElementById("asnt").value = "";
				document.getElementById("dscr").value = "";
			});
			$("#btn-guardar-reclamo").on("click", function(event) {
				event.preventDefault();
				var modo = document.getElementById("modal-edicion-mod").value;
				if(modo == "I") {
					var p = {
						_token: "{{ csrf_token() }}",
						tpo: document.getElementById("tpgs").value,
						mtv: document.getElementById("mtvo").value,
						asn: document.getElementById("asnt").value,
						dsc: document.getElementById("dscr").value
					};
					$.post("{{ url('reclamos/ajax/sv-reclamo') }}", p, function(response) {
						if(response.state == "success") {
							alert("Se guardó el reclamo");
							$("#btn-form").trigger("click");
						}
						else alert(response.message);
					}, "json");
				}
				else {
					var p = {
						_token: "{{ csrf_token() }}",
						agn: document.getElementById("modal-edicion-agn").value,
						tpo: document.getElementById("tpgs").value,
						mtv: document.getElementById("mtvo").value,
						asn: document.getElementById("asnt").value,
						dsc: document.getElementById("dscr").value
					};
					$.post("{{ url('reclamos/ajax/upd-reclamo') }}", p, function(response) {
						if(response.state == "success") {
							alert("Se actualizó el reclamo");
							$("#btn-form").trigger("click");
						}
						else alert(response.message);
					}, "json");
				}
			});
			$("#dv-table").children("div").children("div").children("div").css("height", (window.innerHeight - 235) + "px");
			$("#btn-xls").on("click", function(event) {
				event.preventDefault();
				var p = {
					_token: "{{ csrf_token() }}",
					dsd: document.getElementById("fdesde").value,
					hst: document.getElementById("fhasta").value,
					pnd: document.getElementById("pendientes").checked ? 1 : 0,
					prc: document.getElementById("procede").checked ? 1 : 0,
					npr: document.getElementById("noprocede").checked ? 1 : 0
				};
				var loader = window.open("{{ url('export') }}", "_blank", "height=420,scrollbars=no,titlebar=no,toolbar=no,width=540");
				$.post("{{ url('reclamos/ajax/export') }}", p, function(response) {
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
		</script>
	</body>
</html>