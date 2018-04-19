<!DOCTYPE html>
<html>
	<head>
		<title>Generando documento</title>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="{{ asset('css/opensans.css') }}">
		<style type="text/css">
			*{box-sizing:border-box;margin:0;padding:0}
			body{background-color:#fbfbfb;font-family:'Open Sans';height:100%;padding:25px;width:100%}
			.img-main{display:block;height:128px;margin:0 auto;width:128px}
			h3{color:#1565c0;margin-bottom:5px}
			p{font-size:0.9rem}
		</style>
	</head>
	<body>
		<img src="{{ asset('images/icons/exporting.svg') }}" class="img-main">
		<div class="text-box">
			<h3>Generando su archivo</h3>
			<p>Por favor, espere mientras preparamos el reporte. La descarga comenzará automáticamente cuando el archivo esté listo.</p>
		</div>
		<!-- JS -->
		<script type="text/javascript" src="{{ asset('jquery.min.js') }}"></script>
	</body>
</html>