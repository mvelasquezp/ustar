
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
								<!--div class="dropdown-divider"></div-->
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
								<a class="dropdown-item" href="{{ url('servicios/almacenes') }}"><i class="fa fa-archive"></i> Almacenes</a>
								<!--div class="dropdown-divider"></div-->
					        </div>
						</li>
						<li class="nav-item{{ $menu == 2 ? ' active' : '' }}">
							<a class="nav-link" href="{{ url('tracking') }}"><i class="fa fa-globe"></i> Tracking</a>
						</li>
						<li class="nav-item{{ $menu == 3 ? ' active' : '' }}">
							<a class="nav-link" href="{{ url('reclamos') }}"><i class="fa fa-gavel"></i> Reclamos</a>
						</li>
						<li class="nav-item{{ $menu == 4 ? ' active' : '' }}">
							<a class="nav-link" href="{{ url('indicadores') }}"><i class="fa fa-bar-chart"></i> Indicadores</a>
						</li>
						<li class="nav-item{{ $menu == 5 ? ' active' : '' }}">
							<a class="nav-link" href="{{ url('usuarios') }}"><i class="fa fa-users"></i> Usuarios</a>
						</li>
					</ul>
				</div>
			</nav>
		</div>