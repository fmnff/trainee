// Once DOM is ready
$(document).ready(function () {

	// Adding "loading" symbols
	$(".result").html("<span class='fa fa-spinner fa-spin fa-3x fa-fw'></span>");

	// Adjusting the container height for the page to take the whole screen
	$("body > .container").css("height", $("body").height() - $("header").height());

	// Loading data for the default tab
	$.ajax({
		url: "loadrates.php",
		type: "post",
		data: {"source":$(".active").attr("id")},
		dataType: "json",
		success: function(data) {
			for (let key in data) {
				let output = data[key].toString();

				// Adjusting the float value to have 2 digits after the decimal separator
				while (output.split(".")[1].length < 2) {
					output = output.concat("0");
				}

				// Pasting the results to the elements with id = currency code
				$("#".concat(key)).html(output);
			}
		}
	});
});

// On tab change
$(document).on("click", ".clickable", function () {

	// Adding loading symbols
	$(".result").html("<span class='fa fa-spinner fa-spin fa-3x fa-fw'></span>");
	$(".active").removeClass("active").addClass("clickable");
	$(this).removeClass("clickable").addClass("active");
	$.ajax({
		url: "loadrates.php",
		type: "post",
		data: {"source": $(this).attr("id")},
		dataType: "json",
		success: function (data) {
			for (let key in data) {
				let output = data[key].toString();

				// Adjusting the float value to have 2 digits after the decimal separator
				while (output.split(".")[1].length < 2) {
					output = output.concat("0");
				}

				// Pasting the results to the elements with id = currency code
				$("#".concat(key)).html(output);
			}
		}
	})
});
