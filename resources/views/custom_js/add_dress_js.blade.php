<script type="text/javascript">
const rentDatePicker = flatpickr("#start_date", {
    defaultDate: new Date(),
    onChange: function(selectedDates, dateStr, instance) {
      // When rent_date changes, update return_date to ensure it's always greater
      returnDatePicker.set('minDate', dateStr);
      
    }
  });

  const returnDatePicker = flatpickr("#end_date", {
    defaultDate: new Date(),
    onChange: function() {
      
    }
  });
function maint(id) {
    $('#return_maint_modal .maint_id').val(id);

    }
    function comp_maint(id) {
    $('#maint_complete_modal .maint_id').val(id);

    }
    $(document).ready(function() {

    $('.add_maint').on('submit', function(event) {
        event.preventDefault();
        $('#global-loader').show();
        var formData = new FormData(this);
        var title=$('#maint_name').val();
        if(title=="" )
        {
            show_notification('error','<?php echo trans('messages.add_maint_name_lang',[],session('locale')); ?>'); return false;
        }
        before_submit();
        $.ajax({
            url: "{{ url('maint_dress') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            error: function() {
                $('#global-loader').hide();
                show_notification('error', '<?php echo trans('messages.maintenance_failed_lang',[],session('locale')); ?>');
            },
            success: function(data) {
                after_submit();
                $('#global-loader').hide();
                show_notification('success', '<?php echo trans('messages.send_to_maintenance_lang',[],session('locale')); ?>');
                $('#all_dress').DataTable().ajax.reload();
                $('#return_maint_modal').modal('hide'); // Close the modal
            }
        });
    });

    $('#all_maint').DataTable({
            "sAjaxSource": "{{ url('show_maint_dress') }}",
            "bFilter": true,
            'pagingType': 'numbers',
            "ordering": true,
        });

    $('.maint_comp').on('submit', function(event) {
        event.preventDefault();
        $('#global-loader').show();
        var formData = new FormData(this);
        var cost=$('#maint_cost').val();
        if(cost=="" )
        {
            show_notification('error','<?php echo trans('messages.add_maint_cost_lang',[],session('locale')); ?>'); return false;
        }
        before_submit();
        $.ajax({
            url: "{{ url('maint_dress_comp') }}",
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            error: function() {
                $('#global-loader').hide();
                show_notification('error', '<?php echo trans('messages.maintenance_finish_failed_lang',[],session('locale')); ?>');
                $('#all_maint').DataTable().ajax.reload();
            },
            success: function(data) {
                after_submit();
                $('#global-loader').hide();
                show_notification('success', '<?php echo trans('messages.maintenance_finish_success_lang',[],session('locale')); ?>');
                $('#maint_complete_modal').modal('hide'); // Close the modal
                $('#all_maint').DataTable().ajax.reload();
            }
        });
    });




        $('#add_dress_modal').on('hidden.bs.modal', function() {
            $(".add_dress")[0].reset();
            $('.dress_id').val('');
            var imagePath = '{{ asset('custom_images/dummy_image/cover-image-icon.png') }}';
            $('#ad_cover_preview').attr('src',imagePath);
            $('#attachment-holder').html('');
            $('#all_attribute').html('');
        });
        $('#all_dress').DataTable({
            "sAjaxSource": "{{ url('show_dress') }}",
            "bFilter": true,
            'pagingType': 'numbers',
            "ordering": true,
        });
        $('#ad_cover_container').on('click',function(e){
            $('#ad_cover').trigger('click');
        });

        $('#ad_cover').on('change',function(e){
            $('#ad_cover_preview').attr('src', window.URL.createObjectURL(this.files[0]));
        });


        $('#btn-ad-images').on('click',function(e){
            $('#ad_images').trigger('click');
        });

        // upload atttachments
        $('#ad_images').on('change',function(e){
            var attachments= $(this)[0].files.length;
            var dress_id     = $('.dress_id').val();
            var form_data = new FormData();
            form_data.append('dress_id',dress_id);
            if(attachments>0)
            {
                for (var x = 0; x <attachments; x++)
                {
                    form_data.append('attachments[]',$(this)[0].files[x]);
                }
            }
            $.ajax({
                url:"{{ url('upload_attachments') }}",
                type:'POST',
                processData:false,
                contentType: false,
                data:form_data,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success:function(response)
                {
                    $('#attachment-holder').html(response.images).fadeIn('slow');
                },
            });
        });

        // remove attachments
        $(document).on('click','.rmv-attachment',function(e){
            e.preventDefault();
            var img = $(this).closest('div').find('img').attr('src');
            var form_data = new FormData();
            form_data.append('img',img);
            $.ajax({
                url:"{{ url('remove_attachments') }}",
                type:'POST',
                processData:false,
                contentType: false,
                data:form_data,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                context:this,
                success:function(response)
                {
                    // alert(response);
                    $(this).parent().parent().remove();
                }
            });
        });
        // remove edit attachments
        $(document).on('click','.e-rmv-attachment',function(e){
            e.preventDefault();
            var image_id     = $(this).attr('id');
            var dress_id     = $('.dress_id').val();
            var img = $(this).closest('div').find('img').attr('src');
            var form_data = new FormData();
            form_data.append('img',img);
            form_data.append('image_id',image_id);
            form_data.append('dress_id',dress_id);
            $.ajax({
                url:"{{ url('e_remove_attachments') }}",
                type:'POST',
                processData:false,
                contentType: false,
                data:form_data,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                context:this,
                success:function(response)
                {
                    $(this).parent().parent().remove();
                    $('#attachment-holder').html(response.images).fadeIn('slow');
                }
            });
        });

        // add attribute
        $('#add_attribute').on('click',function(e){
            $('#all_attribute').append(`<div class="row attribute_div">
                                    <div class="col-md-4">
                                        <input type="hidden" class="attribute_id" name="attribute_id[]" value="">
                                        <div class="mb-3">
                                            <label for="attribute_name" class="form-label"><?php echo trans('messages.attribute_name_lang', [], session('locale')) ; ?></label>
                                            <input class="form-control attribute_name" name="attribute_name[]" type="text">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="attribute_notes" class="form-label"><?php echo trans('messages.notes_lang', [], session('locale')) ; ?></label>
                                            <textarea class="form-control attribute_notes" rows="3" name="attribute_notes[]"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="mb-3">
                                            <button type="button" style="margin-top: 40px;" class="btn btn-primary del_attribute"><i class="fas fa-trash"></i></button>
                                        </div>
                                    </div>
                                </div>`);
        });
        // del div attribute
        $(document).on('click', '.del_attribute', function () {
            $(this).closest('.attribute_div').remove(); // Removes the closest parent div with class 'attribute_div'
        });


        $('.add_dress').off().on('submit', function(e){
            e.preventDefault();
            var formdatas = new FormData($('.add_dress')[0]);
            var title=$('.dress_name').val();
            var sku=$('.sku').val();
            var category_name=$('.category_name').val();
            var color_name=$('.color_name').val();
            var size_name=$('.size_name').val();
            var price=$('.price').val();
            var id=$('.dress_id').val();

            if(id!='')
            {
                if(title=="" )
                {
                    show_notification('error','<?php echo trans('messages.add_dress_name_lang',[],session('locale')); ?>'); return false;
                }
                if(sku=="" )
                {
                    show_notification('error','<?php echo trans('messages.add_sku_lang',[],session('locale')); ?>'); return false;
                }
                if(category_name=="" )
                {
                    show_notification('error','<?php echo trans('messages.add_category_name_lang',[],session('locale')); ?>'); return false;
                }
                if(color_name=="" )
                {
                    show_notification('error','<?php echo trans('messages.add_color_name_lang',[],session('locale')); ?>'); return false;
                }
                if(size_name=="" )
                {
                    show_notification('error','<?php echo trans('messages.add_size_name_lang',[],session('locale')); ?>'); return false;
                }
                if(price=="" )
                {
                    show_notification('error','<?php echo trans('messages.add_price_lang',[],session('locale')); ?>'); return false;
                }
                var error=0;
                $('.attribute_name').each(function () {
                    if ($(this).val().trim() == '') {
                        error+=1;
                    }
                });
                if(error>0)
                {
                    show_notification('error','<?php echo trans('messages.add_attribute_name_lang',[],session('locale')); ?>'); return false;
                }


                $('#global-loader').show();
                before_submit();
                var str = $(".add_dress").serialize();
                $.ajax({
                    type: "POST",
                    url: "{{ url('update_dress') }}",
                    data: formdatas,
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        $('#global-loader').hide();
                        after_submit();
                        show_notification('success','<?php echo trans('messages.data_updated_successful_lang',[],session('locale')); ?>');
                        $('#add_dress_modal').modal('hide');
                        $('#all_dress').DataTable().ajax.reload();
                        return false;
                    },
                    error: function(data)
                    {
                        $('#global-loader').hide();
                        after_submit();
                        show_notification('error','<?php echo trans('messages.data_updated_failed_lang',[],session('locale')); ?>');
                        $('#all_dress').DataTable().ajax.reload();
                        console.log(data);
                        return false;
                    }
                });
            }
            else if(id==''){


                if(title=="" )
                {
                    show_notification('error','<?php echo trans('messages.add_dress_name_lang',[],session('locale')); ?>'); return false;
                }
                if(sku=="" )
                {
                    show_notification('error','<?php echo trans('messages.add_sku_lang',[],session('locale')); ?>'); return false;
                }
                if(category_name=="" )
                {
                    show_notification('error','<?php echo trans('messages.add_category_name_lang',[],session('locale')); ?>'); return false;
                }
                if(color_name=="" )
                {
                    show_notification('error','<?php echo trans('messages.add_color_name_lang',[],session('locale')); ?>'); return false;
                }
                if(size_name=="" )
                {
                    show_notification('error','<?php echo trans('messages.add_size_name_lang',[],session('locale')); ?>'); return false;
                }
                if(price=="" )
                {
                    show_notification('error','<?php echo trans('messages.add_price_lang',[],session('locale')); ?>'); return false;
                }
                var error=0;
                $('.attribute_name').each(function () {
                    if ($(this).val().trim() == '') {
                        error+=1;
                    }
                });
                if(error>0)
                {
                    show_notification('error','<?php echo trans('messages.add_attribute_name_lang',[],session('locale')); ?>'); return false;
                }

                $('#global-loader').show();
                before_submit();
                var csrfToken = $('meta[name="csrf-token"]').attr('content');
                var formdatas = new FormData($(".add_dress")[0]); // Create FormData
                formdatas.append('_token', csrfToken);
                var str = $(".add_dress").serialize();
                $.ajax({
                    type: "POST",
                    url: "{{ url('add_dress') }}",
                    data: formdatas,
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        $('#global-loader').hide();
                        after_submit();
                        $('#all_dress').DataTable().ajax.reload();
                        show_notification('success','<?php echo trans('messages.data_added_successful_lang',[],session('locale')); ?>');
                        $('#add_dress_modal').modal('hide');
                        $(".add_dress")[0].reset();
                        return false;
                        },
                    error: function(data)
                    {
                        $('#global-loader').hide();
                        after_submit();
                        show_notification('error','<?php echo trans('messages.data_added_failed_lang',[],session('locale')); ?>');
                        $('#all_dress').DataTable().ajax.reload();
                        console.log(data);
                        return false;
                    }
                });

            }

        });
    });
    function edit(id){
        $('#global-loader').show();
        before_submit();
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        $.ajax ({
            dataType:'JSON',
            url : "{{ url('edit_dress') }}",
            method : "POST",
            data :   {id:id,_token: csrfToken},
            success: function(fetch) {
                $('#global-loader').hide();
                after_submit();
                if(fetch!=""){

                    // / Define a variable for the image path
                    var imagePath = '{{ asset('custom_images/dummy_image/cover-image-icon.png') }}';

                    // Check if the category_image is present and not an empty string
                    if (fetch.dress_image && fetch.dress_image !== "") {
                        imagePath = '{{ asset('custom_images/dress_image/') }}/' + fetch.dress_image;
                    }
                    $('#ad_cover_preview').attr('src',imagePath);
                    $("#attachment-holder").html(fetch.all_images);
                    $("#all_attribute").html(fetch.attributes);
                    $(".dress_name").val(fetch.dress_name);
                    $(".sku").val(fetch.sku);
                    $(".category_name").val(fetch.category_name);
                    $(".brand_name").val(fetch.brand_name);
                    $(".color_name").val(fetch.color_name);
                    $(".size_name").val(fetch.size_name);
                    $(".price").val(fetch.price);
                    $(".condition").val(fetch.condition);
                    $(".notes").val(fetch.notes);
                    $(".dress_id").val(fetch.dress_id);
                    $(".modal-title").html('<?php echo trans('messages.update_data_lang',[],session('locale')); ?>');
                }
            },
            error: function(html)
            {
                $('#global-loader').hide();
                after_submit();
                show_notification('error','<?php echo trans('messages.data_edit_failed_lang',[],session('locale')); ?>');
                console.log(html);
                return false;
            }
        });
    }


    // Example: Call the function when the page is ready or a customer is selected
    let dressId = $('#dress_id').val();

    // Replace with the actual dress ID dynamically
    loaddressProfileData(dressId);

    function loaddressProfileData(dressId) {
    $.ajax({
        url: "{{ url('dress_profile_data') }}",
        method: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            dress_id: dressId
        },
        success: function(response) {
            let bookingsTable = $('#all_profile_docs_1 tbody');
            let upcomingTable = $('#all_profile_docs_2 tbody');
            let currentBookings = response.current_bookings;
            let bookingList = $('#current_bookings');
            $('#booking_count').text(response.total_bookings || 0);
            $('#total_payment').text(response.total_amount || 0);
            $('#total_panelty').text(response.total_panelty || 0);
            $('#up_booking').text(response.upcoming_bookings_count || 0);

            bookingsTable.empty();
            upcomingTable.empty();
            bookingList.empty();


            // Loop through bookings and append to the table
            $.each(response.bookings, function(index, booking) {
                let bill = Array.isArray(booking.bills) && booking.bills.length > 0 ? booking.bills[0] : null;
                let bookingRow = `
                    <tr>
                        <td style="text-align:center; width:5%;">${index + 1}</td>
                        <td style="text-align:center;width:25%;">
                            <span>${'{{ trans("messages.dress_name_lang") }}: '}${booking.dress ? booking.dress.dress_name : '{{ trans("messages.na_lang") }}'}</span><br>
                            <span>${'{{ trans("messages.brand_name_lang") }}: '}${booking.dress && booking.dress.brand ? booking.dress.brand.brand_name : '{{ trans("messages.na_lang") }}'}</span><br>
                            <span>${'{{ trans("messages.category_name_lang") }}: '}${booking.dress && booking.dress.category ? booking.dress.category.category_name : '{{ trans("messages.na_lang") }}'}</span><br>
                            <span>${'{{ trans("messages.color_lang") }}: '}${booking.dress && booking.dress.color ? booking.dress.color.color_name : '{{ trans("messages.na_lang") }}'}</span><br>
                            <span>${'{{ trans("messages.size_lang") }}: '}${booking.dress && booking.dress.size ? booking.dress.size.size_name : '{{ trans("messages.na_lang") }}'}</span>
                        </td>
                        <td style="text-align:center; width:25%;">
                            <span>${'{{ trans("messages.booking_no_lang") }}: '}${booking.booking_no || '{{ trans("messages.na_lang") }}'}</span><br>
                            <span>${'{{ trans("messages.booking_date_lang") }}: '}${booking.booking_date || '{{ trans("messages.na_lang") }}'}</span><br>
                            <span>${'{{ trans("messages.return_date_lang") }}: '}${booking.return_date || '{{ trans("messages.na_lang") }}'}</span><br>
                            <span>${'{{ trans("messages.rent_date_lang") }}: '}${booking.rent_date || '{{ trans("messages.na_lang") }}'}</span><br>
                            <span>${'{{ trans("messages.duration_lang") }}: '}${booking.duration || '{{ trans("messages.na_lang") }}'} days</span>
                        </td>
                        <td style="text-align:center; width:25%;">
                            <span>${'{{ trans("messages.rent_price_lang") }}: '}${bill ? bill.total_price : '{{ trans("messages.na_lang") }}'}</span><br>
                            <span>${'{{ trans("messages.discount_lang") }}: '}${bill ? bill.total_discount : '{{ trans("messages.na_lang") }}'}</span><br>
                            <span>${'{{ trans("messages.total_penalty_lang") }}: '}${bill ? bill.total_penalty : '{{ trans("messages.na_lang") }}'}</span><br>
                            <span>${'{{ trans("messages.grand_total_lang") }}: '}${bill ? bill.grand_total : '{{ trans("messages.na_lang") }}'}</span><br>
                            <span>${'{{ trans("messages.remaining_lang") }}: '}${bill ? bill.total_remaining : '{{ trans("messages.na_lang") }}'}</span>
                        </td>
                        <td style="text-align:center; width:20%;">
                            <span>${'{{ trans("messages.added_by_lang") }}: '}${booking.added_by || '{{ trans("messages.na_lang") }}'}</span><br>
                            <span>${'{{ trans("messages.created_at_lang") }}: '}${booking.created_at ? get_date_only(booking.created_at) : '{{ trans("messages.na_lang") }}'}</span>
                        </td>
                    </tr>
                `;
                bookingsTable.append(bookingRow);
            });

            $('#all_profile_docs_1').DataTable({
        destroy: true,  // Allows re-initializing table multiple times
        responsive: true
    });

            // Loop through upcoming bookings if needed
            $.each(response.up_bookings, function(index, booking) {
                let bill = Array.isArray(booking.bills) && booking.bills.length > 0 ? booking.bills[0] : null;

                let upcomingRow = `
                    <tr>
                        <td style="text-align:center;">${index + 1}</td>
                        <td style="text-align:center;width:25%;">
                            <span>${'{{ trans("messages.dress_name_lang") }}: '}${booking.dress ? booking.dress.dress_name : '{{ trans("messages.na_lang") }}'}</span><br>
                            <span>${'{{ trans("messages.brand_name_lang") }}: '}${booking.dress && booking.dress.brand ? booking.dress.brand.brand_name : '{{ trans("messages.na_lang") }}'}</span><br>
                            <span>${'{{ trans("messages.category_name_lang") }}: '}${booking.dress && booking.dress.category ? booking.dress.category.category_name : '{{ trans("messages.na_lang") }}'}</span><br>
                            <span>${'{{ trans("messages.color_lang") }}: '}${booking.dress && booking.dress.color ? booking.dress.color.color_name : '{{ trans("messages.na_lang") }}'}</span><br>
                            <span>${'{{ trans("messages.size_lang") }}: '}${booking.dress && booking.dress.size ? booking.dress.size.size_name : '{{ trans("messages.na_lang") }}'}</span>
                        </td>
                        <td style="text-align:center; width:25%;">
                            <span>${'{{ trans("messages.booking_no_lang") }}: '}${booking.booking_no || '{{ trans("messages.na_lang") }}'}</span><br>
                            <span>${'{{ trans("messages.booking_date_lang") }}: '}${booking.booking_date || '{{ trans("messages.na_lang") }}'}</span><br>
                            <span>${'{{ trans("messages.return_date_lang") }}: '}${booking.return_date || '{{ trans("messages.na_lang") }}'}</span><br>
                            <span>${'{{ trans("messages.rent_date_lang") }}: '}${booking.rent_date || '{{ trans("messages.na_lang") }}'}</span><br>
                            <span>${'{{ trans("messages.duration_lang") }}: '}${booking.duration || '{{ trans("messages.na_lang") }}'} days</span>
                        </td>
                        <td style="text-align:center; width:25%;">
                            <span>${'{{ trans("messages.rent_price_lang") }}: '}${bill ? bill.total_price : '{{ trans("messages.na_lang") }}'}</span><br>
                            <span>${'{{ trans("messages.discount_lang") }}: '}${bill ? bill.total_discount : '{{ trans("messages.na_lang") }}'}</span><br>
                            <span>${'{{ trans("messages.total_penalty_lang") }}: '}${bill ? bill.total_penalty : '{{ trans("messages.na_lang") }}'}</span><br>
                            <span>${'{{ trans("messages.grand_total_lang") }}: '}${bill ? bill.grand_total : '{{ trans("messages.na_lang") }}'}</span><br>
                            <span>${'{{ trans("messages.remaining_lang") }}: '}${bill ? bill.total_remaining : '{{ trans("messages.na_lang") }}'}</span>
                        </td>
                        <td style="text-align:center; width:20%;">
                            <span>${'{{ trans("messages.added_by_lang") }}: '}${booking.added_by || '{{ trans("messages.na_lang") }}'}</span><br>
                            <span>${'{{ trans("messages.created_at_lang") }}: '}${booking.created_at ? get_date_only(booking.created_at) : '{{ trans("messages.na_lang") }}'}</span>
                        </td>
                    </tr>
                `;
                upcomingTable.append(upcomingRow);
            });

            if (currentBookings.length > 0) {
                $.each(currentBookings, function(index, booking) {
                    let bookingItem = `
                        <a href="javascript: void(0);" class="list-group-item text-muted pb-3 pt-0 px-2">
                            <div class="d-flex align-items-center">
                                <div class="flex-grow-1 overflow-hidden">
                                    <h3 class="font-size-20 text-truncate">${booking.dress.dress_name || 'N/A'}</h3>
                                      <p class="text-danger">${'{{ trans("messages.duration_lang") }}: '} ${booking.duration || 'N/A'} days  </p>
                                    <p class="text-danger">${'{{ trans("messages.rent_date_lang") }}: '} ${booking.booking_date || 'N/A'}  </p>
                                      <p class="text-danger">${'{{ trans("messages.return_date_lang") }}: '} ${booking.return_date || 'N/A'}  </p>
                                </div>
                                <div class="fs-1">
                                    <i class="mdi mdi-calendar"></i>
                                </div>
                            </div>
                        </a>
                    `;
                    bookingList.append(bookingItem);
                });
            } else {
                bookingList.append('<a href="javascript: void(0);" class="list-group-item text-muted pb-3 pt-0 px-2">{{ trans("messages.no_booking_lang") }}</a>');
            }
        },

        error: function(xhr) {
            console.error('Error loading dress profile data: ', xhr.responseText);
        }

    });
}





</script>
