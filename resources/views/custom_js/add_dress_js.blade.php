<script type="text/javascript">
    $(document).ready(function() {
        $('#add_dress_modal').on('hidden.bs.modal', function() {
            $(".add_dress")[0].reset();
            $('.dress_id').val('');
            var imagePath = '{{ asset('custom_images/dummy_image/cover-image-icon.png') }}';
            $('#ad_cover_preview').attr('src',imagePath);
            $('#attachment-holder').html('');
            $('#all_attribute').html('');
        });
        $('#all_dress').DataTable({
            "sAjaxSource": "{{ url('show_dress') }}",
            "bFilter": true,
            'pagingType': 'numbers',
            "ordering": true,
        });
        $('#ad_cover_container').on('click',function(e){
            $('#ad_cover').trigger('click');
        });

        $('#ad_cover').on('change',function(e){
            $('#ad_cover_preview').attr('src', window.URL.createObjectURL(this.files[0]));
        });


        $('#btn-ad-images').on('click',function(e){
            $('#ad_images').trigger('click');
        });

        // upload atttachments
        $('#ad_images').on('change',function(e){
            var attachments= $(this)[0].files.length;
            var dress_id     = $('.dress_id').val();
            var form_data = new FormData();
            form_data.append('dress_id',dress_id);
            if(attachments>0)
            {
                for (var x = 0; x <attachments; x++)
                {
                    form_data.append('attachments[]',$(this)[0].files[x]);
                }
            }
            $.ajax({
                url:"{{ url('upload_attachments') }}",
                type:'POST',
                processData:false,
                contentType: false,
                data:form_data,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success:function(response)
                {
                    $('#attachment-holder').html(response.images).fadeIn('slow');
                },
            });
        });

        // remove attachments
        $(document).on('click','.rmv-attachment',function(e){
            e.preventDefault();
            var img = $(this).closest('div').find('img').attr('src');
            var form_data = new FormData();
            form_data.append('img',img);
            $.ajax({
                url:"{{ url('remove_attachments') }}",
                type:'POST',
                processData:false,
                contentType: false,
                data:form_data,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                context:this,
                success:function(response)
                {
                    // alert(response);
                    $(this).parent().parent().remove();
                }
            });
        });
        // remove edit attachments
        $(document).on('click','.e-rmv-attachment',function(e){
            e.preventDefault();
            var image_id     = $(this).attr('id');
            var dress_id     = $('.dress_id').val();
            var img = $(this).closest('div').find('img').attr('src');
            var form_data = new FormData();
            form_data.append('img',img);
            form_data.append('image_id',image_id);
            form_data.append('dress_id',dress_id);
            $.ajax({
                url:"{{ url('e_remove_attachments') }}",
                type:'POST',
                processData:false,
                contentType: false,
                data:form_data,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                context:this,
                success:function(response)
                {
                    $(this).parent().parent().remove();
                    $('#attachment-holder').html(response.images).fadeIn('slow');
                }
            });
        });

        // add attribute
        $('#add_attribute').on('click',function(e){
            $('#all_attribute').append(`<div class="row attribute_div">
                                    <div class="col-md-4">
                                        <input type="hidden" class="attribute_id" name="attribute_id[]" value="">
                                        <div class="mb-3">
                                            <label for="attribute_name" class="form-label"><?php echo trans('messages.attribute_name_lang', [], session('locale')) ; ?></label>
                                            <input class="form-control attribute_name" name="attribute_name[]" type="text">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="attribute_notes" class="form-label"><?php echo trans('messages.notes_lang', [], session('locale')) ; ?></label>
                                            <textarea class="form-control attribute_notes" rows="3" name="attribute_notes[]"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="mb-3">
                                            <button type="button" style="margin-top: 40px;" class="btn btn-primary del_attribute"><i class="fas fa-trash"></i></button>
                                        </div>
                                    </div>
                                </div>`);
        });
        // del div attribute
        $(document).on('click', '.del_attribute', function () {
            $(this).closest('.attribute_div').remove(); // Removes the closest parent div with class 'attribute_div'
        });


        $('.add_dress').off().on('submit', function(e){
            e.preventDefault();
            var formdatas = new FormData($('.add_dress')[0]);
            var title=$('.dress_name').val();
            var sku=$('.sku').val();
            var category_name=$('.category_name').val();
            var color_name=$('.color_name').val();
            var size_name=$('.size_name').val();
            var price=$('.price').val();
            var id=$('.dress_id').val();

            if(id!='')
            {
                if(title=="" )
                {
                    show_notification('error','<?php echo trans('messages.add_dress_name_lang',[],session('locale')); ?>'); return false;
                }
                if(sku=="" )
                {
                    show_notification('error','<?php echo trans('messages.add_sku_lang',[],session('locale')); ?>'); return false;
                }
                if(category_name=="" )
                {
                    show_notification('error','<?php echo trans('messages.add_category_name_lang',[],session('locale')); ?>'); return false;
                }
                if(color_name=="" )
                {
                    show_notification('error','<?php echo trans('messages.add_color_name_lang',[],session('locale')); ?>'); return false;
                }
                if(size_name=="" )
                {
                    show_notification('error','<?php echo trans('messages.add_size_name_lang',[],session('locale')); ?>'); return false;
                }
                if(price=="" )
                {
                    show_notification('error','<?php echo trans('messages.add_price_lang',[],session('locale')); ?>'); return false;
                }
                var error=0;
                $('.attribute_name').each(function () {
                    if ($(this).val().trim() == '') {
                        error+=1;
                    }
                });
                if(error>0)
                {
                    show_notification('error','<?php echo trans('messages.add_attribute_name_lang',[],session('locale')); ?>'); return false;
                }


                $('#global-loader').show();
                before_submit();
                var str = $(".add_dress").serialize();
                $.ajax({
                    type: "POST",
                    url: "{{ url('update_dress') }}",
                    data: formdatas,
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        $('#global-loader').hide();
                        after_submit();
                        show_notification('success','<?php echo trans('messages.data_updated_successful_lang',[],session('locale')); ?>');
                        $('#add_dress_modal').modal('hide');
                        $('#all_dress').DataTable().ajax.reload();
                        return false;
                    },
                    error: function(data)
                    {
                        $('#global-loader').hide();
                        after_submit();
                        show_notification('error','<?php echo trans('messages.data_updated_failed_lang',[],session('locale')); ?>');
                        $('#all_dress').DataTable().ajax.reload();
                        console.log(data);
                        return false;
                    }
                });
            }
            else if(id==''){


                if(title=="" )
                {
                    show_notification('error','<?php echo trans('messages.add_dress_name_lang',[],session('locale')); ?>'); return false;
                }
                if(sku=="" )
                {
                    show_notification('error','<?php echo trans('messages.add_sku_lang',[],session('locale')); ?>'); return false;
                }
                if(category_name=="" )
                {
                    show_notification('error','<?php echo trans('messages.add_category_name_lang',[],session('locale')); ?>'); return false;
                }
                if(color_name=="" )
                {
                    show_notification('error','<?php echo trans('messages.add_color_name_lang',[],session('locale')); ?>'); return false;
                }
                if(size_name=="" )
                {
                    show_notification('error','<?php echo trans('messages.add_size_name_lang',[],session('locale')); ?>'); return false;
                }
                if(price=="" )
                {
                    show_notification('error','<?php echo trans('messages.add_price_lang',[],session('locale')); ?>'); return false;
                }
                var error=0;
                $('.attribute_name').each(function () {
                    if ($(this).val().trim() == '') {
                        error+=1;
                    }
                });
                if(error>0)
                {
                    show_notification('error','<?php echo trans('messages.add_attribute_name_lang',[],session('locale')); ?>'); return false;
                }

                $('#global-loader').show();
                before_submit();
                var csrfToken = $('meta[name="csrf-token"]').attr('content');
                var formdatas = new FormData($(".add_dress")[0]); // Create FormData
                formdatas.append('_token', csrfToken);
                var str = $(".add_dress").serialize();
                $.ajax({
                    type: "POST",
                    url: "{{ url('add_dress') }}",
                    data: formdatas,
                    contentType: false,
                    processData: false,
                    success: function(data) {
                        $('#global-loader').hide();
                        after_submit();
                        $('#all_dress').DataTable().ajax.reload();
                        show_notification('success','<?php echo trans('messages.data_added_successful_lang',[],session('locale')); ?>');
                        $('#add_dress_modal').modal('hide');
                        $(".add_dress")[0].reset();
                        return false;
                        },
                    error: function(data)
                    {
                        $('#global-loader').hide();
                        after_submit();
                        show_notification('error','<?php echo trans('messages.data_added_failed_lang',[],session('locale')); ?>');
                        $('#all_dress').DataTable().ajax.reload();
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
            url : "{{ url('edit_dress') }}",
            method : "POST",
            data :   {id:id,_token: csrfToken},
            success: function(fetch) {
                $('#global-loader').hide();
                after_submit();
                if(fetch!=""){

                    // / Define a variable for the image path
                    var imagePath = '{{ asset('custom_images/dummy_image/cover-image-icon.png') }}';

                    // Check if the category_image is present and not an empty string
                    if (fetch.dress_image && fetch.dress_image !== "") {
                        imagePath = '{{ asset('custom_images/dress_image/') }}/' + fetch.dress_image;
                    }
                    $('#ad_cover_preview').attr('src',imagePath);
                    $("#attachment-holder").html(fetch.all_images);
                    $("#all_attribute").html(fetch.attributes);
                    $(".dress_name").val(fetch.dress_name);
                    $(".sku").val(fetch.sku);
                    $(".category_name").val(fetch.category_name);
                    $(".brand_name").val(fetch.brand_name);
                    $(".color_name").val(fetch.color_name);
                    $(".size_name").val(fetch.size_name);
                    $(".price").val(fetch.price);
                    $(".condition").val(fetch.condition);
                    $(".notes").val(fetch.notes);
                    $(".dress_id").val(fetch.dress_id);
                    $(".modal-title").html('<?php echo trans('messages.update_data_lang',[],session('locale')); ?>');
                }
            },
            error: function(html)
            {
                $('#global-loader').hide();
                after_submit();
                show_notification('error','<?php echo trans('messages.data_edit_failed_lang',[],session('locale')); ?>');
                console.log(html);
                return false;
            }
        });
    }

    function del(id) {
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
                    url: "{{ url('delete_dress') }}",
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
                        $('#all_dress').DataTable().ajax.reload();
                        show_notification('success', '<?php echo trans('messages.data_deleted_successful_lang',[],session('locale')); ?>');
                    }
                });
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                show_notification('success', '<?php echo trans('messages.data_safe_lang',[],session('locale')); ?>');
            }
        });
    }

</script>
