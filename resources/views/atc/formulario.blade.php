<!DOCTYPE html>
<html lang="es">
    <head>
        <title>Atención al cliente</title>
		@include("common.styles")
        <style>
            body{background-color:#d0d0d0;padding-top:100px !important}
            .rec-titulo{color:#0192d1}
            #form-reclamo{display:none}
        </style>
    </head>

    <body>
        <!-- -->
		<div class="navigation-bar fixed-top">
            <header class="navbar-light">
                <img src="{{ asset('images/ustar_logo.png') }}">
                <div class="nav-profile">
                    <div class="nav-profile-options">
                        <h3 class="mt-3">Atención al cliente</h3>
                    </div>
                </div>
                <button class="navbar-toggler text-dark" type="button" data-toggle="collapse" data-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </header>
        </div>
        <!-- -->
        <!-- PAGINA -->
        <div class="container">
            <!-- -->
            <div class="row justify-content-center">
                <div class="col-xs-12 col-md-10 col-lg-9">
                    <div class="alert bg-light shadow-sm" id="form-reclamo">
                        <form class="form-horizontal">
                            <h2 class="rec-titulo px-2 mb-3">Ingresar un reclamo</h2>
                            <input type="hidden" id="key">
                            <div class="form-group">
                                <label for="usuario" class="control-label mb-0 font-weight-bold">Usuario:</label>
                                <div class="col-sm-10">
                                    <input type="text" name="consultora" class="form-control-plaintext w-100" id="usuario">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="cliente" class="control-label mb-0 font-weight-bold">Cliente:</label>
                                <div class="col-sm-10">
                                    <input type="text" name="consultora" class="form-control-plaintext w-100" id="cliente">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="ciclo" class="control-label font-weight-bold">Ciclo:</label>
                                <div class="col-sm-4">
                                    <select class="form-control" id="ciclo" name="tRecla">
                                        <option value="0" selected disabled>Seleccione</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="tipo" class="control-label font-weight-bold">Tipo de envío:</label>
                                <div class="col-sm-6">
                                    <select class="form-control" id="tipo" name="tEnvio">
                                        <option value="0" selected disabled>Seleccione</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="motivo" class="control-label font-weight-bold">Motivo de reclamo:</label>
                                <div class="col-sm">
                                    <select class="form-control" id="motivo" name="tRecla">
                                        <option value="0" selected disabled>Seleccione</option>
                                        @foreach($motivos as $motivo)
                                        <option value="{{ $motivo->codigo }}">{{ $motivo->descripcion }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="titulo" class="control-label font-weight-bold">Consultora:</label>
                                <div class="col-sm-4">
                                    <div class="d-flex">
                                        <input type="text" id="consultora" name="consultora" class="form-control my-auto" id="consultora">
                                        <button class="btn btn-primary ml-1 my-auto" id="verificar">Verificar</button>
                                    </div>
                                </div>
                                <div class="w-100 p-2 px-3" id="consultoras"></div>
                            </div>
                            <div id="reclamos-footer" class="form-group">
                                <div class="col-sm-offset-2 col-sm-10">
                                    <button type="submit" class="btn btn-success" id="reclamo-submit">GRABAR</button>
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">CANCELAR</button>
                                </div>
                            </div>
                            <div id="reclamos-loader" class="form-group" style="display:none;">
                                <div class="col-sm-offset-2 col-sm-10">
                                    <p>
                                        <img src="{{ asset('asset/img/loader.gif') }}" style="height:50px;margin:5px;" />
                                        <span style="color:#808080;position:relative;top:15px;">Por favor, espere mientras procesamos su solicitud.</span>
                                    </p>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- modal login -->
        <div class="modal fade" id="modal-login" tabindex="-1" role="dialog" data-backdrop="static">
            <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Identificarse</h5>
                    </div>
                    <div class="modal-body">
                        <div class="form-horizontal m-form-n">
                            <div class="form">
                                <label for="codigo" class="control-label">Código / DNI</label>
                                <input id="codigo" name="codigo" class="form-control" placeholder="Ingrese su código o DNI" />
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button id="login-submit" type="button" class="btn btn-primary">Confirmar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include("common.scripts")
    <script type="text/javascript">
        function validar_login (event) {
            event.preventDefault();
            let comboCiclos = $('#ciclo');
            let comboTipos = $('#tipo');
            comboCiclos.empty();
            comboTipos.empty();
            let data = {
                dni: document.getElementById('codigo').value,
                _token: '{{ csrf_token() }}'
            };
            $.ajax({
                url: '{{ url("atc/ajax/validar-login") }}',
                method: 'post',
                data: data,
                dataType: 'json',
                success: function (result) {
                    let datos = result.datos;
                    $('#modal-login').modal('hide');
                    $('#usuario').val(datos.nombre);
                    $('#cliente').val(datos.nomcliente);
                    $('#form-reclamo').fadeIn(150);
                    $('#key').val(result.key);
                    // llenar combo de ciclos
                    let ciclos = result.ciclos;
                    if (ciclos.length > 1) {
                        comboCiclos.append(
                            $('<option>').val(-1).text('- Seleccione -').prop('disabled',true)
                        );
                    }
                    for (let ciclo of ciclos) {
                        let iciclo = ciclo.ciclo;
                        comboCiclos.append(
                            $('<option>').val(iciclo).text(iciclo)
                        );
                    }
                    comboCiclos.children().eq(0).prop('selected',true);
                    // llenar combo de tipos
                    let tipos = result.tipos;
                    if (tipos.length > 1) {
                        comboTipos.append(
                            $('<option>').val(-1).text('- Seleccione -').prop('disabled',true)
                        );
                    }
                    for (let tipo of tipos) {
                        comboTipos.append(
                            $('<option>').val(tipo.codigo).text(tipo.descripcion)
                        );
                    }
                    comboTipos.children().eq(0).prop('selected',true);
                },
                error: function (error) {
                    console.error(error);
                    alert(error.responseText);
                }
            });
        }
        function quitar_consultora () {
            let i = $(this);
            i.parent().remove();
        }
        function verificar_consultora (event) {
            event.preventDefault();
            let data = {
                key: $('#key').val(),
                ciclo: $('#ciclo').val(),
                vendedor: $('#consultora').val()
            };
            $.ajax({
                url: '{{ url("atc/ajax/verificar-revistas") }}',
                method: 'get',
                data: data,
                dataType: 'json',
                success: function (result) {
                    let datos = result.datos;
                    $('#consultora').val(null);
                    $('#consultoras').append(
                        $('<span>').append(data.vendedor).append(
                            $('<i>').addClass('fa fa-times ml-1').css('cursor','pointer').on('click', quitar_consultora)
                        ).addClass('btn btn-info mr-1').data({
                            autogen: datos.autogen,
                            proceso: datos.proceso,
                            control: datos.control
                        })
                    );
                    document.getElementById('consultora').focus();
                },
                error: function (error) {
                    console.error(error);
                }
            });
        }
        function submit_reclamo (event) {
            event.preventDefault();
            let consultoras = [];
            let spans = $('#consultoras').children('span');
            $.each(spans, function () {
                let span = $(this);
                let idata = [ span.data('autogen'), span.data('proceso'), span.data('control') ].join('@');
                consultoras.push(idata);
            });
            let data = {
                key: $('#key').val(),
                tipo: $('#tipo').val(),
                ciclo: $('#ciclo').val(),
                motivo: $('#motivo').val(),
                data: consultoras.join('|'),
                _token: '{{ csrf_token() }}'
            };
            $.ajax({
                url: '{{ url("atc/ajax/registra-reclamos") }}',
                method: 'post',
                data: data,
                dataType: 'json',
                success: function (result) {
                    alert(result.mensaje);
                    location.reload();
                },
                error: function (error) {
                    console.error(error);
                }
            });
        }

        $('#modal-login').modal('show');
        $('#login-submit').on('click', validar_login);
        $('#verificar').on('click', verificar_consultora);
        $('#reclamo-submit').on('click', submit_reclamo);
        $('#usuario').val(null);
        $('#cliente').val(null);
    </script>
</body>
</html>
