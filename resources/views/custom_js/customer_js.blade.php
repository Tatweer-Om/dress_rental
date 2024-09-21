<script type="text/javascript">

   $(document).ready(function() {
       $('#add_customer_modal').on('hidden.bs.modal', function() {
           $(".add_customer")[0].reset();
           $('.customer_id').val('');


       });
       $('#all_customer').DataTable({
            "sAjaxSource": "{{ url('show_customer') }}",
            "bFilter": true,
            'pagingType': 'numbers',
            "ordering": true,
        });

       $('.add_customer').off().on('submit', function(e){
           e.preventDefault();
           var formdatas = new FormData($('.add_customer')[0]);
           var title=$('.customer_name').val();
           var number=$('.customer_number').val();
           var id=$('.customer_id').val();


           if(id!='')
           {
               if(title=="" )
               {
                   show_notification('error','<?php echo trans('messages.add_customer_name_lang',[],session('locale')); ?>'); return false;
               }

               if(number=="" )
               {
                   show_notification('error','<?php echo trans('messages.add_customer_phone_lang',[],session('locale')); ?>'); return false;
               }
               $('#global-loader').show();
               before_submit();
               var str = $(".add_customer").serialize();
               $.ajax({
                   type: "POST",
                   url: "{{ url('update_customer') }}",
                   data: formdatas,
                   contentType: false,
                   processData: false,
                   success: function(data) {
                       $('#global-loader').hide();
                       after_submit();
                       if(data.status==1)
                       {
                           show_notification('success','<?php echo trans('messages.data_update_success_lang',[],session('locale')); ?>');
                           $('#add_customer_modal').modal('hide');
                           $('#all_customer').DataTable().ajax.reload();
                           return false;
                       }

                   },
                   error: function(data)
                   {
                       $('#global-loader').hide();
                       after_submit();
                       show_notification('error','<?php echo trans('messages.data_update_failed_lang',[],session('locale')); ?>');
                       $('#all_customer').DataTable().ajax.reload();
                       console.log(data);
                       return false;
                   }
               });
           }
           else if(id==''){


               if(title=="" )
               {
                   show_notification('error','<?php echo trans('messages.add_customer_name_lang',[],session('locale')); ?>'); return false;

               }

               if(number=="" )
               {
                   show_notification('error','<?php echo trans('messages.add_customer_phone_lang',[],session('locale')); ?>'); return false;
               }
               $('#global-loader').show();
               before_submit();
               var str = $(".add_customer").serialize();
               $.ajax({
                   type: "POST",
                   url: "{{ url('add_customer') }}",
                   data: formdatas,
                   contentType: false,
                   processData: false,
                   success: function(data) {
                       $('#global-loader').hide();
                       after_submit();
                       if (data.status == 3) {
                       show_notification('error', '<?php echo trans('messages.customer_number_or_contact_exist_lang', [], session('locale')); ?>');
                       return false;
                       }

                       else if(data.status==1)
                       {
                           $('#all_customer').DataTable().ajax.reload();
                           show_notification('success','<?php echo trans('messages.data_add_success_lang',[],session('locale')); ?>');
                           $('#add_customer_modal').modal('hide');
                           $(".add_customer")[0].reset();

                           return false;
                       }
                   },
                   error: function(data)
                   {
                       $('#global-loader').hide();
                       after_submit();
                       show_notification('error','<?php echo trans('messages.data_add_failed_lang',[],session('locale')); ?>');
                       $('#all_customer').DataTable().ajax.reload();
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
           url : "{{ url('edit_customer') }}",
           method : "POST",
           data :   {id:id,_token: csrfToken},
           success: function(fetch) {
               $('#global-loader').hide();
               after_submit();
               if(fetch!=""){
                   // Define a variable for the image path

                   $(".customer_id").val(fetch.customer_id);
                   $(".customer_name").val(fetch.customer_name);
                   $(".customer_email").val(fetch.customer_email);
                   $(".dob").val(fetch.dob);
                   $(".address").val(fetch.address)
                   $(".customer_number").val(fetch.customer_number);
                   $(".customer_discount").val(fetch.discount);
                   if (fetch.gender == 1) {
                       $("#male").prop("checked", true);
                   }
                   else
                   {
                       $("#female").prop("checked", true);
                   }


                   $(".modal-title").html('<?php echo trans('messages.update_lang',[],session('locale')); ?>');

               }
           },
           error: function(html)
           {
               $('#global-loader').hide();
               after_submit();
               show_notification('error','<?php echo trans('messages.edit_failed_lang',[],session('locale')); ?>');
               console.log(html);
               return false;
           }
       });
   }

   function del(id) {
       Swal.fire({
           title:  '<?php echo trans('messages.sure_lang',[],session('locale')); ?>',
           text:  '<?php echo trans('messages.delete_lang',[],session('locale')); ?>',
           type: "warning",
           showCancelButton: !0,
           confirmButtonColor: "#3085d6",
           cancelButtonColor: "#d33",
           confirmButtonText: '<?php echo trans('messages.delete_it_lang',[],session('locale')); ?>',
           confirmButtonClass: "btn btn-primary",
           cancelButtonClass: "btn btn-danger ml-1",
           buttonsStyling: !1
       }).then(function (result) {
           if (result.value) {
               $('#global-loader').show();
               before_submit();
               var csrfToken = $('meta[name="csrf-token"]').attr('content');
               $.ajax({
                   url: "{{ url('delete_customer') }}",
                   type: 'POST',
                   data: {id: id,_token: csrfToken},
                   error: function () {
                       $('#global-loader').hide();
                       after_submit();
                       show_notification('error', '<?php echo trans('messages.delete_failed_lang',[],session('locale')); ?>');
                   },
                   success: function (data) {
                       $('#global-loader').hide();
                       after_submit();
                       $('#all_customer').DataTable().ajax.reload();
                       show_notification('success', '<?php echo trans('messages.delete_success_lang',[],session('locale')); ?>');
                   }
               });
           } else if (result.dismiss === Swal.DismissReason.cancel) {
               show_notification('success', '<?php echo trans('messages.safe_lang',[],session('locale')); ?>');
           }
       });
   }





    // Function to load customer profile data via AJAX


    // Example: Call the function when the page is ready or a customer is selected
    let customerId = $('#customer_id').val();

    // Replace with the actual customer ID dynamically
    loadCustomerProfileData(customerId);

    function loadCustomerProfileData(customerId) {
    $.ajax({
        url: "{{ url('customer_profile_data') }}",
        method: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content'),
            customer_id: customerId
        },
        success: function(response) {
            let bookingsTable = $('#all_profile_docs_1 tbody');
            let upcomingTable = $('#all_profile_docs_2 tbody');

            bookingsTable.empty();
            upcomingTable.empty();

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
        },
        error: function(xhr) {
            console.error('Error loading customer profile data: ', xhr.responseText);
        }
    });
}

</script>
