<div class="charts_container">
<div id="container_completed_tasks"></div>
</div>

<script>
$(function () {
    var chart;
	 
	var task_data_own = {SERIES_TASKS_OWN};
	var task_data_all = {SERIES_TASKS_ALL};
	
	var date_start_1 = {SERIES_DATE_START};
	 
	 
	
	$(document).ready(function() {
		
		chart = new Highcharts.Chart({
            chart: {
                renderTo: 'container_completed_tasks',
                type: 'column',
				width: 725,
				borderColor: '#e7e5e5',
				borderWidth:5,
				marginRight:50
            },
			title: {
				text: 'График выполненых задач за последние 30 дней'
			},
             
			xAxis: {
                type: 'datetime',
                name: 'Время',
				dateTimeLabelFormats: {
                    day: '%e. %b'
                }
				 
				 
            },
            yAxis: {
                title: {
                    text: 'Кол-во поставленных задач'
                },
                min: 0,
				minTickInterval : 1
            },
			tooltip: {
				
				 
				formatter: function() {
					
					tmp_d = new Date(this.x);
					
					s = Highcharts.dateFormat('%B, %e, %A', this.x, 1);
					
					$.each(this.points, function(i, point) {
					
						s += '<tr>'+
						'<td style="color:'+point.series.color+';padding:0">'+point.series.name+': </td>' +
                    	'<td style="padding:0"><b> '+point.point.y +' </b></td>' +
						'</tr>';
					})
					return '<table>' + s + '</table>';
                }
				,
				useHTML: true,
				shared: true, 
				 
            },
 
			 
			series: [{
                 
                name: 'Выполнено задач руководителя',
				data: task_data_all,
				color: '#00CC66',
    
            }, {
			  name: 'Выполнено собственных задач',
               /* data: data,*/
				data : task_data_own
				
            }]
	
          
           
        });
    });
    
});	 
</script>
