<!DOCTYPE html>
<html>
	<head>
		<title>Bienvenido, {{ $usuario->v_Nombres }}</title>
		@include("common.styles")
	</head>
	<body>
		@include("common.navbar")
		<!-- PAGINA -->
		<div class="container-fluid">
			<div class="row">
				<div class="div-banner">
					<h1>Bienvenido a nuestro sitio web</h1>
				</div>
			</div>
		</div>
		<div class="container-fluid bg-orange text-light">
			<div class="row justify-content-md-center">
				<div class="col-12 col-md-8" style="padding-top:10px;">
					<p class="text-justify">En nombre de <b>Union Star EIRL</b>, nos complace saludarlo y darle la más cordial bienvenida a nuestra página web, en la cual esperamos que encuentre toda la información que busca acerca de nosotros.</p>
					<p class="text-justify">El principal objetivo de este medio es darle a conocer lo que somos y hacemos. Ud. podrá encontrar aquí de manera directa, una amplia descripción de nuestros servicios, así como el alcance de los mismos. En esta página también podrá contactarse con nosotros y hacernos llegar sus consultas y comentarios, asegurando su atención de manera directa y oportuna.</p>
				</div>
			</div>
		</div>
		<div class="container-fluid bg-light">
			<div class="row justify-content-md-center">
				<div class="col col-md-8" style="padding-top:10px;">
					<p class="text-center">La calidad de nuestros servicios es prioridad número uno, es por ello que nos esforzamos en asegurarte</p>
				</div>
			</div>
			<div class="row justify-content-md-center">
				<div class="col-6 col-md-4 col-lg-2">
					<p class="text-center text-info" style="margin-bottom:0;"><i class="fa fa-lock fa-3x"></i></p>
					<p class="text-center">Honradez</p>
				</div>
				<div class="col-6 col-md-4 col-lg-2">
					<p class="text-center text-info" style="margin-bottom:0;"><i class="fa fa-clock-o fa-3x"></i></p>
					<p class="text-center">Puntualidad</p>
				</div>
				<div class="col-6 col-md-4 col-lg-2">
					<p class="text-center text-info" style="margin-bottom:0;"><i class="fa fa-tachometer fa-3x"></i></p>
					<p class="text-center">Rapidez</p>
				</div>
				<div class="col-6 col-md-4 col-lg-2">
					<p class="text-center text-info" style="margin-bottom:0;"><i class="fa fa-money fa-3x"></i></p>
					<p class="text-center">Economía</p>
				</div>
			</div>
			<br>
		</div>
		<div class="container-fluid bg-dark text-light">
			<div class="row justify-content-md-center">
				<div class="col col-md-8" style="padding-top:10px;">
					<img src="{{ asset('images/ustar_logo-light.png') }}" style="margin:10px 0">
					<p><i class="fa fa-building-o"></i> Cal. Las Orquídeas 2624 - Urb. San Eugenio</p>
					<p>San Isidro - Lima - Perú</p>
					<p><i class="fa fa-phone"></i> 989 845 561</p>
					<p><i class="fa fa-envelope-o"></i> ventas@unionstar.com.pe</p>
				</div>
			</div>
			<div class="row justify-content-md-center">
				<div class="col">
					<p class="text-center">2017 &copy; Union Star - Todos los derechos reservados</p>
				</div>
			</div>
		</div>
		<!-- JS -->
		@include("common.scripts")
	</body>
</html>