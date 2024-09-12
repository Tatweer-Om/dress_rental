<script>
    const rentDatePicker = flatpickr("#rent_date", {
    defaultDate: new Date(),
    onChange: function(selectedDates, dateStr, instance) {
      // When rent_date changes, update return_date to ensure it's always greater
      returnDatePicker.set('minDate', dateStr);
      calculateDays();
    }
  });

  const returnDatePicker = flatpickr("#return_date", {
    defaultDate: new Date(),
    onChange: function() {
      calculateDays();
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

    // Calculate the discount amount
    const discountAmount = (price * discount) / 100;

    // Calculate the total price after discount
    const totalPrice = price - discountAmount;

    // Update the total_price input field
    document.querySelector(".total_price").value = totalPrice.toFixed(3); // Fix to 2 decimal places
  }

  // Attach keyup event listeners to both price and discount inputs
  document.querySelector(".price").addEventListener("keyup", calculateTotalPrice);
  document.querySelector(".discount").addEventListener("keyup", calculateTotalPrice);
    function get_dress_detail(){
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        $.ajax ({
            url : "{{ url('get_dress_detail') }}",
            method : "POST",
            data :   {id:id,_token: csrfToken},
            success: function(response) {
                $('#dress_detail').html(response.dress_detail);
            },
            error: function(response)
            {
                show_notification('error','<?php echo trans('messages.data_get_failed_lang',[],session('locale')); ?>');
                console.log(html);
                return false;
            }
        });
    }
</script>