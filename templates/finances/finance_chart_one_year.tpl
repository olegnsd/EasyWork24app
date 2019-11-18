
<div style="height:400px; width:800px; margin-top:20px; overflow-x:visible;padding-bottom:30px">
	<div id="container_finance_one_year" style="width: 1200px; height: 400px"></div>
</div>

<script>
$(function () {
    var chart;
	 
	series_finances_in = {SERIES_FINANCES_IN};
	series_finances_out = {SERIES_FINANCES_OUT};
	series_date_start = {SERIES_DATE_START};
	summa_currency = '{FINANCE_CURRENCY}';
	$(document).ready(function() {
		
		$('#container_finance_one_year').highcharts({
            chart: {
                type: 'column',
				width : 910,
				borderColor: '#e7e5e5',
				borderWidth:5,
				marginRight:50
            },
            title: {
                text: 'График поступлений и расходов за последние 365 дней'
            },
            xAxis: {
                type: 'datetime',
				name: 'Дата',
				dateTimeLabelFormats: {
                    day: '%e. %b',
                },
				 
            },
			yAxis: {
              /* type : 'logarithmic',*/
			   title : 'Сумма',
			   name : 'Сумма'
				 
            },
			plotOptions: {
				series: {
					pointStart: series_date_start,
					pointInterval: 24 * 3600 * 31 * 1000
				}
			} ,
           
			tooltip: {
				
				 
				formatter: function() {
					
					tmp_d = new Date(this.x);
					var summa = '';
					var date;
					var s = ''
					date = Highcharts.dateFormat('%B, %Y', this.x, 1);
					
					$.each(this.points, function(i, point) {
						
						summa = Highcharts.numberFormat(this.y, 2, '.');
						s += '<tr>'+
						'<td style="color:'+point.series.color+';padding:0">'+point.series.name+': </td>' +
                    	'<td style="padding:0"><b> '+summa+" "+summa_currency+'</b></td>' +
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
                data: series_finances_in
    
            }, {
                name: 'Расход',
				data: series_finances_out
    
            }]
        });
		
    });
    
});	 
</script>
