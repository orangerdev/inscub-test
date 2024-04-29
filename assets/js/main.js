(function ($) {
	"use strict";

	$(document).ready(function () {
		const theTable = $("#incsub-table");

		// initialize the table
		if (theTable.length > 0) {
			theTable.DataTable({
				order: [[0, "desc"]],
				columnDefs: [
					{
						targets: 0,
						data: "id",
					},
					{
						targets: 1,
						data: "name",
					},
					{
						targets: 2,
						data: "email",
					},
					{
						targets: 3,
						data: "phone",
					},
					{
						targets: 4,
						data: "address",
					},
					{
						targets: 5,
						data: "created_at",
					},
				],
				ajax: {
					headers: {
						"X-WP-Nonce": incsub_test.nonce,
					},
					url: incsub_test.rest_url,
				},
			});
		}

		$("#incsub-form").submit(function (e) {
			e.preventDefault();
			const formData = $(this).serializeArray();

			$.ajax({
				url: incsub_test.rest_url,
				type: "POST",
				data: formData,

				headers: {
					"X-WP-Nonce": incsub_test.nonce,
				},
				success: function (response) {
					if (response.success) {
						// show success message
						$("#incsub-form .form-message")
							.html(response.data.message.join("<br>"))
							.addClass("success")
							.removeClass("error");
					}
					if (theTable.length > 0) theTable.DataTable().ajax.reload();
				},
			});
		});
	});
})(jQuery);
