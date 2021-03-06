<!DOCTYPE html>
<html>
	<head>
		<title>Usuarios</title>
		@include("common.styles")
		<style type="text/css">
			.thead-dark>tr>th{vertical-align:middle}
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
							@if(strcmp($usuario->tp_cliente,'admin') == 0)
							<label class="form-control-sm" for="oficina">Oficina</label>
							<select class="form-control form-control-sm" id="oficina" style="max-width:90px;">
								<option value="0" selected>Todas</option>
								@foreach($ofcs as $idx => $oficina)
								@if($idx > 0)
								<option value="{{ $oficina->codigo }}">{{ $oficina->descripcion }}</option>
								@endif
								@endforeach
							</select>
							<!-- -->
							@else
							<input type="hidden" id="oficina" value="{{ $ofcs[0]->codigo }}">
							@endif
							<label class="form-control-sm" for="filtro-usuario">
								Nombre&nbsp;<input type="text" class="form-control form-control-sm" id="filtro-usuario" placeholder="Ingrese nombre a buscar" style="width:32rem;">
							</label>
							<!-- -->
							&nbsp;<button id="btn-form" type="button" class="btn btn-success btn-sm"><i class="fa fa-search"></i> Buscar</button>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a id="btn-add" href="#" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-insert"><i class="fa fa-plus"></i> Nuevo usuario</a>
						</form>
					</div>
				</div>
			</div>
		</div>
		<div class="container">
			<div class="row">
				<div class="col">
					<table class="table table-striped table-sm">
						<thead class="thead-dark">
							<tr>
								<th width="10%">Código</th>
								<th width="20%">Nombre</th>
								<th width="15%">Estado</th>
								<th width="30%">Perfil</th>
								<th width="20%">Oficina</th>
								<th width="2.5%"></th>
								<th width="2.5%"></th>
							</tr>
						</thead>
						<tbody id="usuarios-tbody"></tbody>
					</table>
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
		<!-- modal nuevo usuario -->
		<div class="modal fade bd-example-modal-lg" id="modal-insert" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel"><i class="fa fa-user-plus text-success"></i> Registro de usuarios</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<form>
							<input type="hidden" id="nu-codcli">
							<div class="row">
								<div class="col-6">
									<div class="form-group">
										<label for="nu-contacto">Contacto</label>
										<select id="nu-contacto" class="form-control form-control-sm">
											<option value="0" selected disabled>Seleccione</option>
											@foreach($contactos as $contacto)
											@if(strcmp($contacto->codigo,"Todos") != 0)
											<option value="{{ $contacto->codigo }}">{{ $contacto->nombre }}</option>
											@endif
											@endforeach
										</select>
									</div>
								</div>
								<div class="col-6">
									<div class="form-group">
										<label for="nu-perfil">Tipo perfil</label>
										<select id="nu-perfil" class="form-control form-control-sm">
											<option value="0" selected disabled>Seleccione</option>
											@foreach($perfiles as $perfil)
											<option value="{{ $perfil->i_CodTipoPerfil }}">{{ $perfil->v_NombrePerfil }}</option>
											@endforeach
										</select>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-3">
									<div class="form-group">
										<label for="nu-dni">DNI</label>
										<input type="text" id="nu-dni" class="form-control form-control-sm" placeholder="Ingrese el DNI">
									</div>
								</div>
								<div class="col-9">
									<div class="form-group">
										<label for="nu-nombre">Nombre</label>
										<input type="text" id="nu-nombre" class="form-control form-control-sm" placeholder="Ingrese nombre completo del nuevo usuario">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-6">
									<div class="form-group">
										<label for="nu-idempleo">Id.Empleado</label>
										<input type="text" id="nu-idempleo" class="form-control form-control-sm" placeholder="Ingrese Id. empleado">
									</div>
								</div>
								<div class="col-6">
									<div class="form-group">
										<label for="nu-email">e-mail</label>
										<input type="text" id="nu-email" class="form-control form-control-sm" placeholder="nombre@servidor.com">
									</div>
								</div>
								<div class="col-6">
									<div class="form-group">
										<label for="nu-telefono">Teléfono</label>
										<input type="text" id="nu-telefono" class="form-control form-control-sm" placeholder="###-###-###">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-4">
									<div class="form-group">
										<label for="nu-alias">Usuario</label>
										<input type="text" id="nu-alias" class="form-control form-control-sm" placeholder="Usuario">
									</div>
								</div>
								<div class="col-4">
									<div class="form-group">
										<label for="nu-clave">Clave</label>
										<input type="password" id="nu-clave" class="form-control form-control-sm" placeholder="Ingresar clave">
									</div>
								</div>
								<div class="col-4">
									<div class="form-group">
										<label for="nu-rclave">Repetir clave</label>
										<input type="password" id="nu-rclave" class="form-control form-control-sm" placeholder="Repetir la clave">
									</div>
								</div>
							</div>
						</form>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
						<button type="button" class="btn btn-primary" id="btn-guardar"><i class="fa fa-floppy-o"></i> Guardar</button>
					</div>
				</div>
			</div>
		</div>
		<!-- modal nuevo usuario -->
		<div class="modal fade bd-example-modal-lg" id="modal-edit" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel"><i class="fa fa-user text-info"></i> Edición de usuario</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<form>
							<input type="hidden" id="ed-codcli">
							<div class="row">
								<div class="col-6">
									<div class="form-group">
										<label for="ed-contacto">Contacto</label>
										<select id="ed-contacto" class="form-control form-control-sm">
											<option value="0" selected disabled>Seleccione</option>
											@foreach($contactos as $contacto)
											@if(strcmp($contacto->codigo,"Todos") != 0)
											<option value="{{ $contacto->codigo }}">{{ $contacto->nombre }}</option>
											@endif
											@endforeach
										</select>
									</div>
								</div>
								<div class="col-6">
									<div class="form-group">
										<label for="ed-perfil">Tipo perfil</label>
										<select id="ed-perfil" class="form-control form-control-sm">
											<option value="0" selected disabled>Seleccione</option>
											@foreach($perfiles as $perfil)
											<option value="{{ $perfil->i_CodTipoPerfil }}">{{ $perfil->v_NombrePerfil }}</option>
											@endforeach
										</select>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-3">
									<div class="form-group">
										<label for="ed-dni">DNI</label>
										<input type="text" id="ed-dni" class="form-control form-control-sm" placeholder="Ingrese el DNI">
									</div>
								</div>
								<div class="col-9">
									<div class="form-group">
										<label for="ed-nombre">Nombre</label>
										<input type="text" id="ed-nombre" class="form-control form-control-sm" placeholder="Ingrese nombre completo del nuevo usuario">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-6">
									<div class="form-group">
										<label for="ed-idempleo">Id.Empleado</label>
										<input type="text" id="ed-idempleo" class="form-control form-control-sm" placeholder="Ingrese Id. empleado">
									</div>
								</div>
								<div class="col-6">
									<div class="form-group">
										<label for="ed-email">e-mail</label>
										<input type="text" id="ed-email" class="form-control form-control-sm" placeholder="nombre@servidor.com">
									</div>
								</div>
								<div class="col-6">
									<div class="form-group">
										<label for="ed-telefono">Teléfono</label>
										<input type="text" id="ed-telefono" class="form-control form-control-sm" placeholder="###-###-###">
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-4">
									<div class="form-group">
										<label for="ed-alias">Usuario</label>
										<input type="text" id="ed-alias" class="form-control form-control-sm" placeholder="Usuario">
									</div>
								</div>
								<div class="col-4">
									<div class="form-group">
										<label for="ed-clave">Clave</label>
										<input type="password" id="ed-clave" class="form-control form-control-sm" placeholder="Ingresar clave">
									</div>
								</div>
								<div class="col-4">
									<div class="form-group">
										<label for="ed-rclave">Repetir clave</label>
										<input type="password" id="ed-rclave" class="form-control form-control-sm" placeholder="Repetir la clave">
									</div>
								</div>
							</div>
						</form>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
						<button type="button" class="btn btn-primary" id="btn-editar"><i class="fa fa-floppy-o"></i> Guardar</button>
					</div>
				</div>
			</div>
		</div>
		<!-- JS -->
		@include("common.scripts")
		<script type="text/javascript">
			var usuarios = {!! json_encode($usuarios) !!};
			document.getElementById("filtro-usuario").value = "";
			$("#oficina option[value=0]").prop("selected", true);
			//
			function activaUsuario(e) {
				e.preventDefault();
				var id = $(this).data("id");
				if(window.confirm("¿Seguro que desea activar al usuario?")) {
					var p = { _token:"{{ csrf_token() }}", uid:id };
					$.post("{{ url('usuarios/ajax/act-usuario') }}", p, function(response) {
						if(response.state == "success") {
							usuarios = response.data;
							CargaUsuarios(document.getElementById("oficina").value, document.getElementById("filtro-usuario").value);
						}
						else alert(response.message);
					}, "json");
				}
			}
			function retiraUsuario(e) {
				e.preventDefault();
				var id = $(this).data("id");
				if(window.confirm("¿Seguro que desea retirar al usuario?")) {
					var p = { _token:"{{ csrf_token() }}", uid:id };
					$.post("{{ url('usuarios/ajax/del-usuario') }}", p, function(response) {
						if(response.state == "success") {
							usuarios = response.data;
							CargaUsuarios(document.getElementById("oficina").value, document.getElementById("filtro-usuario").value);
						}
						else alert(response.message);
					}, "json");
				}
			}
			function CargaUsuarios(oficina, nombre) {
				var tbody = $("#usuarios-tbody").empty();
				for(var i in usuarios) {
					var usuario = usuarios[i];
					if((oficina == 0 || usuario.cod_ofic == oficina) && (nombre == "" || usuario.nombre.toLowerCase().indexOf(nombre.toLowerCase()) > -1)) {
						tbody.append(
							$("<tr/>").append(
								$("<td/>").html(usuario.v_Codusuario)
							).append(
								$("<td/>").html(usuario.nombre)
							).append(
								$("<td/>").append(
									$("<a/>").attr("href","#").addClass("btn btn-xs" + (usuario.v_CodEstado == "Vigente" ? " btn-primary" : " btn-danger")).html(usuario.v_CodEstado)
								)
							).append(
								$("<td/>").html(usuario.v_NombrePerfil)
							).append(
								$("<td/>").html(usuario.NomAreaCliente)
							).append(
								$("<td/>").append(
									$("<a/>").attr({
										"href": "#",
										"data-toggle": "modal",
										"data-target": "#modal-edit",
										"data-id": usuario.v_Codusuario
									}).addClass("btn btn-info btn-xs").append(
										$("<i/>").addClass("fa fa-pencil")
									).append(" Editar")
								)
							).append(
								$("<td/>").append(
									$("<a/>").attr("href","#").addClass("btn btn-xs" + (usuario.v_CodEstado == "Vigente" ? " btn-danger" : " btn-success")).attr("data-id", usuario.v_Codusuario).append(
										$("<i/>").addClass("fa fa-remove")
									).append(usuario.v_CodEstado == "Vigente" ? " Retirar" : " Activar").on("click",usuario.v_CodEstado == "Vigente" ? retiraUsuario : activaUsuario)
								)
							)
						);
					}
				}
			}
			//
			CargaUsuarios(0,"");
			$("#modal-insert").on("show.bs.modal", function() {
				document.getElementById("nu-nombre").value = "";
				document.getElementById("nu-email").value = "";
				document.getElementById("nu-telefono").value = "";
				document.getElementById("nu-dni").value = "";
				document.getElementById("nu-codcli").value = "";
				document.getElementById("nu-idempleo").value = "";
				$("#nu-contacto option[value=0]").prop("selected", true);
				$("#nu-perfil option[value=0]").prop("selected", true);
			});
			$("#nu-contacto").on("change", function(e) {
				var contacto = document.getElementById("nu-contacto").value;
				if(contacto > 0) {
					var p = { _token:"{{ csrf_token() }}",ctc:contacto };
					$.post("{{ url('usuarios/ajax/cmb-contacto') }}", p, function(response) {
						if(response.state == "success") {
							var data = response.data;
							document.getElementById("nu-nombre").value = data.nombre;
							document.getElementById("nu-email").value = data.email;
							document.getElementById("nu-telefono").value = data.telefono;
							document.getElementById("nu-dni").value = data.dni;
							document.getElementById("nu-codcli").value = data.codcli;
						}
					}, "json");
				}
			});
			$("#ed-contacto").on("change", function(e) {
				var contacto = document.getElementById("ed-contacto").value;
				if(contacto > 0) {
					var p = { _token:"{{ csrf_token() }}",ctc:contacto };
					$.post("{{ url('usuarios/ajax/cmb-contacto') }}", p, function(response) {
						if(response.state == "success") {
							var data = response.data;
							document.getElementById("ed-codcli").value = data.codcli;
							if(document.getElementById("ed-nombre").value == "") document.getElementById("ed-nombre").value = data.nombre;
							if(document.getElementById("ed-email").value == "") document.getElementById("ed-email").value = data.email;
							if(document.getElementById("ed-telefono").value == "") document.getElementById("ed-telefono").value = data.telefono;
							if(document.getElementById("ed-dni").value == "") document.getElementById("ed-dni").value = data.dni;
						}
					}, "json");
				}
			});
			$("#btn-guardar").on("click", function(e) {
				e.preventDefault();
				$("#btn-guardar").hide();
				var clave = document.getElementById("nu-clave").value;
				var rclave = document.getElementById("nu-rclave").value;
				if(document.getElementById("nu-contacto").value != 0 && document.getElementById("nu-perfil").value != 0) {
					if(clave == rclave) {
						var p = {
							_token: "{{ csrf_token() }}",
							nom: document.getElementById("nu-nombre").value,
							dni: document.getElementById("nu-dni").value,
							eml: document.getElementById("nu-email").value,
							tlf: document.getElementById("nu-telefono").value,
							eid: document.getElementById("nu-idempleo").value,
							cd1: "",
							cd2: "",
							cd3: "",
							ccl: document.getElementById("nu-codcli").value,
							ctc: document.getElementById("nu-contacto").value,
							psw: clave,
							prf: document.getElementById("nu-perfil").value,
							als: document.getElementById("nu-alias").value
						};
						$.post("{{ url('usuarios/ajax/ins-usuario') }}", p, function(response) {
							if(response.state == "success") {
								usuarios = response.data;
								$("#btn-form").trigger("click");
								$("#modal-insert").modal("hide");
								//
								CargaUsuarios(document.getElementById("oficina").value, document.getElementById("filtro-usuario").value);
							}
							else alert(response.message);
							$("#btn-guardar").show();
						}, "json").fail(function(error) {
							$("#btn-guardar").show();
console.log(error);
						});
					}
					else alert("Las claves deben ser iguales");
				}
				else alert("Seleccione un contacto y perfil válidos");
			});
			$("#btn-form").on("click", function(e) {
				e.preventDefault();
				var oficina = document.getElementById("oficina").value;
				var nombre = document.getElementById("filtro-usuario").value;
				CargaUsuarios(oficina, nombre);
			});
			$("#modal-edit").on("show.bs.modal", function(e) {
				$("#btn-editar").hide();
				var id = e.relatedTarget.dataset.id;
				var p = { _token:"{{ csrf_token() }}",uid:id };
				$.post("{{ url('usuarios/ajax/dt-usuario') }}", p, function(response) {
					if(response.state == "success") {
						var usuario = response.data;
						document.getElementById("ed-nombre").value = usuario.nombres;
						document.getElementById("ed-dni").value = usuario.docid;
						document.getElementById("ed-email").value = usuario.mail;
						document.getElementById("ed-telefono").value = usuario.tlf;
						document.getElementById("ed-idempleo").value = usuario.ipc;
						document.getElementById("ed-codcli").value = usuario.codcli;
						document.getElementById("ed-clave").value = "sapazo";
						document.getElementById("ed-rclave").value = "sapazo";
						document.getElementById("ed-alias").value = id;
						$("#ed-contacto option[value=" + usuario.codcon + "]").prop("selected",true);
						$("#ed-perfil option[value=" + usuario.tperfil + "]").prop("selected",true);
					}
					else alert(response.message);
					$("#btn-editar").show();
				}, "json").fail(function(error) {
					$("#modal-edit").modal("hide");
console.log(error);
				});
			});
			//
			$("#btn-editar").on("click", function(e) {
				e.preventDefault();
				$("#btn-editar").hide();
				var clave = document.getElementById("ed-clave").value;
				var rclave = document.getElementById("ed-rclave").value;
				if(document.getElementById("ed-contacto").value != 0 && document.getElementById("ed-perfil").value != 0) {
					if(clave == rclave) {
						var p = {
							_token: "{{ csrf_token() }}",
							nom: document.getElementById("ed-nombre").value,
							dni: document.getElementById("ed-dni").value,
							eml: document.getElementById("ed-email").value,
							tlf: document.getElementById("ed-telefono").value,
							eid: document.getElementById("ed-idempleo").value,
							ccl: document.getElementById("ed-codcli").value,
							ctc: document.getElementById("ed-contacto").value,
							psw: clave,
							prf: document.getElementById("ed-perfil").value,
							als: document.getElementById("ed-alias").value
						};
						$.post("{{ url('usuarios/ajax/upd-usuario') }}", p, function(response) {
							if(response.state == "success") {
								usuarios = response.data;
								$("#btn-form").trigger("click");
								$("#modal-edit").modal("hide");
							}
							else alert(response.message);
							$("#btn-editar").show();
							//
							CargaUsuarios(document.getElementById("oficina").value, document.getElementById("filtro-usuario").value);
						}, "json").fail(function(error) {
							$("#btn-editar").show();
console.log(error);
						});
					}
					else alert("Las claves deben ser iguales");
				}
				else alert("Seleccione un contacto y perfil válidos");
			});
			//
			$("#modal-edit").on("hidden.bs.modal", function(e) {
				document.getElementById("ed-nombre").value = "";
				document.getElementById("ed-dni").value = "";
				document.getElementById("ed-email").value = "";
				document.getElementById("ed-telefono").value = "";
				document.getElementById("ed-idempleo").value = "";
				document.getElementById("ed-codcli").value = "";
				document.getElementById("ed-contacto").value = "";
				document.getElementById("ed-codcli").value = "";
				document.getElementById("ed-clave").value = "";
				document.getElementById("ed-rclave").value = "";
				document.getElementById("ed-alias").value = "";
			});
		</script>
	</body>
</html>