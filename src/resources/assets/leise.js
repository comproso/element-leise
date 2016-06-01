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
		graphs[$(this).attr('id')] = new Chart(canvas.get(0).getContext("2d"),
			{
				type: 'line',
				data: {	// data
					labels: [1],
					datasets: [{
						fill: false,
						lineTension: 0.1,
						backgroundColor: "rgba(75,192,192,0.4)",
						borderColor: "rgb(0, 0, 0)",
						borderCapStyle: 'butt',
						borderDash: [],
						borderDashOffset: 0.0,
						borderJoinStyle: 'miter',
						pointBorderColor: "rgba(75,192,192,1)",
						pointBackgroundColor: "#fff",
						pointBorderWidth: 1,
						pointHoverRadius: 5,
						pointHoverBackgroundColor: "rgba(75,192,192,1)",
						pointHoverBorderColor: "rgba(220,220,220,1)",
						pointHoverBorderWidth: 2,
						pointRadius: 5,
						pointHitRadius: 10,
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

$(document).on("ajaxProceeded", function()
{
	// set current round
	round = $('body#comproso form.cpage input[name="ccusr_rnd"]').val();

	// update graphs
	$(".leise.variable").each(function () {
		graph = graphs[$(this).attr('id')];
		rnd = ++graph.data.datasets[0].data.length;

		graph.data.datasets[0].data.push($(this).find('input').val());
		graph.data.labels.push(rnd);
		graph.update();
	});

	$('form.cpage').on('reset', function () {
		input = $(this).find('.leise.variable input');
		input.val(input.data('value')[0]);
		$(this).find('.statistic .value').html(input.val());
	});
});