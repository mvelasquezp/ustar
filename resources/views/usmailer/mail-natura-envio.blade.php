@extends("usmailer.mail-natura-base")

@section("asunto", "Entrega de pedido Avon/Natura: Aviso de visita")

@section("url-preview")
<a href="{{ url('mailer/preview-natura-envio') }}?key={{ $key }}" style="color:#ffe0b2">aquí</a>
@endsection

@section("mail-body")
<p style="margin:0 0 16px"><b>Hola, {{ $nombre }}</b></p>
<p style="margin:0 0 16px">Te enviamos este correo porque tu pedido de {{ $empresa }} con <b>Nro. {{ $pedido }}</b> se encuentra en camino y será entregado el día de hoy.</p>
<p style="margin:0 0 16px">Nuestro horario de entrega es <b>de 08:00 a 20:00 horas</b>. En caso no te encuentres en tu domicilio, asegúrate que alguien mayor de edad lo pueda recibir.</p>
<p style="margin:0 0 16px">Recuerda que puedes dar seguimiento a tu pedido utilizando el siguiente enlace:</p>
<p style="margin:0 0 40px;padding-top:8px">
    <a href="{{ $url }}" style="background-color:#f2821b;color:#fff;padding:12px;font-size:15px;text-decoration:none">Realizar el seguimiento de mi pedido</a>
</p>
@endsection