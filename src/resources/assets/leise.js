$(document).ajaxComplete(function()
{
	// get rounds
	rounds = Array.apply(null, {length: ($('body#comproso form.cpage input[name="ccusr_rnd"]').val() + 1)}).map(Number.call, Number);

	// handle variables
	$(".leise.variable").each(function() {
		// create graph
		var graphContainer = $(this).find(".graph.content");
		var canvas = graphContainer.children("canvas");
		canvas.html("");

		var graph = new Chart(canvas.get(0).getContext("2d")).Line(
			{
				labels: rounds,
				datasets: [{
					strokeColor: "rgba(220,220,220,1)",
		            pointColor: "rgba(220,220,220,1)",
		            pointStrokeColor: "#fff",
		            pointHighlightFill: "#fff",
		            pointHighlightStroke: "rgba(220,220,220,1)",
		            data: $(this).find("input").data("value")
				}]
			},
			{
				animation: false,
				responsive: false,
				showScale: false,
				datasetFill: false
			}
		);
		canvas.attr('width', graphContainer.width());
		canvas.attr('height', 150);

		// slider
		//$(this).find('.extra.content .data.details input[type="range"]').val($(this).find('.statistic .value').html());
		$(this).find('.extra.content .data.details input[type="range"]').on("input", function() {
			$(this).parents(".extra.content").find('.statistic .value').html($(this).val());
		});

		//$(this).find('extra.content .statistic .value').html($(this).find('.extra.content input').val());

		$(this).find('.extra.content input').on("updated", function() {
			$(this).parents(".extra.content").find('.statistic .value').html($(this).val());
		});
	});
});