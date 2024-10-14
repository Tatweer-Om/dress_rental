<script>
var calendar;
$(document).ready(function() {
    var calendarEl = document.getElementById('dress_calendar');
    calendar = new FullCalendar.Calendar(calendarEl, {
        plugins: ["bootstrap", "interaction", "dayGrid", "timeGrid"],
        editable: true,
        droppable: true,
        selectable: true,
        initialView: "dayGridMonth",
        themeSystem: "bootstrap",
        header: {
            left: "prev,next today",
            center: "title",
            right: "dayGridMonth,timeGridWeek,timeGridDay,listMonth"
        },
        events: [], // Start with an empty array of events
 
        events: [], // Start with an empty array of events
        // Event click callback for booking events
        eventClick: function(info) {
                // Prevent the default action
                info.jsEvent.preventDefault();

                // Display a modal when an event is clicked
                $('#add_booking_info_modal').modal('show');

                // Call an onclick function and pass event data
                get_booking_detail(info.event.extendedProps.booking_id);

                // Example: Log event details
                console.log("Clicked event:", info.event);
            },

            // Date click callback for empty dates
        dateClick: function(info) {
            // Check if a dress is selected
            var dressId = $('#dress_id').val();
            if (dressId == "") {
                // Prevent date click action
                info.jsEvent.preventDefault();
                return;
            }
            if (disabledDates.includes(info.dateStr)) {
                // If the date is disabled, prevent any action
                return; // Do nothing
            }
        // Get the clicked date in YYYY-MM-DD format
            var bookingDate = info.dateStr;
            

            // Redirect to the booking route with the clicked date
            var bookingUrl = "{{ url('new_booking') }}/"+ bookingDate+"/"+dressId;

            // Redirect to the booking page
            window.location.href = bookingUrl;
        },
         // Disable specific dates
        datesSet: function(dateInfo) {
            disableDates();
        }
    });

    // Initialize the calendar
    calendar.render();

    
});
let disabledDates = []; // Array to store disabled dates
function disableDates(bookings) {
    var day_before = $('#day_before').val();
    day_before = parseFloat(day_before) + 1;
  
    bookings.forEach(function(booking) {
        var startDate = new Date(booking.rent_date);
        var endDate = new Date(booking.return_date);
        
        // Calculate the disable range
        var disableStartDate = new Date(startDate);
        var disableEndDate = new Date(endDate);
        disableStartDate.setDate(disableStartDate.getDate() - day_before); // 2 days before
        disableEndDate.setDate(disableEndDate.getDate() + day_before); // 2 days after

        // Loop through each day in the range and add to disabledDates
        var currentDate = disableStartDate;
        while (currentDate <= disableEndDate) {
            disabledDates.push(currentDate.toISOString().split('T')[0]); // Add date in YYYY-MM-DD format
            currentDate.setDate(currentDate.getDate() + 1);
        }
    });
}
// Listen for changes on the dress select box
    function get_calender_bookings()
    {
        var dressId = $('#dress_id').val();

        if (dressId) {
            // AJAX request to fetch bookings
            var csrfToken = $('meta[name="csrf-token"]').attr('content');
            $('#global-loader').show();
            $.ajax({
                url: "<?php echo url('get_calender_bookings') ?>", // Change this to your controller method
                type: "POST",
                data: { dress_id: dressId,_token: csrfToken},
                dataType: "json",
                success: function(data) {
                    $('#global-loader').hide();
                    // Clear previous events
                    calendar.removeAllEvents();

                    // Add new events
                    data.forEach(function(booking) {
                        var endDate = new Date(booking.return_date);
                        endDate.setDate(endDate.getDate() + 1); // Add 1 day to make it inclusive
                        calendar.addEvent({
                            title: "Booking No: " + booking.booking_no + " - " + booking.dress_name,
                            start: booking.rent_date,  // Rent date in YYYY-MM-DD format
                            end: endDate.toISOString().split('T')[0],
                            allDay: true, // Set to true for all-day event
                            booking_id: booking.booking_id // Add the booking ID to the event
                        });
                    });
                    disableDates(data);
                },
                error: function() {
                    console.error("Error fetching bookings.");
                }
            });
        } else {
            // If no dress is selected, clear events
            calendar.removeAllEvents();
        }
    }


    // booking further detail
    // function get_booking_detail
    function get_booking_detail(booking_id){
       
       var csrfToken = $('meta[name="csrf-token"]').attr('content');
       $('#global-loader').show();
       $.ajax ({
           url : "{{ url('get_booking_detail') }}",
           method : "POST",
           data :   {booking_id:booking_id,_token: csrfToken},
           success: function(response) {
               $('#global-loader').hide();
               $('#get_booking_detail').html(response.booking_detail)
               $('#booking_no_id').html(response.booking_no +" "+ response.status) 
               
           },
           error: function(response)
           {
               show_notification('error','<?php echo trans('messages.data_get_failed_lang',[],session('locale')); ?>');
               console.log(response);
               return false;
           }
       });
   }
 
     //   payment
     // get payment detail
     function get_payment(bill_id,booking_id) {
         $('.bill_id').val(bill_id);
         $('.bill_booking_id').val(booking_id);
         var csrfToken = $('meta[name="csrf-token"]').attr('content');
         $('#global-loader').show();
         $.ajax ({
             url : "{{ url('get_payment') }}",
             method : "POST",
             data :   {bill_id:bill_id,booking_id:booking_id,_token: csrfToken},
             success: function(response) {
                 $('#global-loader').hide();
                 $('#bill_total_amount').val(response.total_amount);
                 $('#bill_remaining_amount').val(response.remaining_total);
             },
             error: function(response)
             {
                 show_notification('error','<?php echo trans('messages.data_get_failed_lang',[],session('locale')); ?>');
                 console.log(response);
                 return false;
             }
         });
     }
 
     $('#bill_paid_amount').on('keyup change', function() {
         // Get the values of both inputs
         var remainingAmount = parseFloat($('#bill_remaining_amount').val());
         var paidAmount = parseFloat($(this).val());
 
         // Check if paidAmount is greater than remainingAmount
         if (paidAmount > remainingAmount) {
             // Show error message
             show_notification('error','<?php echo trans('messages.validation_amount_cannot_greater_remaining_lang',[],session('locale')); ?>'); 
 
             // Set bill_paid_amount to the remainingAmount
             $(this).val(remainingAmount);
         } 
     });
 
     $('.add_payment').off().on('submit', function(e){
         e.preventDefault();
         var formdatas = new FormData($('.add_payment')[0]);
         var bill_paid_amount=$('.bill_paid_amount').val();
         var bill_booking_id=$('.bill_booking_id').val();
         var bill_id=$('.bill_id').val();
         
         if(bill_paid_amount=="" )
         {
             show_notification('error','<?php echo trans('messages.add_paid_amount_lang',[],session('locale')); ?>'); return false;
 
         }
         $('#global-loader').show();
         before_submit();
         var str = $(".add_payment").serialize();
         $.ajax({
             type: "POST",
             url: "{{ url('add_payment') }}",
             data: formdatas,
             contentType: false,
             processData: false,
             success: function(data) {
                 $('#global-loader').hide();
                 after_submit();
                 $('.bill_paid_amount').val('');
                 $('.bill_notes').val('');
                 show_notification('success','<?php echo trans('messages.payment_add_successfully',[],session('locale')); ?>');
                 get_payment(bill_id,bill_booking_id)
                 return false;
             },
             error: function(data)
             {
                 $('#global-loader').hide();
                 after_submit();
                 show_notification('error','<?php echo trans('messages.payment_add_failed',[],session('locale')); ?>');
                 console.log(data);
                 return false;
             }
         });
 
     });
     // delete payment
     function del_payment(id) {
         var csrfToken = $('meta[name="csrf-token"]').attr('content');
         Swal.fire({
             title:  '<?php echo trans('messages.sure_lang',[],session('locale')); ?>',
             text:  '<?php echo trans('messages.wanna_delete_lang',[],session('locale')); ?>',
             type: "warning",
             showCancelButton: !0,
             confirmButtonColor: "#3085d6",
             cancelButtonColor: "#d33",
             confirmButtonText:  '<?php echo trans('messages.delete_lang',[],session('locale')); ?>',
             confirmButtonClass: "btn btn-primary",
             cancelButtonClass: "btn btn-danger ml-1",
             buttonsStyling: !1
         }).then(function (result) {
             if (result.value) {
                 $('#global-loader').show();
                 before_submit();
                 $.ajax({
                     url: "{{ url('delete_payment') }}",
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
                         $('#ptr'+id).remove();
                         show_notification('success', '<?php echo trans('messages.delete_success_lang',[],session('locale')); ?>');
                     }
                 });
             } else if (result.dismiss === Swal.DismissReason.cancel) {
                 show_notification('success', '<?php echo trans('messages.data_is_safe_lang',[],session('locale')); ?>');
             }
         });
     }
 
     // delete booking
     function delete_booking(id) {
         Swal.fire({
             title:  '<?php echo trans('messages.sure_lang',[],session('locale')); ?>',
             text:  '<?php echo trans('messages.wanna_delete_lang',[],session('locale')); ?>',
             type: "warning",
             showCancelButton: !0,
             confirmButtonColor: "#3085d6",
             cancelButtonColor: "#d33",
             confirmButtonText:  '<?php echo trans('messages.delete_lang',[],session('locale')); ?>',
             confirmButtonClass: "btn btn-primary",
             cancelButtonClass: "btn btn-danger ml-1",
             buttonsStyling: !1
         }).then(function (result) {
             if (result.value) {
                 $('#global-loader').show();
                 before_submit();
                 var csrfToken = $('meta[name="csrf-token"]').attr('content');
                 $.ajax({
                     url: "{{ url('delete_booking') }}",
                     type: 'POST',
                     data: {id: id,_token: csrfToken},
                     error: function () {
                         $('#global-loader').hide();
                         after_submit();
                         show_notification('error', '<?php echo trans('messages.data_delete_failed_lang',[],session('locale')); ?>');
                     },
                     success: function (data) {
                         $('#global-loader').hide();
                         after_submit();
                         $('#all_booking').DataTable().ajax.reload();
                         show_notification('success', '<?php echo trans('messages.data_deleted_successful_lang',[],session('locale')); ?>');
                     }
                 });
             } else if (result.dismiss === Swal.DismissReason.cancel) {
                 show_notification('success', '<?php echo trans('messages.data_safe_lang',[],session('locale')); ?>');
             }
         });
     }
 
     // delete booking
     function cancel_booking(id) {
         Swal.fire({
             title:  '<?php echo trans('messages.sure_lang',[],session('locale')); ?>',
             text:  '<?php echo trans('messages.wanna_cancel_lang',[],session('locale')); ?>',
             type: "warning",
             showCancelButton: !0,
             confirmButtonColor: "#3085d6",
             cancelButtonColor: "#d33",
             confirmButtonText:  '<?php echo trans('messages.cancel_lang',[],session('locale')); ?>',
             confirmButtonClass: "btn btn-primary",
             cancelButtonClass: "btn btn-danger ml-1",
             buttonsStyling: !1
         }).then(function (result) {
             if (result.value) {
                 $('#global-loader').show();
                 before_submit();
                 var csrfToken = $('meta[name="csrf-token"]').attr('content');
                 $.ajax({
                     url: "{{ url('cancel_booking') }}",
                     type: 'POST',
                     data: {id: id,_token: csrfToken},
                     error: function () {
                         $('#global-loader').hide();
                         after_submit();
                         get_booking_detail(id)
                         show_notification('error', '<?php echo trans('messages.data_cancel_failed_lang',[],session('locale')); ?>');
                     },
                     success: function (data) {
                         $('#global-loader').hide();
                         after_submit();
                         $('#all_booking').DataTable().ajax.reload();
                         get_booking_detail(id)
                         show_notification('success', '<?php echo trans('messages.data_cancel_successful_lang',[],session('locale')); ?>');
                     }
                 });
             } else if (result.dismiss === Swal.DismissReason.cancel) {
                 show_notification('success', '<?php echo trans('messages.data_safe_lang',[],session('locale')); ?>');
             }
         });
     }
 
     // get extend booking
     function extend_booking(booking_id) {
         $('.extend_booking_id').val(booking_id); 
         var csrfToken = $('meta[name="csrf-token"]').attr('content');
         $('#global-loader').show();
         $.ajax ({
             url : "{{ url('get_booking_data') }}",
             method : "POST",
             data :   {booking_id:booking_id,_token: csrfToken},
             success: function(response) {
                 $('#global-loader').hide();
                 $('#old_rent_date').val(response.old_rent_date);
                 $('#return_date').val(response.return_date);
                 // Initialize the date (example: 2024-09-29)
                 let currentDate = new Date(response.return_date);
 
                 // Add one day
                 currentDate.setDate(currentDate.getDate() + 1);
 
                 // Format the date to YYYY-MM-DD
                 let nextDay = currentDate.toISOString().split('T')[0];
                 $('#new_return_date').val(nextDay);
                 $('#new_return_date').trigger('change');
                 $('#price').val(response.price);
                 $('#discount').val(response.discount);
                 $('.extend_dress_id').val(response.dress_id);
                 calculateDays();
                 calculateTotalPrice();
                 get_extend_dress_detail();
             },
             error: function(response)
             {
                 show_notification('error','<?php echo trans('messages.data_get_failed_lang',[],session('locale')); ?>');
                 console.log(response);
                 return false;
             }
         });
     }
 
     // add extend booking
     $('.add_extend_booking').off().on('submit', function(e){
         e.preventDefault();
         var formdatas = new FormData($('.add_extend_booking')[0]);
         var new_return_date=$('.new_return_date').val();
         var return_date=$('.return_date').val(); 
         
         if(new_return_date=="" )
         {
             show_notification('error','<?php echo trans('messages.add_return_date_lang',[],session('locale')); ?>'); return false;
 
         }
         if(err==1)
         {
             show_notification('error','<?php echo trans('messages.validation_dress_already_booked_lang',[],session('locale')); ?>');
             $('#price').val(0.000);
             return false;
         }
         $('#global-loader').show();
         before_submit();
         var str = $(".add_extend_booking").serialize();
         $.ajax({
             type: "POST",
             url: "{{ url('add_extend_booking') }}",
             data: formdatas,
             contentType: false,
             processData: false,
             success: function(data) {
                 $('#global-loader').hide();
                 after_submit();
                 show_notification('success','<?php echo trans('messages.extend_booking_add_successfully_lang',[],session('locale')); ?>');
                 $('#extend_booking_modal').modal('hide');
                 $('#all_booking').DataTable().ajax.reload();
                 return false;
             },
             error: function(data)
             {
                 $('#global-loader').hide();
                 after_submit();
                 show_notification('error','<?php echo trans('messages.extend_booking_add_failed_lang',[],session('locale')); ?>');
                 console.log(data);
                 $('#all_booking').DataTable().ajax.reload();
                 return false;
             }
         });
 
     });
 
     // get finish booking
     function finish_booking(booking_id) {
         $('.finish_booking_id').val(booking_id); 
         var csrfToken = $('meta[name="csrf-token"]').attr('content');
         $('#global-loader').show();
         $.ajax ({
             url : "{{ url('get_finish_booking_detail') }}",
             method : "POST",
             data :   {booking_id:booking_id,_token: csrfToken},
             success: function(response) {
                 $('#global-loader').hide();
                 $('#get_finish_detail_detail').html(response.detail); 
                 $('#finish_booking_no').text(response.booking_no);
             },
             error: function(response)
             {
                 show_notification('error','<?php echo trans('messages.data_get_failed_lang',[],session('locale')); ?>');
                 console.log(response);
                 return false;
             }
         });
     }
 
     // add finish booking
     $('.add_finish_booking').off().on('submit', function(e){
         e.preventDefault();
         var formdatas = new FormData($('.add_finish_booking')[0]);
          
         $('#global-loader').show();
         before_submit();
         var str = $(".add_finish_booking").serialize();
         $.ajax({
             type: "POST",
             url: "{{ url('add_finish_booking') }}",
             data: formdatas,
             contentType: false,
             processData: false,
             success: function(data) {
                 $('#global-loader').hide();
                 after_submit();
                 show_notification('success','<?php echo trans('messages.finish_booking_add_successfully_lang',[],session('locale')); ?>');
                 $('#finish_booking_modal').modal('hide');
                 $('#all_booking').DataTable().ajax.reload();
                 return false;
             },
             error: function(data)
             {
                 $('#global-loader').hide();
                 after_submit();
                 show_notification('error','<?php echo trans('messages.finish_booking_add_failed_lang',[],session('locale')); ?>');
                 console.log(data);
                 $('#all_booking').DataTable().ajax.reload();
                 return false;
             }
         });
 
     });
</script>