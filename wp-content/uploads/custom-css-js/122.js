<!-- start Simple Custom CSS and JS -->
<script type="text/javascript">
if (my_script_vars.postID == 104){
new Chart(document.getElementById("bar-chart"), {
  type: 'bar',
  data: {
    labels: ["2019-05","2019-06","2019-07","2019-08","2019-09","2019-10"],
    datasets: [{ 
        data: [110,130,150,170,210,257],
        label: "Your points for the last months",
        lineTension: 0,
        borderColor: "#3e95cd",
      	backgroundColor: "#4CB944",
        fill: true
      }
    ]
  },
  options: {
    responsive: true,
    bezierCurve: false,
    legend: {
        display: false
    },
    title: {
      display: true,
      text: 'Points collected for the last months'
    },
	scales: {
            yAxes: [{
                ticks: {
                    suggestedMin: 0,
                    suggestedMax: 200
                }
            }]
        }
  }
});

  new Chart(document.getElementById("chart-area"), {
  type: 'pie',
  data: {
    labels: ["Mobility","Food","Events"],
    datasets: [{ 
        data: [125,70,62],
        label: "Your points for the last months",
        lineTension: 0,
        borderColor: "#3e95cd",
      	backgroundColor: "#4CB944",
        fill: true
      }
    ]
  },
  options: {
    responsive: true,
    bezierCurve: false,
    legend: {
        display: false
    },
    title: {
      display: true,
      text: 'Points collected in different categories this month'
    },
	scales: {
            yAxes: [{
                ticks: {
                    suggestedMin: 0,
                    suggestedMax: 200
                }
            }]
        }
  }
});

}</script>
<!-- end Simple Custom CSS and JS -->
