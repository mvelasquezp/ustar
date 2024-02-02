<!DOCTYPE html>
<html>
	<head>
		<title>Carga de envíos</title>
		@include("common.styles")
		<link rel="stylesheet" type="text/css" href="{{ asset('css/datepicker.min.css') }}">
		<style type="text/css">
			.table-responsive{max-height:1000px !important;}
            #dv-result{display:none}
		</style>
	</head>
	<body>
		@include("common.navbar")
		<!-- PAGINA -->
		<div class="container mt-2">
			<div class="row justify-content-center">
				<div class="col-8">
                    <div class="alert bg-light shadow-sm py-3">
                        <form id="form-xls">
                            <div class="form-group">
                                <label for="file-xls">Adjunte el archivo XLS para programar el envío de documentos</label>
                                <input type="file" class="form-control-file form-control-sm" id="file-xls" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">
                            </div>
                            <div class="form-group">
                                <label for="file-tipo">Tipo de archivo a cargar</label>
                                <select class="form-control form-control-sm" id="file-tipo">
                                    <option value="-1" disabled>- Seleccione -</option>
                                    @foreach ($tipos as $tipo)
                                    <option value="{{ $tipo->value }}">{{ $tipo->text }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button id="form-submit" class="btn btn-sm btn-success" disabled="true"><i class="fa fa-upload mr-2"></i>Cargar archivo</button>
                        </form>
                    </div>
				</div>
			</div>
            <div class="row" id="dv-result">
                <div class="col">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Guía</th>
                                <th># Certificado</th>
                                <th colspan="2">Responsable</th>
                                <th>e-mail</th>
                                <th>Envío</th>
                                <th>Observaciones</th>
                            </tr>
                        </thead>
                        <tbody id="result-tbody"></tbody>
                    </table>
                </div>
            </div>
		</div>
		<!-- JS -->
		@include("common.scripts")
		<script type="text/javascript" src="{{ asset('js/datepicker.min.js') }}"></script>
		<script type="text/javascript">
            let UrlPostForm = '{{ url("mailer/upload-xlsx") }}';
            /*
			$(".datepicker").datepicker({
				autoclose: true,
				format: "dd/mm/yyyy",
				language:"es",
				todayHighlight: true
			});
            */
            function escribirListaResultados(resultados) {
                $('#result-tbody').empty();
                for (let fila of resultados) {
                    $('#result-tbody').append(
                        $('<tr>').append(
                            $('<td>').text(fila.guia)
                        ).append(
                            $('<td>').text(fila.certificado)
                        ).append(
                            $('<td>').text(fila.dni)
                        ).append(
                            $('<td>').text(fila.nombre)
                        ).append(
                            $('<td>').text(fila.email)
                        ).append(
                            $('<td>').text('Preparado').addClass('text-center')
                        ).append(
                            $('<td>').text(fila.observaciones)
                        )
                    );
                }
                document.getElementById('file-xls').value = null;
            }
            function iniciarSubidaArchivo(event) {
                event.preventDefault();
                if (document.getElementById('file-tipo').value == '-1') {
                    alert('Seleccione el tipo de archivo a cargar');
                    return;
                }
                $('#dv-result').fadeIn(250);
                $('#result-tbody').empty().append(
                    $('<tr>').append(
                        $('<td>').text('Por favor, espere mientras su archivo es procesado').attr('colspan',7)
                    )
                );
                let formData = new FormData();
                    formData.append('xlsx', document.getElementById('file-xls').files[0]);
                    formData.append('tipo', document.getElementById('file-tipo').value);
                    formData.append('_token', '{{ csrf_token() }}');
                $.ajax({
                    url: UrlPostForm,
                    type: 'post',
                    dataType: 'json',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(result) {
                        if (result.error) {
                            alert(result.error);
                            return;
                        }
                        if (result.resultados) {
                            escribirListaResultados(result.resultados);
                            return;
                        }
                        alert('No se pudo procesar el archivo de subida');
                    },
                    error: function(error) {
                        alert(error);
                    }
                });
            }
            function validaFormFile(event) {
                event.preventDefault();
                if (document.getElementById('file-xls').files.length == 1 && document.getElementById('file-tipo').value != '-1') {
                    $('#form-submit').removeAttr('disabled');
                }
                let tpenvio = document.getElementById('file-tipo').value;
                switch (tpenvio) {
                    case 'C':
                    case 'E':
                        UrlPostForm = '{{ url("mailer/upload-xlsx") }}';
                        break;
                    case 'CF':
                        UrlPostForm = '{{ url("mailer/upload-xlsx-compartamos") }}';
                        break;
                    case 'MS':
                        UrlPostForm = '{{ url("mailer/upload-xlsx-microseguro") }}';
                        break;
                    case 'MS2':
                        UrlPostForm = '{{ url("mailer/upload-xlsx-microseguro-step2") }}';
                        break;
                }
            }
            $('#file-xls').val(null).change(validaFormFile);
            $('#file-tipo').on('change', validaFormFile);
            $('#form-xls').on('submit', iniciarSubidaArchivo);
            $('#form-submit').prop('disabled',true);
            $('#file-tipo option[value=-1]').prop('selected',true);
		</script>
	</body>
</html>