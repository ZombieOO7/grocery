$(document).ready(function () {
    var url = $('#email_template_table').attr('data-url'); // This variable is used for getting route name or url.
    
    // This funtion is used to initialize the data table.
    (function ($) {
        var oppenIppsAssessment = function () {
            $(document).ready(function () {
                c._initialize();
            });
        };
        var c = oppenIppsAssessment.prototype;
    
        c._initialize = function () {
            c._listingView();
        };
    
        c._listingView = function () {
            var field_coloumns = [
                // {
                //     "data": "checkbox",
                //     orderable: false,
                //     searchable: false
                // },
                {
                    "data": "title"
                },
                {
                    "data": "subject"
                },
                {
                    "data": "created_at"
                },
                // {
                //     "data": "status"
                // },
                {
                    "data": "action",
                    orderable: false,
                    searchable: false
                },
            ];
            var order_coloumns = [[2,"desc"]];
            table = oppenIpps._generateDataTable('email_template_table', url, field_coloumns, order_coloumns, data = null);
        };
        window.oppenIppsAssessment = new oppenIppsAssessment();
    })(jQuery);

});
