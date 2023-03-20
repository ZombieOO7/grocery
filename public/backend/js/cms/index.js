// This function is used to initialize the data table.
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
            // { "data": "checkbox", orderable: false, searchable: false },
            // { "data": "id" },
            { "data": "page_title" },
            { "data": "created_at" },
            //  { "data": "status" },
            { "data": "action", orderable: false, searchable: false },
        ];
        var order_coloumns = [[1, "desc"]];
        oppenIpps._generateDataTable('cms_table', cms_list_url, field_coloumns, order_coloumns);
    };
    window.oppenIppsAssessment = new oppenIppsAssessment();
})(jQuery);