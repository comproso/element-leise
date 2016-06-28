$(document).on("ready", function () {
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
	round = 0;

	// prepare graphs
	graphs = {};

	// set each graph
	$(".leise.variable").each(function () {
		// get canvas
		canvas = $(this).find('.graph.content canvas');

		canvas.attr('width', $(this).width());
		canvas.attr('height', canvas.parent().height());

		// set graph
		graphs[$(this).attr('id')] = new Chart(canvas.get(0).getContext("2d"),
			{
				type: 'line',
				data: {	// data
					labels: [round],
					datasets: [{
						fill: false,
						lineTension: 0.1,
						backgroundColor: "rgba(75,192,192,0.4)",
						borderColor: "rgb(0, 0, 0)",
						borderCapStyle: 'butt',
						borderDash: [],
						borderDashOffset: 0.0,
						borderJoinStyle: 'miter',
						pointRadius: 0,
						data:[$(this).find("input").val()]
					}]
				},
				options: {	// options
					legend: {display: false},
					tooltips: {enabled: false},
					scales: {
						yAxes: [{
							display: false
						}],
						xAxes: [{
							display: false
						}]
					},
					showLines: true
				}
			}
		);
	});

	// reset
	$('form.cpage').on('reset', function () {
		input = $(this).find('.leise.variable input');
		input.val(input.data('value')[input.data('value').length - 1]);
		input.triggerHandler('input');
	});
});

$(document).on('jsonResponse', function () {
	round++;

	// update graphs
	$.each(graphs, function (id, graph) {
		graph.data.datasets[0].data.push($('#' + id + ' input').val());
		graph.data.labels.push(round);
		graph.update();
	});
});