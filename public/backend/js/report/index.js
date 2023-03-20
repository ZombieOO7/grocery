var today = new Date();
var minDate = new Date();
var dateOption = {
    orientation: 'bottom auto',
    format: 'yyyy',
    todayHighlight:'TRUE',
    autoclose: true,
    viewMode: "years", 
    minViewMode: "years",
    startDate : '1980',
    endDate: today,
}
$('#year').datepicker(dateOption);
$(document).find("#month").select2();
$(document).find("#date").select2();
$(document).find("#status").select2();
// form validation
$(document).ready(function () {
    $("#m_form_1").validate({
        rules: {
            year:{
                required:true,
            },
            month:{
                required:function(element){
                    if($('#reportCategory').val()==2 || $('#reportCategory').val()==3){
                        return true;
                    }else{
                        return false;
                    }
                }
            },
            date:{
                required:function(element){
                    if($('#reportCategory').val()==3){
                        return true;
                    }else{
                        return false;
                    }
                }                
            },
            export_to:{
                required:true,
            },
            report_type:{
                required:true,
            },
            reportCategory:{
                required:true,
            }
        },
        errorPlacement: function (error, element) {
            if (element.attr("name") == "export_to")
                error.insertAfter(".exportError");
            else if(element.attr("name") == "report_type")
                error.insertAfter(".reportTypeError");
            else if(element.attr("name") == "location_id")
                error.insertAfter(".dynamicError");
            else if(element.attr("name") == "problem_id")
                error.insertAfter(".dynamicError");
            else if(element.attr("name") == "machine_id" && $('#reportType').val() !=6)
                error.insertAfter(".dynamicError");
            else if(element.attr("name") == "user_id")
                error.insertAfter(".dynamicError");
            else if(element.attr("name") == "job_type_id")
                error.insertAfter(".dynamicError");
            else if(element.attr("name") == "month")
                error.insertAfter(".monthsError");
            else if(element.attr("name") == "date")
                error.insertAfter(".daysError");
            else if(element.attr("name") == "machine_id")
                error.insertAfter(".machineError");
            else
                error.insertAfter(element);
        },
        success: function() {
            $("#m_form_1_msg").addClass("m--hide").show();
        },
        invalidHandler: function (e, r) {
            $("#m_form_1_msg").removeClass("m--hide").show(),
                mUtil.scrollTop()
        },
    });
})
// view job report data
$('#viewReport').on('click',function(){
    var year = $('#year').val();
    var month = $('#month').val();
    var date = $('#date').val();
    var status = $('#status').val();
    var type= $('#reportType').val();
    var location_id = $('#location_id').val();
    var problem_id = $('#problem_id').val();
    var machine_id = $('#machine_id').val();
    var user_id = $('#user_id').val();
    var job_type_id = $('#job_type_id').val();
    // $('#location_id').rules('add', {
    //     'required':function (element) {
    //         if ($("#reportType").val() == 6) {
    //             return true;
    //         } else {
    //             return false;
    //         }
    //     },
    // });
    $('#location_id').rules('add', {
        required:true,
    });
    $('#job_type_id').rules('add', {
        required:true,
    });
    $('#problem_id').rules('add', {
        required:true,
    });
    $('#user_id').rules('add', {
        'required':function (element) {
            if ($("#reportType").val() == 5) {
                return true;
            } else {
                return false;
            }
        },
    });
    $('#machine_id').rules('add', {
        'required':function (element) {
            if ($("#reportType").val() == 2) {
                return true;
            } else {
                return false;
            }
        },
    });
    $('#export_to').rules("remove");      
    if($("#m_form_1").valid()){
        $.ajax({
            url: url,
            method: "POST",
            datatype: "html",
            data: {
                year:year,
                month:month,
                date:date,
                status:status,
                report_type : type,
                location_id : location_id,
                machine_id : machine_id,
                user_id : user_id,
                problem_id:problem_id,
                job_type_id:job_type_id
            },
            global:true,

            success: function (response) {
                if(response.msg !=undefined){
                    $('#tableContent').empty();
                    swal(response['msg'], {
                        icon: response['icon'],
                        closeOnClickOutside: false,
                    });
                }else{
                    $('#tableContent').html(response);
                }
            }
        });
    }
})
// clear inputs value and reset job detail
$('#clearBtn').on('click', function(){
    $('#tableContent').empty();
    $('input:text').val('');
    $('#month').val('').change();
    $('#date').val('').change();
})

$('#reportType').on('change',function(){
    var type= $(this).val();
    $('#tableContent').empty();
    // if(type != null && type != undefined && type != '' && type != 1 && type != 4 && type != 3){
    reportTypeData(type);
})
if($('#reportType').val() != null){
    var type= $('#reportType').val();
    $('#tableContent').empty();
    reportTypeData(type);
}
function reportTypeData(type){
        if(type != null && type != undefined && type != '' && type != 8 && type != 4 && type != 9){
        $.ajax({
            url: getReportType,
            method: "POST",
            datatype: "html",
            data: {
                type:type,
            },
            global:true,
            success: function (response) {
                $('#dynamicFilters').html(response);
            }
        })
    }else{
        $('#dynamicFilters').empty();
    }
}
$(document).on('change', '#location_id', function () {
    var id = $(this).val();
    var type= $('#reportType').val();
    if(id != undefined && id !='' && id!=null && type == 6){
        $.ajax({
            url: getMachineList,
            method: "GET",
            data: {location_id: id},
            global: true,
            success: function (response) {
                $('#machine_id').find('option').remove();
                $('#machine_id').append($("<option></option>")
                                .attr("value",'')
                                .text('Select Machine'));
                $.each(response.machinList, function(key, value) {
                    $('#machine_id').append($("<option></option>")
                                    .attr("value",key)
                                    .text(value));
                });
            }
        })
    }
})
$(document).find("#m_form_1").submit(function(){
    // $('#location_id').rules('add', {
    //     'required':function (element) {
    //         if ($("#reportType").val() == 2) {
    //             return true;
    //         } else {
    //             return false;
    //         }
    //     },
    // });
    $('#user_id').rules('add', {
        'required':function (element) {
            if ($("#reportType").val() == 5) {
                return true;
            } else {
                return false;
            }
        },
    });
    // $('#machine_id').rules('add', {
    //     'required':function (element) {
    //         if ($("#reportType").val() == 6) {
    //             return true;
    //         } else {
    //             return false;
    //         }
    //     },
    // });
    $('#export_to').rules('add', {
        'required':true,
    });
    if($("#m_form_1").valid()){
        return true;
    }else{
        return false;
    }
})
$('#yearly').on('click',function(){
    $('#yearInput').show();
    $('#monthInput').hide();
    $('#dayInput').hide();
    $('#reportCategory').val(1);
    $('#year').val('');
    $('#month').val('');
    $('#date').val('');
    $('#month').selectpicker().trigger('change');
    $('#date').selectpicker().trigger('change');
});
$('#monthly').on('click',function(){
    $('#yearInput').show();
    $('#monthInput').show();
    $('#dayInput').hide();
    $('#reportCategory').val(2);
    $('#year').val('');
    $('#month').val('');
    $('#date').val('');
    $('#month').selectpicker().trigger('change');
    $('#date').selectpicker().trigger('change');
});
$('#daily').on('click',function(){
    $('#yearInput').show();
    $('#monthInput').show();
    $('#dayInput').show();
    $('#reportCategory').val(3);
    $('#year').val('');
    $('#month').val('');
    $('#date').val('')
    $('#month').selectpicker().trigger('change');
    $('#date').selectpicker().trigger('change');
});