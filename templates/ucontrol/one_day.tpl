<div>{NOTICES}</div>

<div class="charts_container">
	<div id="container_chart_day"></div>
</div>
 
<script>
 
    var chart;
	  
	data_inbox = {SERIES_INBOX};
	data_outbox = {SERIES_OUTBOX};
	data_tasks_reports = {SERIES_TASKS};
	date_start = {SERIES_DATE_START};
	date_work_start = {SERIES_DATE_WORK_START};
	date_work_finish = {SERIES_DATE_WORK_FINISH};
	date_sipuni = {SERIES_DATE_SIPUNI};
	work_start = '{WORK_START}';
	work_finish = '{WORK_FINISH}';
	data_deals_edit = {SERIES_DEALS_EDIT};
	data_deals_add = {SERIES_DEALS_ADD};
	work_reports = {SERIES_WORK_REPORTS};
	
	// Структурированный массив даты и отчета
	// Чтобы можно было выводить в Tooltip
	structured_arr = {}
	 
	
	$(document).ready(function() {
		 
		chart = new Highcharts.Chart({
            chart: {
                renderTo: 'container_chart_day',
                 
				width: 725,
				borderColor: '#e7e5e5',
				borderWidth:5,
				marginRight:50 
            },
			title: {
				text: 'График за '+$('#for_date').val()
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
                    text: 'Кол-во'
                },
                min: 0 
            },
			 

            plotOptions: {
				series: {
					pointStart : date_start,
					pointInterval : 3600 * 1000,
					marker: {
						enabled: false,
						states: {
							hover: {
								enabled: true,
								radius: 3
							}
						}
					}
				}
				 
			},
			tooltip: {
				formatter: function() {
					var s = [];
					$.each(this.points, function(i, point) {
						s.push("<span style=\"color: "+point.series.color+"\">" + point.series.name+':</span> '+point.y);
					});

					return '<b>'+Highcharts.dateFormat('%H:00', this.x)+ ' - '+Number(parseInt(Highcharts.dateFormat('%H', this.x))+1)+':00</b><br><br>Начало работы: '+work_start+'<br>Конец работы: '+work_finish+'<br> <br/>'+ s.join("<br/>");
				},
				shared: true
			},
			series: [
			{
                name: 'Начало работы',
                
            	data: date_work_start,
			 	marker: {
						enabled: true,
						radius : 9
			 	},
				type : 'scatter',  
			    color : '#62d144'
				
            },
			{
                name: 'Конец работы',
                
            	data: date_work_finish,
			 	marker: {
						enabled: true,
						radius : 8
			 	},
				type : 'scatter',  
			    color : '#d15244'
				
            }
			 
			{SIPUNI_SERIES_JS}
			{INBOX_SERIES_JS}
			{OUTBOX_SERIES_JS}
			{TASKS_REPORTS_SERIES_JS}
			{DEALS_EDIT_SERIES_JS}
			{DEALS_ADD_SERIES_JS}
			{DEALS_WORK_REPORTS_JS}

			],
			
			 
        
	
          
           
        });
    });
    
  
</script>
