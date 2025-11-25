jQuery(document).ready(function($) {
    var datePickerFormat = 'M/D/YYYY';
    var views = {};
    views.listings = {
        'grid' : {
            'box' : ''
        }
    };
    views.listings.grid.box = `
        <div class='listings-box'>
            <button class="add-to-trip" onClick="addToTrip({{post_id}})">Add To Trip</button>
            <div class="img-wrapper">
                <img src="">
            </div>
            <h2>{{post_title}}</h2>

        </div>
    `;
    $.ajax({
        url:  ajax_pagination.ajaxurl,
        type: 'post',
        async:    true,
        cache:    false,
        dataType: 'json',        
        data: {
            action: 'partnerportal_pagination',
            params: {
                //'paged': window.ajaxPage,
                'post_types' : 'listings',
                //'category_id': $category_id,
                //'date' : $date,
                //'keyword' : $keyword
            }
        },
        fail: function(result){
            console.log('fail', result);
        },
        success: function( result ) {
            console.log('success');
            console.log( result);
            var wordLoadMore = "Load More";
            if(result.posts.length > 0){
                build_grid( result.posts );
            }
            else{
                $('#' + $id).find('#load_more').html('All Posts Are Loaded').addClass('inactive');
            } 
            window.ajaxPage++;
        }
    })    
    function build_grid( posts ){
        var output = "";
        var template = views.listings.grid.box;
        console.log(template);        
        $.each( posts, function(k,post){
            //console.log('POST',post);
console.log('before replace');
            output += replace_content( template, 'post_title', post.post_title );
            output += replace_content( template, 'post_id', post.post_id );
           // $('#' + $id).find('.blog-container').append(div);
            //$('#' + $id).find('#load_more').html(wordLoadMore).removeClass('inactive');
        })
        $('#partner-portal-listings').append(output);

    }
    function replace_content( template, key, value ){
        key = "{{" + key + "}}";
        const re = new RegExp(`${key}`, 'g');
        template = template.replace(re, value);
        return template;
    }      
    //This is used to setup the date boxes
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
            $(this).val(picker.startDate.format('YYYY-MM-DD')).trigger('change');
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
        console.log(data);
        jQuery.post(wp_ajax.ajax_url, data, function(response) {
            console.log(response);
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
