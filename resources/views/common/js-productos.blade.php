<script type="text/javascript">
	var arr_prds = ["Todos"];
	document.getElementById("trg-producto").value = "Todos";
	$("#modal-producto input[type=checkbox]").prop("checked",true);
	//modal producto
	$("#trg-producto").on("click", function() {
		$("#modal-producto").modal("show");
	});
	$("#ch-pr-all").change(function() {
		$(".ch-pr").prop("checked", document.getElementById("ch-pr-all").checked);
	});
	$(".ch-pr").change(function() {
		$(".ch-pr-all").prop("checked", false);
	});
	$("#modal-producto").on("hide.bs.modal", function() {
		arr_prds = new Array();
		if(document.getElementById("ch-pr-all").checked) {
			arr_prds = [document.getElementById("ch-pr-all").value];
			document.getElementById("trg-producto").value = "Todos";
		}
		else {
			var pr_all = $(".ch-pr:checked");
			var sseleccion = "";
			$.each(pr_all, function() {
				var input = $(this);
				arr_prds.push(input.val());
				sseleccion += (sseleccion == "" ? "" : ",") + input.data("label");
			});
			document.getElementById("trg-producto").value = sseleccion;
		}
	});
</script>