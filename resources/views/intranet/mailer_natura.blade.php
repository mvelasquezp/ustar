<!DOCTYPE html>
<html>
	<head>
		<title>Reporte de envíos</title>
		@include("common.styles")
		<link rel="stylesheet" type="text/css" href="{{ asset('css/datepicker.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('datatables/datatables.min.css') }}">
		<style type="text/css">
            #dv-result, #f-envia-mails {display:none}
            .table th,.table td{vertical-align:middle !important}
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
                        <div class="d-flex">
                            <label for="f-desde" class="mr-1 my-auto">Desde</label>
                            <input type="text" id="f-desde" class="form-control form-control-sm datepicker mr-3 my-auto" value="{{ date('d/m/Y') }}" style="width:90px;">
                            <!-- -->
                            <label for="f-hasta" class="mr-1 my-auto">Hasta</label>
                            <input type="text" id="f-hasta" class="form-control form-control-sm datepicker mr-3 my-auto" value="{{ date('d/m/Y') }}" style="width:90px;">
                            <!-- -->
                            <button id="f-submit" class="btn btn-primary btn-sm my-auto"><i class="fa fa-search mr-1"></i>Buscar</button>
                            <button id="f-envia-mails" class="btn btn-success btn-sm ml-auto my-auto" disabled><i class="fa fa-paper-plane mr-1"></i>Enviar correos</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col" id="dv-table-container" style="font-size:12px"></div>
            </div>
        </div>
        <div id="dv-result" class="container-fluid mt-4">
            <div class="row justify-content-center">
                <div class="col-xs-12 col-md-9 col-lg-6">
                    <div id="dv-loader" class="alert text-dark" style="font-size:12px;background-color:#f4f4f4">
                        <div class="d-flex">
                            <img src="{{ asset('images/icons/ic-loader-green.svg') }}" width="32" class="my-auto mr-1">
                            <p class="my-auto">Cargando xd</p>
                        </div>
                    </div>
                </div>
            </div>
		</div>
        <!-- modal ok -->
        <div id="modal-ok" class="modal fade" tabindex="-1" data-backdrop="static">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col">
                                <p class="mb-2">Se completó el envío de los correos electrónicos correspondientes a los envíos seleccionados.</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="#" class="btn btn-sm btn-success" data-dismiss="modal"><i class="fa fa-times mr-1"></i>OK</a>
                    </div>
                </div>
            </div>
        </div>
		<!-- JS -->
		@include("common.scripts")
		<script type="text/javascript" src="{{ asset('js/datepicker.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('datatables/datatables.min.js') }}"></script>
		<script type="text/javascript">
            let TablaEnvios;

            function escribir_lista_envios(envios) {
                let tbody = $('<tbody>');
                // arma el tbody
                for (let envio of envios) {
                    let iclase, icolor, ichbox;
                    if (envio.enviado == 'S') {
                        ichbox = null;
                        iclase = 'envelope-open';
                        icolor = 'success';
                    }
                    else {
                        ichbox = $('<input>').attr('type', 'checkbox').on('click', validar_chboxes).val(envio.key);
                        iclase = 'envelope';
                        icolor = 'danger';
                    }
                    tbody.append(
                        $('<tr>').append(
                            $('<td>').append(
                                $('<i>').addClass('fa fa-' + iclase + ' text-' + icolor)
                            )
                        ).append(
                            $('<td>').append(ichbox).addClass('text-center')
                        ).append(
                            $('<td>').text(envio.guia)
                        ).append(
                            $('<td>').text(envio.empresa)
                        ).append(
                            $('<td>').text(envio.agente).addClass('text-nowrap')
                        ).append(
                            $('<td>').text(envio.destinatario).addClass('text-nowrap')
                        ).append(
                            $('<td>').text(envio.ciudad).addClass('text-nowrap')
                        ).append(
                            $('<td>').text(envio.direccion).addClass('text-nowrap')
                        )
                    );
                }
                // crea la tabla
                $('#dv-table-container').append(
                    $('<table>').append(
                        $('<thead>').append(
                            $('<tr>').append(
                                $('<th>')
                            ).append(
                                $('<th>').append(
                                    $('<div>').append(
                                        $('<input>').attr({
                                            type: 'checkbox',
                                            id: 'ch-all'
                                        }).addClass('my-auto mr-1').on('click', ch_all)
                                    ).append(
                                        $('<label>').text('Todo').addClass('my-auto').attr('for','ch-all')
                                    ).addClass('d-flex')
                                )
                            ).append(
                                $('<th>').text('Guía')
                            ).append(
                                $('<th>').text('Empresa')
                            ).append(
                                $('<th>').text('Agente')
                            ).append(
                                $('<th>').text('Destinatario')
                            ).append(
                                $('<th>').text('Distrito')
                            ).append(
                                $('<th>').text('Dirección')
                            )
                        )
                    ).append(tbody).addClass('table table-striped table-hover table-responsive').attr('id','tabla-detalle')
                );
                TablaEnvios = $('#tabla-detalle').DataTable({
                    columnDefs: [
                        { targets: [0,1], orderable: false }
                    ],
                    order: false,
                    language: {
                        url: '{{ asset("datatables/es-mx.json") }}',
                        searchPlaceholder: 'Nombre, ciudad, guía, etc.'
                    },
                    lengthMenu: [[50, 100, -1], [50, 100, 'All']]
                });
            }
            function carga_lista_envios() {
                $('#dv-table-container').empty();
                $('#dv-loader>div>p').text('Cargando lista de envíos. Por favor, espere un momento...');
                $('#dv-result').fadeIn(250);
                $('#reporte-tbody').empty().append(
                    $('<tr>').append(
                        $('<td>').append(
                            $('<p>').text('Cargando la lista de envíos. Por favor, espere...').addClass('text-scondary mb-0').css('width','1280px')
                        ).attr('colspan',17)
                    )
                );
                $.ajax({
                    url: '{{ url("mailer/lista-envios-natura") }}',
                    method: 'get',
                    data: {
                        desde: document.getElementById('f-desde').value,
                        hasta: document.getElementById('f-hasta').value
                    },
                    dataType: 'json',
                    success: function (result) {
                        if (result.error) {
                            alert(result.error);
                            return;
                        }
                        escribir_lista_envios(result.envios);
                        $('#f-envia-mails').fadeIn(250);
                    },
                    error: function (error) {
                        alert(error);
                    },
                    complete: function () {
                        $('#dv-result').fadeOut(250);
                    }
                });
            }
            function buscar_envios(event) {
                event.preventDefault();
                carga_lista_envios();
            }
            function ch_all () {
                let checked = document.getElementById('ch-all').checked;
                TablaEnvios.rows({filter: 'applied'}).every(function (rowIdx, tableLoop, rowLoop) {
                    /*
                    let data = this.data();
                    let row = $(this.node());
                    let chbox = row.find('input[type=checkbox]');
                    console.log(data, chbox.prop('checked'));
                    */
                    let row = $(this.node());
                    row.find('input[type=checkbox]').prop('checked', checked);
                });
                if (checked) $('#f-envia-mails').removeAttr('disabled');
                else $('#f-envia-mails').prop('disabled', true);
            }
            function chboxes_seleccionados () {
                let marcados = 0;
                TablaEnvios.rows({filter: 'applied'}).every(function (rowIdx, tableLoop, rowLoop) {
                    let row = $(this.node());
                    let checked = row.find('input[type=checkbox]').prop('checked');
                    if (checked) marcados++;
                });
                return marcados > 0;
            }
            function validar_chboxes () {
                let seleccionados = chboxes_seleccionados();
                if (seleccionados) $('#f-envia-mails').removeAttr('disabled');
                else $('#f-envia-mails').prop('disabled', true);
            }
            async function enviar_correos (event) {
                event.preventDefault();
                if (chboxes_seleccionados()) {
                    $('#f-envia-mails').prop('disabled', true);
                    let rows = [];
                    TablaEnvios.rows({filter: 'applied'}).every(function (rowIdx, tableLoop, rowLoop) {
                        let row = $(this.node());
                        let checked = row.find('input[type=checkbox]').prop('checked');
                        if (checked) {
                            rows.push(row);
                        }
                    });
                    for (let row of rows) {
                        let key = row.find('input[type=checkbox]').val();
                        row.children().eq(0).empty().append(
                            $('<img>').attr('src','{{ asset("images/icons/ic-loader-yellow.svg") }}').css('height',16)
                        );
                        try {
                            let result = await $.post('{{ url("mailer/procesar-mail-envio") }}', { key: key, _token: '{{ csrf_token() }}' });
                            row.children().eq(0).empty().append(
                                $('<img>').attr('src','{{ asset("images/icons/ic-ok.png") }}').css('height',16)
                            );
                            row.find('input[type=checkbox]').remove();
                        }
                        catch (error) {
                            row.children().eq(0).empty().append(
                                $('<img>').attr('src','{{ asset("images/icons/ic-error.png") }}').css('height',16)
                            );
                        }
                    }
                    $('#f-envia-mails').removeAttr('disabled');
                    $('#modal-ok').modal('show');
                }
                else alert('Debe seleccionar al menos un envío');
            }

			$('.datepicker').datepicker({
				autoclose: true,
				format: 'dd/mm/yyyy',
				language: 'es',
				todayHighlight: true
			});
            $('#f-submit').on('click', buscar_envios);
            $('#f-envia-mails').on('click', enviar_correos);
		</script>
	</body>
</html>