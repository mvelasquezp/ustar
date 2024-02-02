<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta name="viewport" content="width=device-width, height=device-height, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover, shrink-to-fit=no">
        <meta name="robots" content="index, follow">
        <title>Imágenes - Manifiesto {{ $manifiesto }}</title>
        <script src="{{ asset('spotlight/spotlight.bundle.js') }}"></script>
        <link rel="stylesheet" href="{{ asset('spotlight/style.css') }}">
        <meta name="format-detection" content="telephone=no">
    </head>
    <body>
        <div class="spotlight-group" data-fit="cover" data-autohide="all">
            @foreach($files as $file)
            <a class="spotlight" href="https://wan2.unionstar.com.pe{{ $path }}/{{$file}}">
                <img src="https://wan2.unionstar.com.pe{{ $path }}/{{$file}}">
            </a>
            @endforeach
        </div>
    </body>
</html>