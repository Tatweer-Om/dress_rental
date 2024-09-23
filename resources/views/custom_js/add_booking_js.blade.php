<script>
    const rentDatePicker = flatpickr("#rent_date", {
    defaultDate: new Date(),
    onChange: function(selectedDates, dateStr, instance) {
      // When rent_date changes, update return_date to ensure it's always greater
      returnDatePicker.set('minDate', dateStr);
      calculateDays();
      calculateTotalPrice();
      get_dress_detail();
    }
  });

  const returnDatePicker = flatpickr("#return_date", {
    defaultDate: new Date(),
    onChange: function() {
      calculateDays();
      calculateTotalPrice();
      get_dress_detail();
    }
  });
  calculateDays();
  // Function to calculate days between rent_date and return_date
  function calculateDays() {
    const rentDate = new Date(document.getElementById("rent_date").value);
    const returnDate = new Date(document.getElementById("return_date").value);

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
  function get_dress_detail(){
      var dress_id = $('#dress_name').val();
      var rent_date = $('#rent_date').val();
      var return_date = $('#return_date').val();
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
          url : "{{ url('get_dress_detail') }}",
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
                add_waitlist(dress_id);
                return false;
              }
              else if(response.status==3)
              {
                show_notification('error','<?php echo trans('messages.validation_dress_under_maintenance_lang',[],session('locale')); ?>');
                $('#dress_detail').html("");
                $('#price').val(0.000);
                err=1;
                return false;   
              }
              err=0;
              $('#dress_detail').html(response.dress_detail);
              $('#price').val(response.price);
              calculateTotalPrice()
              lightbox = GLightbox({
                  selector: '.image-popup',
                  title: false
              });
              
          },
          error: function(response)
          {
              show_notification('error','<?php echo trans('messages.data_get_failed_lang',[],session('locale')); ?>');
              console.log(response);
              return false;
          }
      });
  }

  //waitlist
  function add_waitlist(dress_id)
  {
     
    Swal.fire({
        title: '<?php echo trans('messages.add_phone_for_availability_lang',[],session('locale')); ?> ',
        html: `
            <input id="availability_number" class="swal2-input isnumber"  type="text">
        `,
        showCancelButton: true,
        confirmButtonText: '<?php echo trans('messages.submit_lang',[],session('locale')); ?>',
        showLoaderOnConfirm: true,
        confirmButtonColor: "#5156be",
        cancelButtonColor: "#fd625e",
        preConfirm: function() {
            const number = Swal.getPopup().querySelector('#availability_number').value;

            // Validate inputs
            if (!number) {
                Swal.showValidationMessage('<?php echo trans('messages.add_contact_lang',[],session('locale')); ?>');
                return false;
            }

            // Return a promise for the AJAX request
            return new Promise((resolve, reject) => {
                $.ajax({
                    url: '{{ url('add_dress_availability') }}', // Change this to your controller route
                    method: 'POST',
                    data: {
                        number: number,
                        dress_id: dress_id,
                        _token: $('meta[name="csrf-token"]').attr('content') // CSRF token for Laravel
                    },
                    success: function(response) {
                        resolve(response); // Resolve promise with the response data
                    },
                    error: function(xhr) {
                        reject(xhr.responseText); // Reject promise with error message
                    }
                });
            });
        },
        allowOutsideClick: false
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                icon: 'success',
                title: '<?php echo trans('messages.data_added_successful_lang',[],session('locale')); ?>',
                confirmButtonColor: "#5156be",
                // html: `${result.value.number}`
            });
        }
    });

  }   

  // search customer
  $(".customer_name").autocomplete({
      source: function(request, response) {
          $.ajax({
              url: "{{ url('search_customer') }}",
              method: "POST",
              dataType: "json",
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              data: {
                  term: request.term
              },

              success: function(data) {
                response(data.slice(0, 10)); // Limit to 10 results 
                  
              },
              error: function(xhr, status, error) {
                  console.error(xhr.responseText);
              }
          });
      },
      select: function(event, ui) {
        let selectedValue = ui.item.value; // This is in the format: "1-John Doe+1234567890"
        let discount = ui.item.discount; // This is in the format: "1-John Doe+1234567890"
        let customerId = selectedValue.split('-')[0]; // Extract customer ID (before '-')
        $('#customer_id').val(customerId);
        $('#discount').val(discount);
    }

  }).autocomplete("search", "");
  // add booking
  $('.add_booking').off().on('submit', function(e){
      e.preventDefault();
      var formdatas = new FormData($('.add_booking')[0]);
      var customer_id=$('.customer_id').val();
      var dress_name=$('.dress_name').val();
      var price=$('.price').val();
      var total_price=$('.total_price').val();
      var total_paid_payment=$('.total_paid_payment').val();
      var id=$('.booking_id').val();

      if(id!='')
      {
          if(total_paid_payment > total_price)
          {
                show_notification('error','<?php echo trans('messages.paid_greater_delete_amount_lang',[],session('locale')); ?>'); return false;
          }
          if(customer_id=="" )
          {
              show_notification('error','<?php echo trans('messages.add_customer_lang',[],session('locale')); ?>'); return false;
          }
          if(dress_name=="" )
          {
              show_notification('error','<?php echo trans('messages.add_dress_name_lang',[],session('locale')); ?>'); return false;
          }
          if(price=="" )
          {
              show_notification('error','<?php echo trans('messages.add_price_lang',[],session('locale')); ?>'); return false;
          }
          if(err==1)
          {
                show_notification('error','<?php echo trans('messages.validation_dress_already_booked_lang',[],session('locale')); ?>');
                $('#dress_detail').html("");
                $('#price').val(0.000);
                return false;
          }

          $('#global-loader').show();
          before_submit();
          var str = $(".add_booking").serialize();
          $.ajax({
              type: "POST",
              url: "{{ url('update_booking') }}",
              data: formdatas,
              contentType: false,
              processData: false,
              success: function(data) {
                  $('#global-loader').hide();
                  after_submit();
                  show_notification('success','<?php echo trans('messages.data_updated_successful_lang',[],session('locale')); ?>');
                  location.reload();
                  return false;
              },
              error: function(data)
              {
                  $('#global-loader').hide();
                  after_submit();
                  show_notification('error','<?php echo trans('messages.data_updated_failed_lang',[],session('locale')); ?>');
                   console.log(data);
                  return false;
              }
          });
      }
      else if(id==''){


          if(customer_id=="" )
          {
              show_notification('error','<?php echo trans('messages.add_customer_lang',[],session('locale')); ?>'); return false;
          }
          if(dress_name=="" )
          {
              show_notification('error','<?php echo trans('messages.add_dress_name_lang',[],session('locale')); ?>'); return false;
          }
          if(price=="" )
          {
              show_notification('error','<?php echo trans('messages.add_price_lang',[],session('locale')); ?>'); return false;
          }
          if(err==1)
          {
                show_notification('error','<?php echo trans('messages.validation_dress_already_booked_lang',[],session('locale')); ?>');
                $('#dress_detail').html("");
                $('#price').val(0.000);
                return false;
          }
          $('#global-loader').show();
          before_submit();
          var str = $(".add_booking").serialize();
          $.ajax({
              type: "POST",
              url: "{{ url('add_booking') }}",
              data: formdatas,
              contentType: false,
              processData: false,
              success: function(data) {
                  $('#global-loader').hide();
                  after_submit();
                  show_notification('success','<?php echo trans('messages.data_added_successful_lang',[],session('locale')); ?>');
                  $('#add_payment_modal').modal('show');
                  $(".add_booking")[0].reset();
                  get_payment(data.bill_id,data.booking_id)
                  return false;
              },
              error: function(data)
              {
                  $('#global-loader').hide();
                  after_submit();
                  show_notification('error','<?php echo trans('messages.data_added_failed_lang',[],session('locale')); ?>');
                   console.log(data);
                  return false;
              }
          });

      }

    });

    // add payment
    // add customer
    $('.add_customer').off().on('submit', function(e){
        e.preventDefault();
        var formdatas = new FormData($('.add_customer')[0]);
        var title=$('.customer_names').val();
        var contact=$('.customer_number').val();
        var email=$('.customer_email').val();
        var id=$('.customers_id').val();

        
        if(id==''){


            if(title=="" )
            {
                show_notification('error','<?php echo trans('messages.add_customer_name_lang',[],session('locale')); ?>'); return false;

            }
            if(contact=="" )
            {
                show_notification('error','<?php echo trans('messages.add_contact_lang',[],session('locale')); ?>'); return false;

            }
            if(email=="" )
            {
                show_notification('error','<?php echo trans('messages.add_email_lang',[],session('locale')); ?>'); return false;

            }

            $('#global-loader').show();
            before_submit();
            var str = $(".add_customer").serialize();
            $.ajax({
                type: "POST",
                url: "{{ url('add_booking_customer') }}",
                data: formdatas,
                contentType: false,
                processData: false,
                success: function(data) {
                    $('#global-loader').hide();
                    after_submit();
                    if(data.status==2)
                    {
                        show_notification('error','<?php echo trans('messages.customer_contact_exist_lang',[],session('locale')); ?>');
                        return false;
                    }
                    else
                    {
                        let selectedValue = data.full_name; // This is in the format: "1-John Doe+1234567890"
                        let customerId = selectedValue.split('-')[0]; // Extract customer ID (before '-')
                        $('#customer_id').val(customerId);
                        $('#customer_name').val(selectedValue);
                        $('#discount').val(data.discount);
                        show_notification('success','<?php echo trans('messages.data_added_successful_lang',[],session('locale')); ?>');
                        $('#add_customer_modal').modal('hide');
                        $(".add_customer")[0].reset();
                        return false;
                    }
                    
                    },
                error: function(data)
                {
                    $('#global-loader').hide();
                    after_submit();
                    show_notification('error','<?php echo trans('messages.data_added_failed_lang',[],session('locale')); ?>');
                        console.log(data);
                    return false;
                }
            });

        }

    });

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
    $('#add_payment_modal').on('hidden.bs.modal', function() {
        location.reload();
    });

     

    
    
</script>