jQuery(document).ready(function($) {
    // alert(1);
    var datePickerFormat = 'M/D/YYYY';
    $("input[type=date]").each(function() {
        var options = {
            showDropdowns: false,
            singleDatePicker: true,
            autoUpdateInput: false,
            // minDate: "<?php echo date(Constants::DATE_FORMAT_MYSQL) ?>",
            opens: "right",
            drops: "down",
            locale: {
                format: datePickerFormat,
                firstDay: 0
            }
        };

        // current value?
        if ($(this).val() != "") {
            options.startDate = moment($(this).val()).format(datePickerFormat);
        }

        // init it
        $(this).daterangepicker(options);

        // act upon selection
        $(this).on('show.daterangepicker', function(ev, picker) {
            if ($(this).val() != "") {
                var sd = moment($(this).val()).format(datePickerFormat);
                $(this).data('daterangepicker').setStartDate(sd);
            }
        });
        $(this).on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD'));
        });
        $(this).on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });
    });


    $('.checkbox').click(function(e) {
        e.preventDefault();
        var checkbox = $(this).children('input');
        checkbox.prop("checked", !checkbox.prop("checked")).trigger('change');
    });
z
    $(".filterSubmit").click(function() {
        var startDate = $('input[name="start_date"]').val();
        var endDate = $('input[name="end_date"]').val();
        var keyword = $('input[name="keyword"]').val();
        var categories = [];
        $('input[name="category"]:checked').each(function() {
            categories.push($(this).val());
        });
        var data = {
            'action': 'eventasticGetEvents',
            'startDate': startDate,
            'endDate': endDate,
            'keyword': keyword,
            'categories': categories
        };
        console.log(data);
        jQuery.post(wp_ajax.ajax_url, data, function(response) {
            console.log(response);
            $(".events").html(response.html);
        }, 'json');
    });
    $('.events').on('click', '.showMoreButton', function (){
        console.log('hi');
        var $i = 0;
        $(".events .event").each(function() {
            if ($i < 20 && $(this).hasClass('hidden')) {
                $(this).removeClass('hidden');
                $i++;
            }
        });
        if ($i < 20) {
            $(".showMoreButton").hide();
        }
    });
});
