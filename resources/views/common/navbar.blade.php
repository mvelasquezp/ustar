
		<div class="navigation-bar fixed-top">
			<header class="navbar-light">
				<img src="{{ asset('images/ustar_logo.png') }}">
				<div class="nav-profile">
					<h1>{{ $opcion }}</h1>
					<img src="{{ asset('images/user-default.png') }}">
					<div class="nav-profile-options">
						<p>Bienvenid@</p>
						<h3>{{ $usuario->v_Nombres }} <a id="caret-menu" href="#"><i class="fa fa-caret-down"></i></a></h3>
						<div class="profile-menu">
							<div class="list-group">
								<a href="#" class="list-group-item list-group-item-secondary"><i class="fa fa-user"></i> Ver perfil</a>
								<a href="{{ url('login/logout') }}" class="list-group-item list-group-item-secondary"><i class="fa fa-sign-out"></i> Cerrar sesión</a>
							</div>
						</div>
					</div>
				</div>
				<button class="navbar-toggler text-dark" type="button" data-toggle="collapse" data-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>
			</header>
			<nav class="navbar navbar-expand-lg navbar-dark bg-ustar">
				<div id="navbar" class="collapse navbar-collapse">
					<ul class="navbar-nav mr-auto">
						<li class="nav-item dropdown d-md-block d-lg-none bg-info">
							<a class="nav-link">UnionStar &gt; {{ $opcion }}</a>
						</li>
						<li class="nav-item dropdown d-md-block d-lg-none">
							<a class="nav-link dropdown-toggle" href="#" id="serviciosDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-user"></i> {{ $usuario->v_Nombres }} {{ $usuario->v_Apellidos }}</a>
							<div class="dropdown-menu" aria-labelledby="serviciosDropdown">
								<a class="dropdown-item" href="{{ url('servicios/distribucion') }}"><i class="fa fa-user-o"></i> Ver perfil</a>
								<a class="dropdown-item" href="{{ url('servicios/almacenes') }}"><i class="fa fa-sign-out"></i> Cerrar sesión</a>
					        </div>
						</li>
						<!-- -->
						<li class="nav-item{{ $menu == 0 ? ' active' : '' }}">
							<a class="nav-link" href="{{ url('/') }}"><i class="fa fa-home"></i> Inicio</a>
						</li>
						<li class="nav-item{{ $menu == 1 ? ' active' : '' }} dropdown">
							<a class="nav-link dropdown-toggle" href="#" id="serviciosDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-list-ul"></i> Mis servicios</a>
							<div class="dropdown-menu" aria-labelledby="serviciosDropdown">
								<a class="dropdown-item" href="{{ url('servicios/distribucion') }}"><i class="fa fa-truck"></i> Distribución</a>
					        </div>
						</li>
						<li class="nav-item{{ $menu == 2 ? ' active' : '' }}">
							<a class="nav-link" href="{{ url('tracking') }}"><i class="fa fa-globe"></i> Tracking</a>
						</li>
						<li class="nav-item{{ $menu == 3 ? ' active' : '' }}">
							<a class="nav-link" href="{{ url('reclamos') }}"><i class="fa fa-gavel"></i> Reclamos</a>
						</li>
						<li class="nav-item{{ $menu == 4 ? ' active' : '' }} dropdown">
							<a class="nav-link dropdown-toggle" href="#" id="indicadorDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bar-chart"></i> Indicadores</a>
							<!-- -->
							<div class="dropdown-menu" aria-labelledby="indicadorDropdown">
								<a class="dropdown-item" href="{{ url('indicadores/d-entregas') }}"><i class="fa fa-paper-plane"></i> Distribución - Entregas</a>
							</div>
						</li>
						@php
						$validacion = DB::table("app_mailing_accesos")
							->where("user_id", $usuario->user_id)
							->where("es_vigente", "S")
							->select("st_reportes as reportes", "st_carga as carga");
						if ($validacion->count() > 0) {
							$validacion = $validacion->first();
							$reportes = strcmp($validacion->reportes, "S") == 0;
							$carga = strcmp($validacion->carga, "S") == 0;
							$validacion = true;
						}
						else {
							$reportes = false;
							$carga = false;
							$validacion = false;
						}
						@endphp
						@if ($validacion)
						<li class="nav-item{{ $menu == 6 ? ' active' : '' }} dropdown">
							<a class="nav-link dropdown-toggle" href="#" id="mailerDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-envelope-o"></i> Mailing</a>
							<div class="dropdown-menu" aria-labelledby="mailerDropdown">
								@if ($carga)
								<a class="dropdown-item" href="{{ route('mailer_upload') }}"><i class="fa fa-cloud-upload"></i> Carga de envíos</a>
								@endif
								@if ($usuario->i_CodCliente == 75)
								<a class="dropdown-item" href="{{ route('mailing_natura') }}"><i class="fa fa-envelope-open"></i> Notificaciones Natura</a>
								@endif
								@if ($reportes)
								<a class="dropdown-item" href="{{ route('mailer_reporte') }}"><i class="fa fa-list"></i> Reporte de envíos</a>
								@endif
					        </div>
						</li>
						@endif
						<!-- delimitador -->
						<li class="nav-item{{ $menu == 7 ? ' active' : '' }} dropdown">
							<a class="nav-link dropdown-toggle" href="#" id="mailerDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-cloud-upload"></i> Reporte de envíos</a>
							<div class="dropdown-menu" aria-labelledby="mailerDropdown">
								<a class="dropdown-item" href="{{ url('masivos') }}"><i class="fa fa-cloud-upload"></i> Envío de datos</a>
								<a class="dropdown-item" href="{{ url('masivos/reporte') }}"><i class="fa fa-list"></i> Reporte</a>
					        </div>
						</li>
						<!-- delimitador -->
						<li class="nav-item{{ $menu == 5 ? ' active' : '' }}">
							<a class="nav-link" href="{{ url('usuarios') }}"><i class="fa fa-users"></i> Usuarios</a>
						</li>
					</ul>
				</div>
			</nav>
		</div>