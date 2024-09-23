<script type="text/javascript">
    $(document).ready(function() {
        $('#add_account_modal').on('hidden.bs.modal', function() {
            $(".add_account")[0].reset();
            $('.account_id').val('');
        });
        $('#accounts').DataTable({
            "sAjaxSource": "{{ url('show_account') }}",
            "bFilter": true,
            'pagingType': 'numbers',
            "ordering": true,
        });


        $('.add_account').off().on('submit', function(e){
            e.preventDefault();
            var formdatas = new FormData($('.add_account')[0]);
            var account_name=$('.account_name').val();
            var id=$('.account_id').val();

            if(id!='')
            {
                if(account_name=="" )
                {
                    show_notification('error','<?php echo trans('messages.add_account_name_lang',[],session('locale')); ?>'); return false;
                }
                $('#global-loader').show();
                before_submit();
                var str = $(".add_account").serialize();
                $.ajax({
                    type: "POST",
                    url: "{{ url('update_account') }}",
                    data: formdatas,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {
                        $('#global-loader').hide();
                        after_submit();
                        show_notification('success','<?php echo trans('messages.data_update_success_lang',[],session('locale')); ?>');
                        $('#add_account_modal').modal('hide');
                        $('#accounts').DataTable().ajax.reload();
                        return false;
                    },
                    error: function(data)
                    {
                        $('#global-loader').hide();
                        after_submit();
                        show_notification('error','<?php echo trans('messages.data_update_failed_lang',[],session('locale')); ?>');
                        $('#accounts').DataTable().ajax.reload();
                        console.log(data);
                        return false;
                    }
                });
            }
            else if(id==''){


                if(account_name=="" )
                {
                    show_notification('error','<?php echo trans('messages.add_account_name_lang',[],session('locale')); ?>'); return false;
                }

                $('#global-loader').show();
                before_submit();
                var str = $(".add_account").serialize();
                $.ajax({
                    type: "POST",
                    url: "{{ url('add_account') }}",
                    data: formdatas,
                    contentType: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(data) {
                        $('#global-loader').hide();
                        after_submit();
                        $('#accounts').DataTable().ajax.reload();
                        show_notification('success','<?php echo trans('messages.data_add_success_lang',[],session('locale')); ?>');
                        $('#add_account_modal').modal('hide');
                        $(".add_account")[0].reset();
                        return false;
                        },
                    error: function(data)
                    {
                        $('#global-loader').hide();
                        after_submit();
                        show_notification('error','<?php echo trans('messages.data_add_failed_lang',[],session('locale')); ?>');
                        $('#accounts').DataTable().ajax.reload();
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
            url : "{{ url('edit_account') }}",
            method : "POST",
            data :   {id:id,_token: csrfToken},
            success: function(fetch) {
                $('#global-loader').hide();
                after_submit();
                if(fetch!=""){

                    $(".account_name").val(fetch.account_name);
                    $(".account_branch").val(fetch.account_branch);
                    $(".account_no").val(fetch.account_no);
                    $(".opening_balance").val(fetch.opening_balance);
                    $(".commission").val(fetch.commission);
                    $(".account_type").val(fetch.account_type);
                    if(fetch.account_status==1)
                    {
                        $('.account_status').prop('checked',true);
                    }
                    else
                    {
                        $('.account_status').prop('checked',false);
                    }
                    $(".notes").val(fetch.notes);
                    $(".account_id").val(fetch.account_id);
                    $(".modal-title").html('Update');
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
                    url: "{{ url('delete_account') }}",
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
                        $('#accounts').DataTable().ajax.reload();
                        show_notification('success', '<?php echo trans('messages.delete_success_lang',[],session('locale')); ?>');
                    }
                });
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                show_notification('success', '<?php echo trans('messages.data_is_safe_lang',[],session('locale')); ?>');
            }
        });
    }



    </script>
