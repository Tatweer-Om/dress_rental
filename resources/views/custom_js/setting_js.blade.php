<script>
    $(document).on('submit', '.add_setting', function(e) {
    e.preventDefault();
    var formData = new FormData(this);

    $.ajax({
    url: '{{ route("add_setting") }}',
    type: 'POST',
    data: formData,
    processData: false,
    contentType: false,
    success: function(response) {
        if (response.success) {
            show_notification('success', '<?php echo trans('messages.data_saved_success',[],session('locale')); ?>');
            // location.reload();

        } else {
            show_notification('error', '<?php echo trans('messages.data_not_saved',[],session('locale')); ?>');
        }
    },
    error: function(xhr) {
        show_notification('error', '<?php echo trans('messages.some_error_occured',[],session('locale')); ?>');
    }
});

});

$(document).on('submit', '.add_avail', function(e) {
    e.preventDefault();
    var formData = new FormData(this);

    $.ajax({
    url: '{{ route("dress_avail") }}',
    type: 'POST',
    data: formData,
    processData: false,
    contentType: false,
    success: function(response) {
        if (response.success) {
            show_notification('success', '<?php echo trans('messages.data_saved_success',[],session('locale')); ?>');
            // location.reload();

        } else {
            show_notification('error', '<?php echo trans('messages.data_not_saved',[],session('locale')); ?>');
        }
    },
    error: function(xhr) {
        show_notification('error', '<?php echo trans('messages.some_error_occured',[],session('locale')); ?>');
    }
});



});

</script>
