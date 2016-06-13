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
});

$(document).on('jsonResponse', function () {
	round++;

	console.log('triggerActive');

	// update graphs
	$.each(graphs, function (id, graph) {
		console.log(id + ":" + round + '=>' + $('#' + id + ' input').val());

		graph.data.datasets[0].data.push($('#' + id + ' input').val());
		graph.data.labels.push(round);
		graph.update();
	});
});

$('form.cpage').on('reset', function () {
	input = $(this).find('.leise.variable input');
	input.val(input.data('value')[0]);
	$(this).find('.statistic .value').html(input.val());
});