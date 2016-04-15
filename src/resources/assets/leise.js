$(document).on("updated", function () {
	// slider
	// user input
	$('.leise.variable .extra.content .data.details input[type="range"]').on("input", function() {
		$(this).parents(".extra.content").find('.statistic .value').html($(this).val());
	});

	// system input
	$('.leise.variable .extra.content input').on("updated", function() {
		$(this).parents(".extra.content").find('.statistic .value').html($(this).val());
	});

	// graphs
	// prepare round
	round = $('body#comproso form.cpage input[name="ccusr_rnd"]').val();

	// prepare graphs
	graphs = {};

	// set each graph
	$(".leise.variable").each(function () {
		// get canvas
		canvas = $(this).find('.graph.content canvas');

		// set graph
		graphs[$(this).attr('id')] = new Chart(canvas.get(0).getContext("2d")).Line(
			{
				labels: [round],
				datasets: [{
					strokeColor: "rgba(220,220,220,1)",
					pointColor: "rgba(220,220,220,1)",
					pointStrokeColor: "#fff",
					pointHighlightFill: "#fff",
					pointHighlightStroke: "rgba(220,220,220,1)",
					data: $(this).find("input").val()
				}]
			},
			{
				animation: false,
				responsive: false,
				showScale: false,
				datasetFill: false
			}
		);
	});
});

$(document).on("ajaxProceeded", function()
{
	// set current round
	round = $('body#comproso form.cpage input[name="ccusr_rnd"]').val();

	// update graphs
	$.each(graphs, function (elementId, graph) {
		graph.addData([$(elementId + 'input').val()], round);
	});
});