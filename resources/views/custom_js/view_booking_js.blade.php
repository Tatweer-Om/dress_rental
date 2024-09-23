<script>
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
              $('#booking_no_id').text(response.booking_no)
              
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
</script>