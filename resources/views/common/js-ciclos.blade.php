<script type="text/javascript">
	var arr_ciclos = [];
	document.getElementById("trg-ciclo").value = "";
	$("#modal-ciclo input[type=checkbox]").prop("checked",false);
	//modal ciclo
	$("#trg-ciclo").on("click", function() {
		$("#modal-ciclo").modal("show");
	});
	$("#modal-ciclo").on("hide.bs.modal", function() {
		arr_ciclos = new Array();
		var of_all = $(".ch-cl:checked");
		var sseleccion = "";
		$.each(of_all, function() {
			var input = $(this);
			arr_ciclos.push(input.val());
			sseleccion += (sseleccion == "" ? "" : ",") + input.data("label");
		});
		document.getElementById("trg-ciclo").value = sseleccion;
	});
</script>