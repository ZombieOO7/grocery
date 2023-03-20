// This function is used to initialize the data table.
(function ($)
{
    var data = [];

    var oppenIpps = function ()
    {
        c._initialize();
        c._deleteRecord();
        c._changeStatus();
        c._bulkAction();
    };
    var c = oppenIpps.prototype;

    c._initialize = function ()
    {
        // This function is used for applying csrf token in ajax.
        $.ajaxSetup({
            headers: {'X-CSRF-TOKEN': $('meta[name="csrfToken"]').attr('content')}
        });

        $(document)
        .ajaxStart(function () {
            $('.main_loader').show();   //ajax request went so show the loading image
        })
        .ajaxStop(function () {
            $('.main_loader').hide();   //got response so hide the loading image
        });

        // This function is used to validate the select picker on change.
        $(document).find('.selectpicker').on('change', function () {
            $(this).valid();
        });

        // This function is used of doing trim on sentence.
        function trim(el) {
            el.value = el.value.
            replace(/(^\s*)|(\s*$)/gi, ""). // removes leading and trailing spaces
            replace(/[ ]{2,}/gi, " "). // replaces multiple spaces with one space
            replace(/\n +/, "\n"); // Removes spaces after newlines
            return;
        }
    };

    //Generate data table
    c._generateDataTable = function(element_id_name,ajax_URL,field_coloumns,order_coloumns,data,dom){
        var bSearching = true;
        if (field_coloumns === undefined) {
            field_coloumns = [];
        }
        if (order_coloumns === undefined) {
            order_coloumns = [[1, "desc"]];
        }

        var intial_url = 'http://';
        var intial_url2 = 'https://';
        var final_ajax_url = '';
        if(ajax_URL.indexOf(intial_url) != -1){
            final_ajax_url = ajax_URL;
        }else if(ajax_URL.indexOf(intial_url2) != -1){
            final_ajax_url = ajax_URL;
        }else{
            final_ajax_url = base_url + ajax_URL;
        }
        var doms = 'trilp' , button = [];
        if(dom != undefined) {
            doms = dom;
            button = [
                {
                    extend: 'csvHtml5',
                    // title: 'Data List',
                    text:'Export',
                    extension:'.csv',
                    exportOptions: {
                        columns: "thead th:not(.noExport)"
                    }
                }
            ]
        }
        var locationId = null;
        if($('#locationId').length > 0)
        {
            locationId = $('#locationId').val();
        }
        table = $('#'+element_id_name).DataTable({
            stateSave:true,
            // responsive: true,
            "processing": true,
            "order": order_coloumns,
            "oLanguage": {
                "sProcessing":  '<img src="'+base_url +'/public/images/loader.gif" width="40">',
                "sEmptyTable":"No Record Found",
            },
            "lengthMenu": [10, 25, 50, 75, 100 ],
            "serverSide": true,
            "bInfo": true,
            "autoWidth": false,
            "searching": bSearching,
            "orderCellsTop": true,
            "columns": field_coloumns,
            "bPaginate":true,
                dom: doms,
            buttons:button,
            initComplete: function () {
                if(data != undefined) {
                    if(data['type'] == 'user') {
                        this.api().columns([5]).visible(false);
                    }
                }
            },
            "ajax": {
                url: final_ajax_url,
                type: "get", // method  , by default get
                global: false,
                "data": function ( d ) {
                    $.extend( d, data);
                    d.status = $('.statusFilter').val();
                    d.location = locationId;
                    d.permissionStatus = $('.permissionStatusFilter').val();
                 },
                "error":function(){
                    // window.location.reload();
                }
            }
        });
        c.table = table;
        if(bSearching){
            c._tableSearchInput(element_id_name);
        }
        c._tableResetFilter();
        return table;
    };

    //Event added for table search
    c._tableSearchInput = function(element_id_name) {

        var r = $('#'+element_id_name+' tfoot tr');
        $('#'+element_id_name+' thead').append(r);
        var table = c.table;
        table.columns().every(function (colindex) {
            var column = this;
            column.search('');
            $('.tbl-filter-column').val('');
        //      table.ajax.reload(null, false);
            var tColumn = $('#'+element_id_name+' thead th').eq(this.index());
            // $('input', this.footer()).on('keyup', function () {
            //     column.search($.trim($(this).val()), false, false, true).draw();
            // });
            $('input', this.footer()).keyup(delay(function (e) {
                column.search($.trim($(this).val()), false, false, true).draw();
              }, 500));
            $('select', this.footer()).on('change', function () {
                // column.search($(this).val(), false, false, true);
                table.draw();
            });
        });
    };

    function delay(callback, ms) {
        var timer = 0;
        return function() {
          var context = this, args = arguments;
          clearTimeout(timer);
          timer = setTimeout(function () {
            callback.apply(context, args);
          }, ms || 0);
        };
      }
      
    //Event added for record per page
    c._tableResetFilter = function(){
        $(document).on('click','#clr_filter', function(event) {
            c._tableResetFilterDraw();
            $('input:checkbox').prop("checked", false);
        });
    };

    //Table Draw after reset table
    c._tableResetFilterDraw = function(){
        $('.tbl-filter-column').val('');
        var columns = c.table.columns();
        // columns.every(function(i) {
        //     debugger;
        //     var column = this;
        //     column.search('').draw();
        // });
        c.table.search( '' ).columns().search( '' ).draw();
        c.table.clear().draw();
        // c.table.DataTable().ajax.reload(null, false);
    };

    // Delete Record
    c._deleteRecord = function() {
        $(document).on('click', '.delete', function () {
            var id = $(this).attr('id');
            var url = $(this).attr('data-url');
            var tableName = $(this).attr('data-table_name');
            if(tableName == 'user_table'){
                var jobCheckUrl = $(this).attr('data-job_url');
                checkJob(jobCheckUrl,id);
            }
            if(tableName == 'company_table' || tableName == 'machine_table' || tableName == 'problem_table' || tableName == 'location_table'){
                var jobCheckUrl = $(this).attr('data-job_url');
                checkRelationship(jobCheckUrl,id);
            }
            swal({
                title: 'Are you sure?',
                text: text,
                icon: "warning",
                buttons: true,
                dangerMode: true,
                closeOnClickOutside: false,
            }).then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        url: url,
                        method: "delete",
                        data: {id: id},
                        success: function (response) {
                            if(response['msg'] != 'error') {
                                swal(response['msg'], {
                                    icon: response['icon'],
                                    closeOnClickOutside: false,
                                });
                                $('#'+tableName).DataTable().ajax.reload(null, false);
                            }
                        }
                    })
                }
            });
        });
    };

    // Change Status (Active/Inactive)
    c._changeStatus = function() {
        $(document).on('click', '.active_inactive', function () {
            var id = $(this).attr('id');
            var url = $(this).attr('data-url');
            var tableName = $(this).attr('data-table_name');
            var status = $(this).attr('data-status');
            if(tableName == 'user_table'){
                var jobCheckUrl = $(this).attr('data-job_url');
                checkJob(jobCheckUrl,id);
            }
            swal({
                title: 'Are you sure?',
                text: 'You want to change status!',
                icon: "warning",
                buttons: true,
                dangerMode: true,
                closeOnClickOutside: false,
            }).then((isConfirm) => {
                if (isConfirm) {
                    $.ajax({
                        url: url,
                        method: "POST",
                        data: {id: id, status: status},
                        success: function (response) {
                            swal(response['msg'], {
                                icon: response['icon'],
                                closeOnClickOutside: false,
                            });
                            $('#'+tableName).DataTable().ajax.reload(null, false);
                        }
                    })
                }
            });
        });
    };

    // Bulk Action Active/Inactive/Delete
    c._bulkAction = function() {
        // This function is used for un checking all checkbox.
        $("body").on('change', '.checkbox', function () {
            if ($(this).is(':unchecked')) {
                $(".allCheckbox").prop("checked", false);
            }
        });

        /*Mutiple checkbox checked or unchecked*/
        $(document).on('click', '.allCheckbox',function () {
            if ($(this).is(':checked')) {
                $('.checkbox').prop("checked", true);
            } else {
                $('.checkbox').prop("checked", false);
            }
        });

        $('body').on('click', '#action_submit', function (e) {
            var url = $(this).attr('data-url');
            var tableName = $(this).attr('data-table_name');
            var id = [], msg;
            $('.checkbox:checked').each(function () {
                id.push($(this).val());
            });
            var action = $("#action option:selected").val();
            if (action != "" && id.length > 0) {
                if (action == 'print'){
                    $.ajax({
                        url: url,
                        method: "POST",
                        global:true,
                        data: {ids: id, action: action},
                        success: function (response) {
                            var anchor = document.createElement('a');
                            anchor.href = response.path;
                            anchor.target = '_blank';
                            anchor.download = 'Machine-QR-Code.pdf';
                            anchor.click();
                        }
                    });
                    return;
                }
                if (action == 'active' || action == 'inactive' || action == 'Active' || action == 'Inactive') {
                    msg = 'You want to change status!';
                }
                else {
                    if(tableName == 'company_table' || tableName == 'machine_table' || tableName == 'problem_table' || tableName == 'location_table'){
                        var jobCheckUrl = $(this).attr('data-job_url');
                        checkRelationship(jobCheckUrl,id);
                    }
                    msg = text;
                }
                if(tableName == 'permission_table'){
                    msg = 'You want to change status!';
                }
                if(tableName == 'user_table'){
                    var jobCheckUrl = $(this).attr('data-job_url');
                    checkJob(jobCheckUrl,id);
                }
                
                swal({
                    title: 'Are you sure?',
                    text: msg,
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                    closeOnClickOutside: false,
                }).then((willDelete) => {
                    if (willDelete) {
                        $.ajax({
                            url: url,
                            method: "POST",
                            data: {ids: id, action: action},
                            success: function (response) {
                                swal(response['msg'], {
                                    icon: response['icon'],
                                    closeOnClickOutside: false,
                                });
                                $('#'+tableName).DataTable().ajax.reload(null, false);
                                $(".allCheckbox").prop("checked", false);
                            }
                        });

                    }
                });
            } else {
                var msgTxt;
                if (id.length <= 0) {
                    msgTxt = "Please select at least one checkbox";
                } else {
                    msgTxt = "Please select one option";
                }
                swal(msgTxt, {
                    icon: "info",
                });
            }
        });
    };
    window.oppenIpps = new oppenIpps();
})(jQuery);

