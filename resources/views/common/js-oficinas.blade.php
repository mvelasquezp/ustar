<script>
	var arr_ofcs = ["Todos"];
	document.getElementById("trg-oficina").value = "Todos";
	$("#modal-oficina input[type=checkbox]").prop("checked",true);
	//modal oficina
	$("#trg-oficina").on("click", function() {
		$("#modal-oficina").modal("show");
	});
	$("#ch-of-all").change(function() {
		$(".ch-of").prop("checked", document.getElementById("ch-of-all").checked);
	});
	$(".ch-of").change(function() {
		$(".ch-of-all").prop("checked", false);
	});
	$("#modal-oficina").on("hide.bs.modal", function() {
		arr_ofcs = new Array();
		if(document.getElementById("ch-of-all").checked) {
			arr_ofcs = [document.getElementById("ch-of-all").value];
			document.getElementById("trg-oficina").value = "Todos";
		}
		else {
			var of_all = $(".ch-of:checked");
			var sseleccion = "";
			$.each(of_all, function() {
				var input = $(this);
				arr_ofcs.push(input.val());
				sseleccion += (sseleccion == "" ? "" : ",") + input.data("label");
			});
			document.getElementById("trg-oficina").value = sseleccion;
		}
	});
</script>