$(document).ready(function(){
    var today = new Date();
    var minDate = new Date();
    var dateOption = {
        format: 'mm/yyyy',
        todayHighlight:'TRUE',
        autoclose: true,
        startDate : '1980',
        endDate: today,
    }
    // $('#from_month').datepicker(dateOption);
    // $('#to_month').datepicker(dateOption);
    $("#from_month").datepicker({    
        endDate: new Date(),
        autoclose: true, 
        todayHighlight: true,
        format: 'mm-yyyy',
        viewMode: "months", 
        minViewMode: "months"
    }).on('changeDate', function (selected) {
        var minDate = new Date(selected.date.valueOf());
        $('#to_month').datepicker('setStartDate', minDate);
    });

    $("#to_month").datepicker({ 
        startDate: $("#from_month").val(),
        autoclose: true, 
        todayHighlight: true,
        format: 'mm-yyyy',
        viewMode: "months", 
        minViewMode: "months",
        endDate: new Date(),
    }).on('changeDate', function (selected) {
        var maxDate = new Date(selected.date.valueOf());
        $('#from_month').datepicker('setEndDate', maxDate);
    });
})
$("#m_form_1").validate({
    rules: {
        location_id:{
            required: true,
        },
        from_month:{
            required: true,
        },
        to_month:{
            required: true,
        }
    },
});
$("#m_form_2").validate({
    rules: {
        assigned_to:{
            required: true,
        }
    },
});
$("#m_form_3").validate({
    rules: {
        machine_location_id:{
            required: true,
        }
    },
});
var html = "<div class='m-form__section m-form__section--first'><div class='form-group m-form__group col-md-12 text-center'><div class='col-form-label'>Record not found</div></div></div>";
$('#workOrderByFilter').click(function() {
    if($("#m_form_2").valid()){
        var assigned_to = $('#assigned_to').val();
        var title= $( "#assigned_to option:selected" ).text();
        var displayLabel = 'Machine Name(s)'; 
        $.ajax({
            url: locationUrl,
            type: 'GET',
            data: {
                assigned_to:assigned_to,
                type:2, 
            },
            success: function(response) {
                $('#columnchart').empty();
                if(response.status == 'fail'){
                    swal(response['msg'], {
                        icon: response['icon'],
                        closeOnClickOutside: false,
                    });
                    $('#columnchart').html(html);
                }else{
                    var series = [];
                    $.each(response.chartData,function(k,v){
                        series.push({'name':k,'data':v});
                    });
                    stackedChart(response.months,series,'columnchart',title,displayLabel);
                }
            }
        });
    }
})
$('#machineSummuryReport').click(function() {
    if($("#m_form_3").valid()){
        var location_id = $('#machine_location_id').val();
        var title= $( "#machine_location_id option:selected" ).text();
        var displayLabel = 'Machine Name(s)';
        $.ajax({
            url: locationUrl,
            type: 'GET',
            data: {
                location_id:location_id,
                type:3, 
            },
            success: function(response) {
                $('#machineSummuryChart').empty();
                if(response.status == 'fail'){
                    swal(response['msg'], {
                        icon: response['icon'],
                        closeOnClickOutside: false,
                    });
                    $('#machineSummuryChart').html(html);
                }else{
                    var series = [];
                    $.each(response.chartData,function(k,v){
                        series.push({'name':k,'data':v});
                    });
                    stackedChart(response.months,series,'machineSummuryChart',title,displayLabel);
                }
            }
        });
    }
})

$('#workOrderByLocation').click(function() {
    if($("#m_form_1").valid()){
        var from_month = $('#from_month').val();
        var to_month = $('#to_month').val();
        var location_id = $('#location_id').val();
        var title= $( "#location_id option:selected" ).text();
        var displayLabel = "Month(s)";
        $.ajax({
            url: locationUrl,
            type: 'GET',
            data: {
                from_month:from_month,
                to_month:to_month,
                location_id: location_id,
                type:1,
            },
            success: function(response) {
                $('#stackedColumnchart').empty();
                if(response.status == 'fail'){
                    swal(response['msg'], {
                        icon: response['icon'],
                        closeOnClickOutside: false,
                    });
                    $('#stackedColumnchart').html(html);
                }else{
                    var series = [];
                    $.each(response.chartData,function(k,v){
                        series.push({'name':k,'data':v});
                    });
                    stackedChart(response.months,series,'stackedColumnchart',title,displayLabel);
                }
            }
        });
    }
});

function stackedChart(months,data,id,title,displayLabel){
    Highcharts.chart(id, {
        chart: {
            type: 'column'
        },
        title: {
            text: title
        },
        xAxis: {
            categories: months,
            title: {
                text: displayLabel
            },
        },
        yAxis: {
            min: 0,
            title: {
                text: 'No of work order'
            },
            stackLabels: {
                enabled: true,
                style: {
                    fontWeight: 'bold',
                    color: ( // theme
                        Highcharts.defaultOptions.title.style &&
                        Highcharts.defaultOptions.title.style.color
                    ) || 'gray'
                }
            }
        },
        // legend: {
        //     align: 'right',
        //     x: -30,
        //     verticalAlign: 'top',
        //     y: 25,
        //     floating: true,
        //     backgroundColor:
        //         Highcharts.defaultOptions.legend.backgroundColor || 'white',
        //     borderColor: '#CCC',
        //     borderWidth: 1,
        //     shadow: false
        // },
        tooltip: {
            headerFormat: '<b>{point.x}</b><br/>',
            pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
        },
        plotOptions: {
            column: {
                stacking: 'normal',
                dataLabels: {
                    enabled: false
                }
            }
        },
        series: data,
        credits: {
            enabled: false
        },
    });

}