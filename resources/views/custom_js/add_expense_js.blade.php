<script type="text/javascript">
    $(document).ready(function() {
        $('#add_expense_modal').on('hidden.bs.modal', function() {
            $(".add_expense")[0].reset();
            $('.expense_id').val('');
        });
        $('#all_expense').DataTable({
            "sAjaxSource": "{{ url('show_expense') }}",
            "bFilter": true,
            'pagingType': 'numbers',
            "ordering": true,
        });

        $('.add_expense').off().on('submit', function(e){
            e.preventDefault();
            var formdatas = new FormData($('.add_expense')[0]);
            var title=$('.expense_name').val();
            var amount=$('.amount').val();
            var payment_method=$('.payment_method').val();
            var id=$('.expense_id').val();

            if(id!='')
            {
                if(title=="" )
                {
                    show_notification('error','<?php echo trans('messages.add_expense_name_lang',[],session('locale')); ?>'); return false;
                }
                if(amount=="" )
                {
                    show_notification('error','<?php echo trans('messages.add_amount_lang',[],session('locale')); ?>'); return false;
                }
                $('#global-loader').show();
                before_submit();
                var str = $(".add_expense").serialize();
                $.ajax({
                    type: "POST",
                    url: "{{ url('update_expense') }}",
                    data: formdatas,
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        $('#global-loader').hide();
                        after_submit();
                        // Clear previous error messages
                        $('.alert-danger').remove();

                        // If validation errors exist, display them
                        if (data.status === 422) {
                            var errors = data.responseJSON.errors;
                            var errorList = '<div class="alert alert-danger"><ul>';
                            $.each(errors, function(key, value) {
                                errorList += '<li>' + value[0] + '</li>'; // Show the first error message for each field
                            });
                            errorList += '</ul></div>';
                            $('#formErrors').html(errorList); // Assuming you have a div with id="formErrors" to display errors
                            return false;
                        }
                        show_notification('success','<?php echo trans('messages.data_update_success_lang',[],session('locale')); ?>');
                        $('#add_expense_modal').modal('hide');
                        $('#all_expense').DataTable().ajax.reload();
                        return false;
                    },
                    error: function(data)
                    {
                        $('#global-loader').hide();
                        after_submit();
                        show_notification('error','<?php echo trans('messages.data_update_failed_lang',[],session('locale')); ?>');
                        $('#all_expense').DataTable().ajax.reload();
                        console.log(data);
                        return false;
                    }
                });
            }
            else if(id==''){


                if(title=="" )
                {
                    show_notification('error','<?php echo trans('messages.add_expense_name_lang',[],session('locale')); ?>'); return false;
                }
                if(amount=="" )
                {
                    show_notification('error','<?php echo trans('messages.add_amount_lang',[],session('locale')); ?>'); return false;
                }
                $('#global-loader').show();
                before_submit();
                var str = $(".add_expense").serialize();
                $.ajax({
                    type: "POST",
                    url: "{{ url('add_expense') }}",
                    data: formdatas,
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        $('#global-loader').hide();
                        after_submit();
                        // Clear previous error messages
                        $('.alert-danger').remove();

                        // If validation errors exist, display them
                        if (data.status === 422) {
                            var errors = data.responseJSON.errors;
                            var errorList = '<div class="alert alert-danger"><ul>';
                            $.each(errors, function(key, value) {
                                errorList += '<li>' + value[0] + '</li>'; // Show the first error message for each field
                            });
                            errorList += '</ul></div>';
                            $('#formErrors').html(errorList); // Assuming you have a div with id="formErrors" to display errors
                            return false;
                        }
                        $('#all_expense').DataTable().ajax.reload();
                        show_notification('success','<?php echo trans('messages.data_add_success_lang',[],session('locale')); ?>');
                        $('#add_expense_modal').modal('hide');
                        $(".add_expense")[0].reset();
                        return false;
                    },
                    error: function(data)
                    {
                        $('#global-loader').hide();
                        after_submit();
                        show_notification('error','<?php echo trans('messages.data_add_failed_lang',[],session('locale')); ?>');
                        $('#all_expense').DataTable().ajax.reload();
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
            url : "{{ url('edit_expense') }}",
            method : "POST",
            data :   {id:id,_token: csrfToken},
            success: function(fetch) {
                $('#global-loader').hide();
                after_submit();
                if(fetch!=""){

                    $(".expense_name").val(fetch.expense_name);
                    $(".category_id").val(fetch.category_id);
                    $(".expense_date").val(fetch.expense_date);
                    $(".amount").val(fetch.amount);
                    $(".payment_method").val(fetch.payment_method);
                    $(".notes").val(fetch.notes);
                    // Define a variable for the image path
                    var imagePath = '{{ asset('images/dummy_image/no_image.png') }}';
                    // Check if the category_image is present and not an empty string
                    if (fetch.expense_image && fetch.expense_image !== "") {
                        imagePath = '{{ asset('images/expense_images/') }}/' + fetch.expense_image;
                    }
                    $('#img_tag').attr('src',imagePath);
                    $(".expense_id").val(fetch.expense_id);
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
                    url: "{{ url('delete_expense') }}",
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
                        $('#all_expense').DataTable().ajax.reload();
                        show_notification('success', '<?php echo trans('messages.delete_success_lang',[],session('locale')); ?>');
                    }
                });
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                show_notification('success',  '<?php echo trans('messages.safe_lang',[],session('locale')); ?>' );
            }
        });
    }



    </script>
