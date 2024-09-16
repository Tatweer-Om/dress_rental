<script>
    // datepicket
    $(document).ready(function(){
        // $('.datepick').datetimepicker({
        //     format: 'YYYY-MM-DD',
        //     icons: {
        //         up: "fas fa-angle-up",
        //         down: "fas fa-angle-down",
        //         next: 'fas fa-angle-right',
        //         previous: 'fas fa-angle-left'
        //     }
        // });
        flatpickr(".datepick",{defaultDate:new Date})
    });
    // img
    var imagePath = '{{ asset('images/dummy_image/no_image.png') }}';
    $('.custom-file-container__image-preview').css('background-image', 'url(' + imagePath + ')');
     

    // notification messages
    function show_notification(type, msg) {
        toastr.options = {
            closeButton: true,
            debug: false,
            newestOnTop: true,
            progressBar: true,
            positionClass: 'toast-top-right', // Set position to top-right
            preventDuplicates: false,
            onclick: null,
            showDuration: '300',
            hideDuration: '1000',
            timeOut: '5000',
            extendedTimeOut: '1000',
            showEasing: 'swing',
            hideEasing: 'linear',
            showMethod: 'fadeIn',
            hideMethod: 'fadeOut'
        };
        if (type == "success") {
            toastr.success(msg, type);
        } else if (type == "error") {
            toastr.error(msg, type);
        } else if (type == "warning") {
            toastr.warning(msg, type);
        }
    }

    function before_submit() {
        $('.submit_form').attr('disabled', true);
        $('.submit_form').html(
            'Please wait <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>');
    }

    function after_submit() {
        $('.submit_form').attr('disabled', false);
        $('.submit_form').html('Submit');
    }

    // phone mask
    



    // file validation
    function fileValidation(stk_input, stk_img) {
        var fileInput = document.getElementById(stk_input);
        var filePath = fileInput.value;
        // Allowing file type
        // var allowedExtensions = /(\.jpg|\.jpeg|\.png|\.gif|\.pdf)$/i;
        var allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i;
        if (!allowedExtensions.exec(filePath)) {
            show_notification('error',  '<?php echo trans('messages.extension_validation_lang',[],session('locale')); ?>')
            fileInput.value = '';
            return false;
        } else {
            // Image preview
            if (fileInput.files && fileInput.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {

                    $('#' + stk_img).attr('src', e.target.result);

                };
                reader.readAsDataURL(fileInput.files[0]);
            }
        }
    }
    // three digit after decimal
    function three_digit_after_decimal(number) {
        if (!isNaN(number)) {
            return Math.floor(number * 1000) / 1000;
        }
    }
    // two digit
    function two_digit_after_decimal(number) {
        if (!isNaN(number)) {
            return Math.floor(number * 100) / 100;
        }
    }
    // only number allow
    function isNumber(evt, element) {
        var charCode = (evt.which) ? evt.which : event.keyCode
        if ((charCode != 45 || $(element).val().indexOf('-') != -1) && (charCode != 46 || $(element).val().indexOf(
                '.') != -1) && ((charCode < 48 && charCode != 8) || charCode > 57)) {
            return false;
        } else {
            return true;
        }
    }

    function isNumber_qty(evt, element) {
        var charCode = (evt.which) ? evt.which : event.keyCode;

        // Allow only digits
        if (charCode < 48 || charCode > 57) {
            return false;
        } else {
            return true;
        }
    }

    function convertToEnglishDigits(inputField) {
        // Replace Arabic digits with English digits
        inputField.value = inputField.value.replace(/[٠١٢٣٤٥٦٧٨٩]/g, function(match) {
            return String.fromCharCode(match.charCodeAt(0) - '٠'.charCodeAt(0) + '0'.charCodeAt(0));
        });

        // Remove any non-digit characters
        inputField.value = inputField.value.replace(/\D/g, '');
    }

    function isNumber1(evt, element) {
        var charCode = (evt.which) ? evt.which : event.keyCode;

        // Allow digits (0-9), backspace (8), and minus sign (45)
        if ((charCode >= 48 && charCode <= 57) || charCode == 8 || charCode == 45) {
            // Check if the minus sign is not the first character
            if (charCode == 45 && $(element).val().indexOf('-') !== -1) {
                return false;
            }
            return true;
        } else {
            return false;
        }
    }

    //Number with decimal only
    $(document).on('keypress', '.isnumber', function(e) {
        return isNumber(e, this);
    });
    // only english digit
    $(document).on('input', '.isnumber_qty', function() {
        convertToEnglishDigits(this);
    });
    //Number without decimal only
    $(document).on('keypress', '.isnumber1', function(e) {
        return isNumber1(e, this);
    });
     
</script>
