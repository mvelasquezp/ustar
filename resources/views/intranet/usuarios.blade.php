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
								<option value="" selected>Todas</option>
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
								<th>Código</th>
								<th>Nombre</th>
								<th>Estado</th>
								<th>Perfil</th>
								<th>Oficina</th>
							</tr>
						</thead>
						<tbody>
							@foreach($usuarios as $idx => $usuario)
							<tr>
								<td>{{ $usuario->v_Codusuario }}</td>
								<td>{{ $usuario->nombre }}</td>
								<td>{{ $usuario->v_CodEstado }}</td>
								<td>{{ $usuario->v_NombrePerfil }}</td>
								<td>{{ $usuario->NomAreaCliente }}</td>
							</tr>
							@endforeach
						</tbody>
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
						<h5 class="modal-title" id="exampleModalLabel">Registro de usuarios</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<form>
							<div class="row">
								<div class="col-9">
									<div class="form-group">
										<label for="nu-contacto">Contacto</label>
										<select id="nu-contacto" class="form-control form-control-sm">
											@foreach($contactos as $contacto)
											@if(strcmp($contacto->codigo,"Todos") != 0)
											<option value="{{ $contacto->codigo }}">{{ $contacto->nombre }}</option>
											@endif
											@endforeach
										</select>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="form-group">
									<label for=""></label>
								</div>
							</div>
						</form>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						<button type="button" class="btn btn-primary">Guardar</button>
					</div>
				</div>
			</div>
		</div>
		<!-- JS -->
		@include("common.scripts")
	</body>
</html>