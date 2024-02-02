<!DOCTYPE html>
<html>
	<head>
		<title>Reporte de envíos</title>
		@include("common.styles")
		<link rel="stylesheet" type="text/css" href="{{ asset('css/datepicker.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('datatables/datatables.min.css') }}">
		<style type="text/css">
            .table th,.table td{vertical-align:middle !important}
            #dv-result{display:none}
            #f-export, #f-pdfs{display:none}
            .btn-xs{padding: .125rem 0.5rem;font-size: .75rem;}
		</style>
	</head>
	<body>
		@include("common.navbar")
		<!-- PAGINA -->
		<div class="container mt-2">
            <div class="row justify-content-center">
                <div class="col">
                    <div class="alert alert-secondary text-center mb-3">
                        <div class="form-inline">
                            <div class="form-group mr-2">
                                <label for="f-desde" class="mr-1">Desde</label>
                                <input type="text" id="f-desde" class="form-control form-control-sm datepicker" value="{{ date('d/m/Y') }}" style="width:90px;">
                            </div>
                            <div class="form-group mr-2">
                                <label for="f-hasta" class="mr-1">Hasta</label>
                                <input type="text" id="f-hasta" class="form-control form-control-sm datepicker" value="{{ date('d/m/Y') }}" style="width:90px;">
                            </div>
                            <div class="form-group mr-2">
                                <label for="f-guia" class="mr-1">Guia</label>
                                <input type="text" id="f-guia" class="form-control form-control-sm" placeholder="000-0" style="width:90px;">
                            </div>
                            <div class="form-group mr-2">
                                <label for="f-envio" class="mr-1">Mail enviado</label>
                                <select id="f-envio" class="form-control form-control-sm">
                                    <option value="A">TODO</option>
                                    <option value="S">Si</option>
                                    <option value="N">No</option>
                                </select>
                            </div>
                            <div class="form-group mr-2">
                                <label for="f-leido" class="mr-1">Mail leído</label>
                                <select id="f-leido" class="form-control form-control-sm">
                                    <option value="A">TODO</option>
                                    <option value="S">Si</option>
                                    <option value="N">No</option>
                                </select>
                            </div>
                            <div class="form-group mr-2">
                                <label for="f-carta" class="mr-1">Carta leída</label>
                                <select id="f-carta" class="form-control form-control-sm">
                                    <option value="A">TODO</option>
                                    <option value="S">Leída</option>
                                    <option value="N">Sin leer</option>
                                </select>
                            </div>
                            <div class="form-group mr-2">
                                <label for="f-contrato" class="mr-1">Certificado leído</label>
                                <select id="f-contrato" class="form-control form-control-sm">
                                    <option value="A">TODO</option>
                                    <option value="S">Leído</option>
                                    <option value="N">Sin leer</option>
                                </select>
                            </div>
                            <button id="f-submit" class="btn btn-primary btn-sm"><i class="fa fa-search mr-1"></i>Buscar</button>
                            <a id="f-export" href="#" class="btn btn-success btn-sm ml-2"><i class="fa fa-file-excel-o mr-1"></i>Exportar</a>
                            <a id="f-pdfs" href="#" class="btn btn-danger btn-sm ml-2"><i class="fa fa-file-pdf-o mr-1"></i>Descarga adjuntos</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="dv-result" class="container-fluid">
            <div class="row">
                <div class="col">
                    <div id="dv-table-container">
                        <!--table class="table table-striped table-hover table-responsive">
                            <thead>
                                <tr>
                                    <th rowspan="2" class="text-center">Guía</th>
                                    <th rowspan="2" class="text-center">#Certificado</th>
                                    <th colspan="4" class="text-center">Responsable</th>
                                    <th colspan="2" class="text-center">Envío</th>
                                    <th colspan="2" class="text-center">Lectura</th>
                                    <th colspan="3" class="text-center">Recepción carta</th>
                                    <th colspan="3" class="text-center">Recepción certificado</th>
                                    <th rowspan="2" class="text-center">Observaciones</th>
                                </tr>
                                <tr>
                                    <th>DNI</th>
                                    <th>Nombre</th>
                                    <th>Tipo</th>
                                    <th>e-mail</th>
                                    <th>Fecha/hora</th>
                                    <th>Estado</th>
                                    <th>Fecha/hora</th>
                                    <th>Estado</th>
                                    <th>Fecha/hora</th>
                                    <th>Estado</th>
                                    <th></th>
                                    <th>Fecha/hora</th>
                                    <th>Estado</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody id="reporte-tbody"></tbody>
                        </table-->
                    </div>
                    <div class="alert alert-light text-dark" style="font-size:12px;">
                        <p class="mb-2 font-weight-bold text-uppercase">Envíos totales</p>
                        <div id="dv-envios" class="m-0"></div>
                    </div>
                </div>
            </div>
		</div>
        <!-- modal preview -->
        <div id="modal-preview" class="modal fade" tabindex="-1" role="dialog" data-backdrop="static">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <iframe id="ifr-preview" src="" frameborder="0" style="height:640px;width:100%"></iframe>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light btn-sm" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- modal de confirmacion de guias -->
        <div id="modal-guia" class="modal fade" tabindex="-1" data-backdrop="static">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirmar envío de la guía <span class="mod-guia"></span></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            <input type="hidden" id="f-guia-confirma">
                        </button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-1">Utilice la siguiente opción para dar conformidad a todos los envíos la guía <b class="font-italic mod-guia"></b>.</p>
                        <p class="mb-3">Cuando haya dado la conformidad, se ingresará la guía a la lista de pendientes y se procederá con los envíos por correo electrónico.</p>
                        <p class="mb-1">El próximo envío de correos se realizará a las <b class="mod-hora"></b> horas.</p>
                        <p class="mb-4">Recuerde que, mientras no dé la conformidad a toda la guía, los envíos permanecerán en espera y no serán tomados en cuenta para el siguiente envío de correos.</p>
                        <h4 class="text-primary mb-2">Conformidad de la guía <span class="mod-guia"></span></h4>
                        <label for="mod-conforme" class="mb-1"><input type="checkbox" id="mod-conforme"> ¿La información de los documentos de la guía <b class="mod-guia"></b> es correcta?</label>
                        <p class="mb-1">Recuerde que, una vez confirmada la guía, podrá anular el envío hasta antes del siguiente envío, a las <b class="mod-hora"></b> horas</p>
                    </div>
                    <div class="modal-footer">
                        <a href="#" class="btn btn-sm btn-outline-dark" data-dismiss="modal"><i class="fa fa-times"></i> Cerrar</a>
                        <button id="mod-confirma" class="btn btn-sm btn-outline-success" data-dismiss="modal"><i class="fa fa-check"></i> La guía es conforme</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- modal descarga adjuntos -->
        <div id="modal-adjuntos" class="modal fade" tabindex="-1" data-backdrop="static">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Descargar archivos adjuntos</h5>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col" id="att-div"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="#" class="btn btn-sm btn-outline-dark" data-dismiss="modal"><i class="fa fa-times mr-1"></i>Cerrar</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- modal descarga adjuntos -->
        <div id="modal-conformidad" class="modal fade" tabindex="-1" data-backdrop="static">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Conformidad de guía</h5>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col">
                                <p class="mb-2">Está a punto de dar conformidad a la guía <span id="conf-guia" class="font-weight-bold text-success"></span>. Si lo hace, se procesará <b>TODOS LOS ENVÍOS</b> de la guía correspondiente y se enviará los correos respectivos a cada destinatario. ¿Está seguro de continuar?</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="#" class="btn btn-sm btn-light" data-dismiss="modal"><i class="fa fa-times mr-1"></i>Cerrar</a>
                        <a href="#" class="btn btn-sm btn-success" id="conf-submit"><i class="fa fa-check mr-1"></i>Dar conformidad</a>
                    </div>
                </div>
            </div>
        </div>
		<!-- JS -->
		@include("common.scripts")
		<script type="text/javascript" src="{{ asset('js/datepicker.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('datatables/datatables.min.js') }}"></script>
		<script type="text/javascript">
            let MapTotales;
            let ArrConformidad;
			$('.datepicker').datepicker({
				autoclose: true,
				format: 'dd/mm/yyyy',
				language: 'es',
				todayHighlight: true
			});
            function verCarta(event) {
                event.preventDefault();
                let a = $(this);
                let url = '{{ url("mailer/usdocs/prev-carta") }}?token=' + a.data('token');
                $('#ifr-preview').attr('src', url);
                $('#modal-preview').modal('show');
            }
            function verCertificado(event) {
                event.preventDefault();
                let a = $(this);
                let url = '{{ url("mailer/usdocs/prev-contrato") }}?token=' + a.data('token');
                $('#ifr-preview').attr('src', url);
                $('#modal-preview').modal('show');
            }
            function confirmar_envio_guia(event) {
                event.preventDefault();
                let guia = $(this).data('guia');
                document.getElementById('f-guia-confirma').value = guia;
                document.getElementById('mod-conforme').checked = false;
                $('#mod-confirma').prop('disabled',true).addClass('disabled');
                $('.mod-guia').text(guia);
                $('#modal-guia').modal('show');
                $('.mod-hora').text('22:00');
            }
            function enviar_conformidad (event) {
                $('#conf-submit').addClass('disabled').prop('disabled', true);
                let guia = $('#conf-submit').data('guia');
                $.ajax({
                    url: '{{ url("mailer/conformidad-guia") }}',
                    method: 'patch',
                    data: {
                        guia: guia,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function (result) {
                        if (result.state == 'ok') {
                            $('#f-submit').trigger('click');
                            $('#modal-conformidad').modal('hide');
                        }
                    },
                    error: function (error) {
                        alert('Ocurrió un error');
                        console.log(error);
                    }
                });
            }
            function dar_conformidad (event) {
                event.preventDefault();
                let guia = $(this).data('guia');
                $('#conf-guia').text(guia);
                $('#conf-submit').data('guia',guia);
                $('#conf-submit').removeClass('disabled')
                $('#conf-submit').removeAttr('disabled');
                $('#modal-conformidad').modal('show');
            }
            function escribir_lista_envios(envios) {
                let tbody = $('<tbody>');
                MapTotales = new Map();
                // llenar el detalle
                ArrConformidad = [];
                for (let envio of envios) {
                    // actualiza el totalizador
                    if (MapTotales.has(envio.guia)) {
                        MapTotales.set(envio.guia, MapTotales.get(envio.guia) + 1);
                    }
                    else {
                        MapTotales.set(envio.guia, 1);
                    }
                    let spanGuia = $('<p>').text(envio.guia).addClass('mb-0').css('width','80px');
                    if (envio.esguia == 'En espera') {
                        spanGuia.append(
                            $('<i>').addClass('ml-1 text-danger fa fa-exclamation-triangle').attr({
                                title: 'Guía pendiente de envío',
                                'data-guia': envio.guia
                            }).css('cursor','pointer').on('click', confirmar_envio_guia)
                        );
                    }
                    let thGuia = $('<th>').append(spanGuia);
                    let tdConformidad = $('<td>');
                    if (envio.conforme == 'S') {
                        tdConformidad.append(
                            $('<span>').text('Conforme').addClass('btn btn-xs btn-success')
                        );
                    }
                    else {
                        ArrConformidad.push(envio.guia);
                        tdConformidad.append(
                            $('<span>').text('Pendiente').addClass('btn btn-xs btn-warning mb-1 d-inline-block')
                        );
                    }
                    let detenvio;
                    if (envio.esreenvio == 'S') {
                        detenvio = $('<div>');
                        detenvio.append(
                            $('<p>').append(
                                $('<b>').text('1:').addClass('mr-1')
                            ).append(envio.envio).addClass('mb-0 text-nowrap')
                        ).append(
                            $('<p>').append(
                                $('<b>').text('2:').addClass('mr-1')
                            ).append(envio.fereenvio).addClass('mb-0 text-nowrap')
                        );
                    }
                    else detenvio = envio.envio;
                    tbody.append(
                        $('<tr>').append(thGuia).append(tdConformidad.addClass('text-center')).append(
                            $('<td>').text(envio.certificado)
                        ).append(
                            $('<td>').text(envio.dni)
                        ).append(
                            $('<td>').append(
                                $('<p>').text(envio.nombre).addClass('mb-0').css('width','200px')
                            )
                        ).append(
                            $('<td>').text(envio.tipo)
                        ).append(
                            $('<td>').text(envio.email)
                        ).append(
                            $('<td>').append(detenvio)
                        ).append(
                            $('<td>').append(
                                $('<i>').addClass('fa fa-' + (envio.esenvio == 'S' ? 'check text-success' : (envio.esguia == 'En espera' ? 'times text-danger' : 'clock-o text-primary')))
                            )
                        ).append(
                            $('<td>').text(envio.feleido)
                        ).append(
                            $('<td>').append(
                                $('<i>').addClass('fa fa-' + (envio.esleido == 'S' ? 'check text-success' : 'times text-danger'))
                            )
                        ).append(
                            $('<td>').text(envio.fecarta)
                        ).append(
                            $('<td>').append(
                                $('<i>').addClass('fa fa-' + (envio.escarta == 'S' ? 'check text-success' : 'times text-danger'))
                            )
                        ).append(
                            $('<td>').append(
                                $('<a>').append(
                                    $('<i>').addClass('fa fa-eye')
                                ).attr({
                                    'href': '#',
                                    'data-token': envio.token,
                                    'title': 'Ver carta'
                                }).addClass('btn btn-xs btn-danger').on('click', verCarta)
                            )
                        ).append(
                            $('<td>').text(envio.fecontrato)
                        ).append(
                            $('<td>').append(
                                envio.escontrato == 'x' ?
                                '-' :
                                $('<i>').addClass('fa fa-' + (envio.escontrato == 'S' ? 'check text-success' : 'times text-danger'))
                            )
                        ).append(
                            $('<td>').append(
                                envio.escontrato == 'x' ?
                                '-' :
                                $('<a>').append(
                                    $('<i>').addClass('fa fa-eye')
                                ).attr({
                                    'href': '#',
                                    'data-token': envio.token,
                                    'title': 'Ver certificado'
                                }).addClass('btn btn-xs btn-danger').on('click', verCertificado)
                            )
                        ).append(
                            $('<td>').text(envio.observaciones)
                        )
                    );
                }
                // crea la tabla
                let table = $('<table>');
/*
<!--table class="">
    <thead>
        <tr>
            <th rowspan="2" class="text-center">Guía</th>
            <th rowspan="2" class="text-center">#Certificado</th>
            <th colspan="4" class="text-center">Responsable</th>
            <th colspan="2" class="text-center">Envío</th>
            <th colspan="2" class="text-center">Lectura</th>
            <th colspan="3" class="text-center">Recepción carta</th>
            <th colspan="3" class="text-center">Recepción certificado</th>
            <th rowspan="2" class="text-center">Observaciones</th>
        </tr>
        <tr>
            <th>DNI</th>
            <th>Nombre</th>
            <th>Tipo</th>
            <th>e-mail</th>
            <th>Fecha/hora</th>
            <th>Estado</th>
            <th>Fecha/hora</th>
            <th>Estado</th>
            <th>Fecha/hora</th>
            <th>Estado</th>
            <th></th>
            <th>Fecha/hora</th>
            <th>Estado</th>
            <th></th>
        </tr>
    </thead>
    <tbody id="reporte-tbody"></tbody>
</table-->
*/
                table.append(
                    $('<thead>').append(
                        $('<tr>').append(
                            $('<th>').text('Guía').attr('rowspan',2)
                        ).append(
                            $('<th>').text('Conformidad').attr('rowspan',2)
                        ).append(
                            $('<th>').text('#Certificado').attr('rowspan',2)
                        ).append(
                            $('<th>').text('Responsable').attr('colspan',4)
                        ).append(
                            $('<th>').text('Envío').attr('colspan',2)
                        ).append(
                            $('<th>').text('Lectura').attr('colspan',2)
                        ).append(
                            $('<th>').text('Letura adjunto 1').attr('colspan',3)
                        ).append(
                            $('<th>').text('Letura adjunto 2').attr('colspan',3)
                        ).append(
                            $('<th>').text('Observaciones').attr('rowspan',2)
                        )
                    ).append(
                        $('<tr>').append(
                            $('<th>').text('DNI')
                        ).append(
                            $('<th>').text('Nombre')
                        ).append(
                            $('<th>').text('Tipo')
                        ).append(
                            $('<th>').text('e-mail')
                        ).append(
                            $('<th>').text('Fecha/hora')
                        ).append(
                            $('<th>').text('Estado')
                        ).append(
                            $('<th>').text('Fecha/hora')
                        ).append(
                            $('<th>').text('Estado')
                        ).append(
                            $('<th>').text('Fecha/hora')
                        ).append(
                            $('<th>').text('Estado')
                        ).append(
                            $('<th>').text('')
                        ).append(
                            $('<th>').text('Fecha/hora')
                        ).append(
                            $('<th>').text('Estado')
                        ).append(
                            $('<th>').text('')
                        )
                    )
                ).append(tbody).addClass('table table-striped table-hover table-responsive').attr('id','tabla-detalle');
                $('#dv-table-container').empty().append(table);
                $('#tabla-detalle').DataTable({
                    order: false,
                    language: {
                        url: '{{ asset("datatables/es-mx.json") }}',
                        searchPlaceholder: '#Certificado, DNI, nombres, etc.'
                    },
                    lengthMenu: [[50, 100, -1], [50, 100, 'All']]
                });
                // llenar el resumen
                $('#dv-envios').empty();
                /*
                .append(
                    $('<br>')
                )
                */
                for (let [key,value] of MapTotales) {
                    let lresumen = $('<div>').append(
                        $('<p>').html('<b>Guía ' + key + '</b>: ' + value.toLocaleString('en-us') + ' items').addClass('my-auto mr-auto')
                    ).addClass('mb-1 d-flex');
                    if (ArrConformidad.indexOf(key) > -1) {
                        lresumen.append(
                            $('<a>').addClass('btn btn-xs btn-success').text('Dar conformidad').prepend(
                                $('<i>').addClass('fa fa-check mr-1')
                            ).attr('href','#').data('guia', key).on('click', dar_conformidad)
                        )
                    }
                    else {
                        lresumen.append(
                            $('<a>').addClass('btn btn-xs btn-light disabled').text('Guía conforme').prepend(
                                $('<i>').addClass('fa fa-check mr-1')
                            ).attr('href','javascript:void(0)').prop('disabled',true)
                        )
                    }
                    $('#dv-envios').append(lresumen).css('width', '400px');
                }
            }
            function habilitar_boton() {
                if (document.getElementById('mod-conforme').checked) {
                    $('#mod-confirma').removeAttr('disabled').removeClass('disabled');
                }
                else {
                    $('#mod-confirma').prop('disabled',true).addClass('disabled');
                }
            }
            function carga_reporte_envios() {
                $('#dv-result').fadeIn('250');
                $('#dv-envios').empty();
                $('#reporte-tbody').empty().append(
                    $('<tr>').append(
                        $('<td>').append(
                            $('<p>').text('Cargando la lista de envíos. Por favor, espere...').addClass('text-scondary mb-0').css('width','1280px')
                        ).attr('colspan',17)
                    )
                );
                $.ajax({
                    url: '{{ url("mailer/reporte-envios") }}',
                    method: 'get',
                    data: {
                        desde: document.getElementById('f-desde').value,
                        hasta: document.getElementById('f-hasta').value,
                        guia: document.getElementById('f-guia').value,
                        envio: document.getElementById('f-envio').value,
                        leido: document.getElementById('f-leido').value,
                        carta: document.getElementById('f-carta').value,
                        contrato: document.getElementById('f-contrato').value
                    },
                    dataType: 'json',
                    success: function (result) {
                        $('#f-export').show();
                        $('#f-pdfs').show();
                        if (result.error) {
                            alert(result.error);
                            return;
                        }
                        escribir_lista_envios(result.envios);
                    },
                    error: function (error) {
                        alert(error);
                    }
                });
            }
            function buscar_envios(event) {
                event.preventDefault();
                carga_reporte_envios();
            }
            function exportarPdfs (event) {
                event.preventDefault();
                $('#att-div').empty().append(
                    $('<div>').append(
                        $('<div>').append(
                            $('<img>').attr('src', '{{ asset("images/ustar-loader.svg") }}').addClass('my-auto mr-2').css('height',32)
                        ).append(
                            $('<p>').text('Por favor, espere mientras se prepara su descarga').addClass('my-auto text-dark')
                        ).addClass('d-flex')
                    )
                );
                $('#modal-adjuntos').modal('show');
                let desde = document.getElementById('f-desde').value;
                let hasta = document.getElementById('f-hasta').value;
                let guia = document.getElementById('f-guia').value;
                let envio = document.getElementById('f-envio').value;
                let leido = document.getElementById('f-leido').value;
                let carta = document.getElementById('f-carta').value;
                let contrato = document.getElementById('f-contrato').value;
                $.ajax({
                    url: '{{ url("mailer/usdocs/exportar-pdfs") }}',
                    method: 'post',
                    data: {
                        desde: desde,
                        hasta: hasta,
                        guia: guia,
                        envio: envio,
                        leido: leido,
                        carta: carta,
                        contrato: contrato,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function (result) {
                        let url = '{{ url("mailer/usdocs/descarga-pdfs") }}?name=' + result.folder_name;
                        $('#att-div').empty().append(
                            $('<div>').append(
                                $('<p>').text('Su archivo está listo. Pulse el siguiente botón para descargarlo:').addClass('my-auto text-dark')
                            ).append(
                                $('<a>').append(
                                    $('<i>').addClass('fa fa-download')
                                ).attr('href', url).addClass('btn btn-success my-auto')
                            ).addClass('d-flex')
                        );
                        console.log('descargar:', url);
                    }
                });
            }
            function exportarXlsx(event) {
                event.preventDefault();
                let desde = document.getElementById('f-desde').value;
                let hasta = document.getElementById('f-hasta').value;
                let guia = document.getElementById('f-guia').value;
                let envio = document.getElementById('f-envio').value;
                let leido = document.getElementById('f-leido').value;
                let carta = document.getElementById('f-carta').value;
                let contrato = document.getElementById('f-contrato').value;
                let query = 'desde=' + encodeURIComponent(desde) + '&hasta=' + encodeURIComponent(hasta) + '&envio=' + envio + '&leido=' + leido + '&carta=' + carta + '&contrato=' + contrato + '&guia=' + guia;
                window.open('{{ url("mailer/exporta-reporte") }}?' + query, '_blank');
            }
            function actualizar_guia(event) {
                event.preventDefault();
                let guia = document.getElementById('f-guia-confirma').value;
                $.ajax({
                    url: '{{ url("mailer/confirma-guia") }}',
                    data: {
                        guia: guia,
                        _token: '{{ csrf_token() }}'
                    },
                    method: 'post',
                    dataType: 'json',
                    success: function(result) {
                        if (result.error) {
                            alert(result.error);
                            return;
                        }
                        alert('La guía ' + guia + ' fue confirmada y se procesará en el siguiente envío.');
                        carga_reporte_envios();
                    }
                });
            }
            $('#f-submit').on('click', buscar_envios);
            $('#mod-conforme').on('click', habilitar_boton);
            $('#mod-confirma').on('click', actualizar_guia);
            $('#f-export').on('click', exportarXlsx);
            $('#f-pdfs').on('click', exportarPdfs);
            $('#conf-submit').on('click', enviar_conformidad);
		</script>
	</body>
</html>