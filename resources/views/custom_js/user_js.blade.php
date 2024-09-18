<script type="text/javascript">
    $(document).ready(function() {
        $('#add_user_modal').on('hidden.bs.modal', function() {
            $(".add_user")[0].reset();
            $('.user_id').val('');

        });

        $('#all_user').DataTable({
            "sAjaxSource": "{{ url('show_user') }}",
            "bFilter": true,
            'pagingType': 'numbers',
            "ordering": true,
        });

        $('#add_user_modal').off().on('submit', function(e){
            e.preventDefault();
            var formdatas = new FormData($('.add_user')[0]);
            var title=$('.user_username').val();
            var password=$('.password').val();
            var id=$('.user_id').val();

            if(id!='')
            {
                if(title=="" )
                {
                    show_notification('error','<?php echo trans('messages.add_user_name_lang',[],session('locale')); ?>'); return false;
                }
                if(password=="" )
                {
                    show_notification('error','<?php echo trans('messages.provide_password_lang',[],session('locale')); ?>'); return false;
                }
                $('#global-loader').show();
                before_submit();
                var str = $(".add_user").serialize();
                $.ajax({
                    type: "POST",
                    url: "{{ url('update_user') }}",
                    data: formdatas,
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        $('#global-loader').hide();
                        after_submit();
                        show_notification('success','<?php echo trans('messages.data_update_success_lang',[],session('locale')); ?>');
                        $('#add_user_modal').modal('hide');
                        $('#all_user').DataTable().ajax.reload();
                        return false;
                    },
                    error: function(data)
                    {
                        $('#global-loader').hide();
                        after_submit();
                        show_notification('error','<?php echo trans('messages.data_update_failed_lang',[],session('locale')); ?>');
                        $('#all_user').DataTable().ajax.reload();
                        console.log(data);
                        return false;
                    }
                });
            }
            else if(id==''){


                if(title=="" )
                {
                    show_notification('error','<?php echo trans('messages.add_user_name_lang',[],session('locale')); ?>'); return false;

                }
                if(password=="" )
                {
                    show_notification('error','<?php echo trans('messages.provide_password_lang',[],session('locale')); ?>'); return false;
                }
                $('#global-loader').show();
                before_submit();
                var str = $(".add_user").serialize();
                $.ajax({
                    type: "POST",
                    url: "{{ url('add_user') }}",
                    data: formdatas,
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        $('#global-loader').hide();
                        after_submit();
                        $('#all_user').DataTable().ajax.reload();
                        show_notification('success','<?php echo trans('messages.data_add_success_lang',[],session('locale')); ?>');
                        $('#add_user_modal').modal('hide');
                        $(".add_user")[0].reset();
                        return false;
                        },
                    error: function(data)
                    {
                        $('#global-loader').hide();
                        after_submit();
                        show_notification('error','<?php echo trans('messages.data_add_failed_lang',[],session('locale')); ?>');
                        $('#all_user').DataTable().ajax.reload();

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
            url : "{{ url('edit_user') }}",
            method : "POST",
            data :   {id:id,_token: csrfToken},
            success: function(fetch) {
                $('#global-loader').hide();
                after_submit();
                if(fetch!=""){

                    $(".user_name").val(fetch.user_name);
                    $(".password").val(fetch.password);
                    $(".user_email").val(fetch.user_email);
                    $(".user_phone").val(fetch.user_phone);
                    $(".notes").val(fetch.user_detail);
                    $(".user_id").val(fetch.user_id);
                    $(".permit_type").val(fetch.permit_type);
                    $('#checked_html').html(fetch.checked_html);

                    $(".modal-title").html('<?php echo trans('messages.update_lang',[],session('locale')); ?>');
                }
            },
            error: function(html)
            {
                $('#global-loader').hide();
                after_submit();
                show_notification('error','<?php echo trans('messages.edit_failed_lang',[],session('locale')); ?>');

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
                    url: "{{ url('delete_user') }}",
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
                        $('#all_user').DataTable().ajax.reload();
                        show_notification('success', '<?php echo trans('messages.delete_success_lang',[],session('locale')); ?>');
                    }
                });
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                show_notification('success', '<?php echo trans('messages.safe_lang',[],session('locale')); ?>');
            }
        });
    }

    $(document).ready(function() {
    // Event delegation for 'Select All' checkbox
    $(document).on('change', '#checkboxAll', function () {
        // When 'Select All' checkbox is checked or unchecked
        $('.permit_array').prop('checked', $(this).prop('checked'));
    });

    // Event delegation for individual checkboxes
    $(document).on('change', '.permit_array', function () {
        // If any individual checkbox is unchecked, uncheck the 'Select All' checkbox
        if (!$(this).prop('checked')) {
            $('#checkboxAll').prop('checked', false);
        }

        // If all individual checkboxes are checked, check the 'Select All' checkbox
        if ($('.permit_array:checked').length === $('.permit_array').length) {
            $('#checkboxAll').prop('checked', true);
        }
    });
});







    //loginform






    </script>
