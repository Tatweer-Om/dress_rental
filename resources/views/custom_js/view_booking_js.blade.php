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
</script>