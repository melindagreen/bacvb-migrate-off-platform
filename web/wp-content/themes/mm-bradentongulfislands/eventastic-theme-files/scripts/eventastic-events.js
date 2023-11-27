jQuery(document).ready(function($) {
    var datePickerDispFormat = 'MM/DD/YYYY';
    var datePickerEvalFormat = 'YYYY-MM-DD';

    //This is used to setup the date boxes
    $("input[type=date].eventasticDatePicker").each(function() {
        var options = {
            showDropdowns: false,
            singleDatePicker: true,
            autoUpdateInput: false,
            opens: "right",
            drops: "down",
            locale: {
                format: datePickerDispFormat,
                firstDay: 0
            }
        };

        // current value?
        if ($(this).val() != "") {
            options.startDate = moment($(this).val()).format(datePickerDispFormat);
        }

        // init it
        $(this).daterangepicker(options);

        // act upon selection
        $(this).on('show.daterangepicker', function(ev, picker) {
            if ($(this).val() != "") {
                var sd = moment($(this).val()).format(datePickerDispFormat);
                $(this).data('daterangepicker').setStartDate(sd);
            }
        });
        $(this).on('apply.daterangepicker', function(ev, picker) {
            if ($(this).val() != "") {
                // for comparison
                var triggerEl = $(this);
                var newDate = picker.startDate.format(datePickerEvalFormat);
                var areStart = (picker.element.attr('id').toLowerCase().indexOf('start'))
                    ? true
                    : false;

                // set our value
                $(this).val(newDate).trigger('change');

                // go through the other eventastic date pickers and set any that
                //  has a date less than this date to this date
                $("input[type=date].eventasticDatePicker").each(function() {
                    if ($(this).get(0) !== triggerEl.get(0)) {
                        var thisDate = moment($(this).val()).format(datePickerEvalFormat);
                        // if it's start, ensure end is after; opposite if not
                        if ( ( (areStart) && (thisDate > newDate) ) || ( (! areStart) && (thisDate < newDate) ) ) {
                            $(this).val(moment(newDate).format(datePickerEvalFormat));
                            // and change the related picker
                            var updatedDate = moment(newDate).format(datePickerDispFormat);
                            $(this).data('daterangepicker').setStartDate(updatedDate);
                            $(this).data('daterangepicker').setEndDate(updatedDate);
                        }                        
                    }
                });
            }
        });
        $(this).on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('').trigger('change');
        });
    });


    //This is used so that any part of a checkbox being click (IE: the label) will trigger the checkbox click
    $('.checkbox').click(function(e) {
        e.preventDefault();
        var checkbox = $(this).children('input');
        checkbox.prop("checked", !checkbox.prop("checked")).trigger('change');
    });

    //This is used to trigger the form submit and process an ajax request, in order to fire on any change to the filters
    //replace $(".filterSubmit").click() with something like $(".filters input").change()
    $(".filterSubmit").click(function() {
        //if one of these inputs is removed the ajax request will still work with a null value
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
        jQuery.post(wp_ajax.ajax_url, data, function(response) {
            //the ajax request html json element contains the new information to be put in the container
            $(".events").html(response.html);
        }, 'json');
    });

    //this logic controls the load more button to show hidden listings
    $('.events').on('click', '.showMoreButton', function (){
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
