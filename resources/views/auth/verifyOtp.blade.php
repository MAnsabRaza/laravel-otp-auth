<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Verify OTP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .main {
            background-color: #fff;
        }

        .voucher-title {
            font-size: 28px;
            font-weight: 600;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .voucher-header {
            background: linear-gradient(135deg, #3498db, #2c3e50);
            color: white;
            padding: 30px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .btnOtp {
            background: linear-gradient(135deg, #3498db, #2c3e50);
            color: white;
        }

        .btnOtp:hover {
            background: linear-gradient(135deg, #2c3e50, #3498db);
            color: white;
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .otp-input {
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
            letter-spacing: 0.5rem;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row my-5">
            <div class="col-md-12 p-0">
                <div class="row my-5">
                    <div class="col-md-4"></div>
                    <div class="col-md-5 shadow rounded main p-0 my-5">
                        <div class="voucher-header w-100">
                            <h1 class="text-center voucher-title">Verify OTP</h1>
                        </div>
                        <div class="p-3">
                            <div class="form-group my-2">
                                <label for="otp" class="form-label fw-semibold fs-5">Enter OTP</label>
                                <input type="text" class="form-control form-control-lg otp-input" id="otp"
                                    name="otp" placeholder="000000" maxlength="6" required>
                                <div class="invalid-feedback" id="otp-error"></div>
                                <small class="form-text text-muted">Check your email for the 6-digit OTP code</small>
                            </div>
                            <div class="my-2 form-group">
                                <button class="btn w-100 btn-lg btnOtp" id="btnOtp">Verify OTP</button>
                            </div>
                            <div class="my-3 p-3 bg-light text-center rounded border">
                                <a href="{{ route('forgetPassword') }}" class="btn btn-link">Request New OTP</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4"></div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.js"></script>
    <script>
        $(document).ready(function() {
            // Only allow numbers in OTP input
            $('#otp').on('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });

            $('#btnOtp').click(function(e) {
                e.preventDefault();

                const otp = $('#otp').val().trim();
                const $button = $(this);
                const $otpInput = $('#otp');

                // Reset previous validation states
                $otpInput.removeClass('is-invalid');
                $('#otp-error').text('');

                // Basic validation
                if (!otp) {
                    $otpInput.addClass('is-invalid');
                    $('#otp-error').text('OTP is required');
                    return;
                }

                if (otp.length !== 6) {
                    $otpInput.addClass('is-invalid');
                    $('#otp-error').text('OTP must be 6 digits');
                    return;
                }

                // Disable button and show loading state
                $button.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-2"></span>Verifying...');

                $.ajax({
                    type: 'POST',
                    url: '/sndVerifyOtp',
                    data: {
                        otp: otp
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Toastify({
                            text: response.message || "OTP verified successfully!",
                            duration: 3000,
                            close: true,
                            gravity: "top",
                            position: "right",
                            stopOnFocus: true,
                            backgroundColor: "#4caf50",
                        }).showToast();

                        setTimeout(() => {
                            if (response.redirect) {
                                window.location.href = response.redirect;
                            }
                        }, 1500);
                    },
                    error: function(xhr) {
                        console.error('AJAX Error:', xhr);
                        const response = xhr.responseJSON;

                        let errorMessage = "OTP verification failed. Please try again.";

                        if (response) {
                            if (response.message) {
                                errorMessage = response.message;
                            } else if (response.errors && response.errors.otp) {
                                errorMessage = response.errors.otp[0];
                                $otpInput.addClass('is-invalid');
                                $('#otp-error').text(errorMessage);
                            }
                        }

                        Toastify({
                            text: errorMessage,
                            duration: 5000,
                            close: true,
                            gravity: "top",
                            position: "right",
                            stopOnFocus: true,
                            backgroundColor: "#f44336",
                        }).showToast();
                    },
                    complete: function() {
                        // Re-enable button and restore original text
                        $button.prop('disabled', false).html('Verify OTP');
                    }
                });
            });

            // Allow Enter key to submit
            $('#otp').keypress(function(e) {
                if (e.which === 13) { // Enter key
                    $('#btnOtp').click();
                }
            });
        });
    </script>
</body>

</html>
