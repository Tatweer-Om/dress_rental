<script>
    const rentDatePicker = flatpickr("#return_date", {
        defaultDate: new Date(),
        onChange: function(selectedDates, dateStr, instance) {
        // When rent_date changes, update return_date to ensure it's always greater
        returnDatePicker.set('minDate', dateStr);
            calculateDays();
            calculateTotalPrice();
            get_extend_dress_detail();
        }
    });

    const returnDatePicker = flatpickr("#new_return_date", {
        defaultDate: new Date(),
        onChange: function() {
            calculateDays();
            calculateTotalPrice();
            get_extend_dress_detail();
        }
    });
  calculateDays(); 
  // Function to calculate days between rent_date and return_date
  function calculateDays() {
    const rentDate = new Date(document.getElementById("old_rent_date").value);
    const returnDate = new Date(document.getElementById("new_return_date").value);

    if (returnDate > rentDate) {
      const timeDiff = returnDate - rentDate;
      const daysDiff = Math.ceil(timeDiff / (1000 * 60 * 60 * 24)); // Convert milliseconds to days
      document.getElementById("duration").value = daysDiff;
    } else {
      document.getElementById("duration").value = 1;
    }
  }

  // Function to calculate the total price after discount
  function calculateTotalPrice() {
    // Get the price and discount values from the input fields
    const price = parseFloat(document.querySelector(".price").value) || 0;
    const discount = parseFloat(document.querySelector(".discount").value) || 0;
    const duration = parseFloat(document.querySelector(".duration").value) || 0;

    // Calculate the discount amount
    const discountAmount = (price * discount) / 100;

    // Calculate the total price after discount
    const totalPrice = (price - discountAmount)*duration;

    // Update the total_price input field
    document.querySelector(".total_price").value = totalPrice.toFixed(3); // Fix to 2 decimal places
  }

  // Attach keyup event listeners to both price and discount inputs
  document.querySelector(".price").addEventListener("keyup", calculateTotalPrice);
  document.querySelector(".discount").addEventListener("keyup", calculateTotalPrice);
  var err=0;
  function get_extend_dress_detail(){
      var dress_id = $('.extend_dress_id').val();
      var rent_date = $('#return_date').val();
      var return_date = $('#new_return_date').val();
      if(dress_id=="")
      {
        show_notification('error','<?php echo trans('messages.select_dress_lang',[],session('locale')); ?>');
        return false;
      }
      if(rent_date=="")
      {
        show_notification('error','<?php echo trans('messages.select_rent_date_lang',[],session('locale')); ?>');
        return false;
      }
      if(return_date=="")
      {
        show_notification('error','<?php echo trans('messages.select_return_date_lang',[],session('locale')); ?>');
        return false;
      }
      
      var csrfToken = $('meta[name="csrf-token"]').attr('content');
      $('#global-loader').show();
      $.ajax ({
          url : "{{ url('get_extend_dress_detail') }}",
          method : "POST",
          data :   {dress_id:dress_id,rent_date:rent_date,return_date:return_date,_token: csrfToken},
          success: function(response) {
              $('#global-loader').hide();
              
              if(response.status==2)
              {
                show_notification('error','<?php echo trans('messages.validation_dress_already_booked_lang',[],session('locale')); ?>');
                $('#dress_detail').html("");
                $('#price').val(0.000);
                err=1;
                calculateTotalPrice()  
                return false;
              }
              else if(response.status==3)
              {
                show_notification('error','<?php echo trans('messages.validation_dress_under_maintenance_lang',[],session('locale')); ?>');
                $('#dress_detail').html("");
                $('#price').val(0.000);
                err=1;
                calculateTotalPrice() 
                return false;   
              }
              err=0;
              $('#price').val(response.price);
              calculateTotalPrice() 
          },
          error: function(response)
          {
              show_notification('error','<?php echo trans('messages.data_get_failed_lang',[],session('locale')); ?>');
              console.log(response);
              return false;
          }
      });
  }
    // view booking
    $('#all_booking').DataTable({
        "sAjaxSource": "{{ url('show_booking') }}",
        "bFilter": true,
        'pagingType': 'numbers',
        "ordering": true,
    });

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