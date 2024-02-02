@extends("usmailer.mail-natura-base")

@section("asunto", "Entrega de pedido Avon/Natura: Aviso de visita")

@section("url-preview")
<a href="{{ url('mailer/preview-natura-entrega') }}?key={{ $key }}" style="color:#ffe0b2">aquí</a>
@endsection

@section("mail-body")
<p style="margin:0 0 16px"><b>Hola, {{ $nombre }}</b></p>
<p style="margin:0 0 16px">Te informamos que tu pedido de {{ $empresa }} con <b>Nro. {{ $pedido }}</b> ha sido entregado.</p>
<p style="margin:0 0 16px">Recuerda que puedes comprobar el estado de tu pedido utilizando el siguiente enlace:</p>
<p style="margin:0 0 40px;padding-top:8px">
    <a href="{{ $url }}" style="background-color:#f2821b;color:#fff;padding:12px;font-size:15px;text-decoration:none">Comprobar estado de mi pedido</a>
</p>
@endsection