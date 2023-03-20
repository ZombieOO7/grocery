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
            { "data": "checkbox", orderable: false, searchable: false },
            // { "data": "id" },
            { "data": "full_name" },
            {"data":"email"},
            { "data": "subject"},
             { "data": "message" },
             { "data": "created_at" },
            { "data": "action", orderable: false, searchable: false },
        ];
        var order_coloumns = [[5, "desc"]];
        oppenIpps._generateDataTable('contact_us_table', contact_us_list_url, field_coloumns, order_coloumns);
    };
    window.oppenIppsAssessment = new oppenIppsAssessment();
})(jQuery);