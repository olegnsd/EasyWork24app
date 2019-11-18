<!--Строка навигации-->
<div class="nav_block">
{NAV}
</div>
<!--\ Строка навигации-->


<script>
$(function () {
    var chart;
    $(document).ready(function() {
		categories = {CATEGORIES};
		data = {SERIES};
		user_name = '{SURNAME} {NAME} {MIDDLENAME}';
        chart = new Highcharts.Chart({
            chart: {
                renderTo: 'container',
                type: 'area',
				spacingBottom: 20,
                marginRight: 130,
                paddingBottom: 35,
				ignoreHiddenSeries : true
            },
            title: {
                text: 'График эффективности',
                x: -20 //center
            },
            subtitle: {
                text: user_name,
                x: -20
            },
            xAxis: {
				showFirstLabel:true,
				startOnTick:true,
				endOnTick:true,
				
				type : 'datetime',
				 tickInterval: 2*24 * 3600 * 1000,
				title: {
                    text: 'КПД'
                },
            }
			,
			 series: [{
			name : 'Эффективность',
				 data: data
            }],
           /* series: [{
                name: 'Эффективность',
                data: [1,4,5] 
				 
            }],*/
            yAxis: {
                title: {
                    text: 'КПД'
                },
				 max : 100,
                plotLines: [{
                    value: 0,
                    width: 1,
                    color: '#808080'
                }]
            } ,
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'top',
                x: -10,
                y: 100,
                borderWidth: 0
            }
        });
    });
    
});	 
</script>

<div style="height:400px; width:700px; overflow-x:hidden; overflow-y:hidden; padding-bottom:30px">
<div id="container" style="width: 1200px; height: 400px"></div>
</div>
