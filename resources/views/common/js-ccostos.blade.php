<script type="text/javascript">
	var arr_ccs = ["Todos"];
	document.getElementById("trg-ccosto").value = "Todos";
	//modal ccosto
	$("#trg-ccosto").on("click", function() {
		$("#modal-ccosto").modal("show");
	});
	$("#ch-cc-all").change(function() {
		$(".ch-cc").prop("checked", document.getElementById("ch-cc-all").checked);
	});
	$(".ch-cc").change(function() {
		$(".ch-cc-all").prop("checked", false);
	});
	$("#modal-ccosto").on("hide.bs.modal", function() {
		arr_ccs = new Array();
		if(document.getElementById("ch-cc-all").checked) {
			arr_ccs = [document.getElementById("ch-cc-all").value];
			document.getElementById("trg-ccosto").value = "Todos";
		}
		else {
			var of_all = $(".ch-cc:checked");
			var sseleccion = "";
			$.each(of_all, function() {
				var input = $(this);
				arr_ccs.push(input.val());
				sseleccion += (sseleccion == "" ? "" : ",") + input.data("label");
			});
			document.getElementById("trg-ccosto").value = sseleccion;
		}
	});
</script>