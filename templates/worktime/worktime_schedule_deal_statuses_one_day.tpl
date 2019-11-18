
<div class="charts_container">
	<div id="container_chart_day"></div>
</div>

<script>
$(function () {
    var chart;
	 
	data = {SERIES};
	date_start = {SERIES_DATE_START};
	// Структурированный массив даты и отчета
	// Чтобы можно было выводить в Tooltip
	structured_arr = {}
	 
	
	$(document).ready(function() {
		
		chart = new Highcharts.Chart({
            chart: {
                renderTo: 'container_chart_day',
                type: 'column',
				width: 910,
				borderColor: '#e7e5e5',
				borderWidth:5,
				marginRight:50 
            },
			title: {
				text: 'График статусов сделок за '+checked_date
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
                    text: 'Кол-во измененных сделок'
                },
                min: 0 
            },
			 

            plotOptions: {
				series: {
					pointStart : date_start,
					pointInterval : 3600 * 1000
				}
				 
			},
			tooltip: {
				
				useHTML : true,
				formatter: function() {
					
					var f_date;
					dateObj = new Date(this.point.x);
				 	f_date = Highcharts.dateFormat('%H:%M', this.x, 1)+' ч.';
					return f_date+'<br>Кол-во обновленных статусов сделок: <b>'+this.point.y+'</b>';
                }
            },
			series: [{
                name: 'Обновление статусов',
               /* data: data,*/
				data : data 
				
            }]
	
          
           
        });
    });
    
});	 
</script>
