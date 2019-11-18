
<div style="height:400px; width:800px; margin-top:20px; overflow-x:visible;padding-bottom:30px">
	<div id="container_finance_30_days" style="width: 1200px; height: 400px"></div>
</div>

<script>
$(function () {
    var chart;
	 
	series_finances_in_year = {SERIES_FINANCES_IN};
	series_finances_out_year = {SERIES_FINANCES_OUT};
	series_date_start_year = {SERIES_DATE_START};
	summa_currency_year = '{FINANCE_CURRENCY}';
	$(document).ready(function() {
		
		$('#container_finance_30_days').highcharts({
            chart: {
                type: 'column',
				width : 910,
				borderColor: '#e7e5e5',
				borderWidth:5,
				marginRight:50
            },
            title: {
                text: 'График поступлений и расходов за последние 30 дней'
            },
            xAxis: {
                type: 'datetime',
				name: 'Дата',
				dateTimeLabelFormats: {
                    day: '%e. %b',
                },
				 
            },
			yAxis: {
               /*type : 'logarithmic',*/
			   title : 'Сумма',
			   name : 'Сумма'
				 
            },
			plotOptions: {
				series: {
					pointStart: series_date_start_year,
					pointInterval: 24 * 3600 * 1000
				}
			} ,
           
			tooltip: {
				
				 
				formatter: function() {
					
					tmp_d = new Date(this.x);
					var summa = '';
					var date;
					var s = ''
					date = Highcharts.dateFormat('%B, %e, %A', this.x, 1);
					
					$.each(this.points, function(i, point) {
						
						summa = Highcharts.numberFormat(this.y, 2, '.');
						s += '<tr>'+
						'<td style="color:'+point.series.color+';padding:0">'+point.series.name+': </td>' +
                    	'<td style="padding:0"><b> '+summa+" "+summa_currency_year+'</b></td>' +
						'</tr>';
					})
					return date+'<table>' + s + '</table>';
                }
				,
				useHTML: true,
				shared: true, 
				 
            },
			
            series: [{
                name: 'Поступление',
				color: '#00CC66',
                data: series_finances_in_year
    
            }, {
                name: 'Расход',
				data: series_finances_out_year
    
            }]
        });
		
    });
    
});	 
</script>