/* image preview befor submiy */
function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#blah').attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
    }
}
$("#imgInp").change(function () {
    readURL(this);
});
$.validator.addMethod("noSpace", function (value, element) {
    return $.trim(value);
}, "This field is required");

/* check that own user's has assign any jobs in job listing page */
function checkJob(url,id){
    $.ajax({
        url: url,
        method: "POST",
        data: {id: id},
        success: function (response) {
            if(response){
                swal(response['msg'], {
                    icon: response['icon'],
                    closeOnClickOutside: false,
                });
            }
        }
    })
}

/* Hide error or success message */
setTimeout(function(){
    $('.alert-success').fadeOut(2000);
},2000);
setTimeout(function(){
    $('.alert-success').remove();
},3000);
$('.m-alert__close').find('button').on('click', function(){
    $('.alert-danger').remove();
})


/* Get Notification content */
function getContent(data,totalNotification){
    var existingNotifications = $('#topbar_notifications_notifications').find('.m-list-timeline__items').empty();
        newNotificationHtml = ``;
        $.each(data, function(key,value){
            newNotificationHtml += `
            <div id="`+value.id+`" data-url="`+value.url+`" class="m-list-timeline__item" style="cursor:pointer;">
                <span class="m-list-timeline__badge -m-list-timeline__badge--state-success"></span>
                <span class="m-list-timeline__text">`+value.content+`</span>
                <span class="m-list-timeline__time">`+value.time+`</span>
            </div>
            `;

        });
        $('.m-list-timeline__items').html(newNotificationHtml);
        $('#count-notification').text(totalNotification);
        if(totalNotification > 0){
            $('#data-count').text(totalNotification+' New');
            $('#data-count-label').text('User Notifications')
            $('#count-notification').css('display','unset');
            $('#alertDiv').css('display','unset');
        }else{
            $('#data-count').text('Not Notifications');
            $('#data-count-label').text('');
            $('#count-notification').css('display','none');
            $('#alertDiv').css('display','none');
        }
    }

/* Updata notification status */
$('#topbar_notifications_notifications').on('click','div.m-list-timeline__item', function(){
    var id= $(this).attr('id');
    redirectUrl = $(this).attr('data-url');
    $.ajax({
        url: updateNotification,
        method: "POST",
        data: {id: id},
        global:false,
        success: function (response) {
            window.location.replace(redirectUrl);
        }
    });
})
var text = 'Once deleted, you will not be able to recover this data!';

/* check that own user's has assign any jobs in job listing page */
function checkRelationship(url,id){
    $.ajax({
        url: url,
        method: "POST",
        data: {id: id},
        async: false,
        success: function (response) {
            if(response.counts > 0){
                text = response.msg;
                return response;
            }else{
                text = 'Once deleted, you will not be able to recover this data!';
            }
        }
    })
}
