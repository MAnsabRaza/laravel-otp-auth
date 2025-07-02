<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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

        .btnSignUp {
            background: linear-gradient(135deg, #3498db, #2c3e50);
            color: white;
        }

        .btnSignUp:hover {
            background: linear-gradient(135deg, #2c3e50, #3498db);
            color: white;
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
                            <h1 class="text-center voucher-title">Login</h1>
                        </div>
                        <div class="p-3">
                            <div class="form-group my-2">
                                <label for="email" class="fw-semibold fs-5 my-1">Email</label>
                                <input type="email" class="form-control-lg form-control" id="email" name="email"
                                    placeholder="Enter Your Email">
                            </div>
                            <div class="form-group my-2">
                                <label for="password" class="fw-semibold fs-5 my-1">Password</label>
                                <input type="password" class="form-control form-control-lg" id="password"
                                    name="password" placeholder="Enter Your Password">
                            </div>

                            <div class="my-2 form-group">
                                <button class="btn w-100 btn-lg btnSignUp" id="btnLogin">SignIn</button>
                            </div>
                            <div
                                class="my-3 p-3 bg-light rounded border d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="me-2">Already have an account?</span>
                                    <a href="{{ route('register') }}" class="fw-semibold">Sign Up</a>
                                </div>
                                <div>
                                    <a href="{{ route('forgetPassword') }}" class="text-danger fw-semibold">Forgot
                                        Password?</a>
                                </div>
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
            $("#btnLogin").click(function(e) {
                const email = $("#email").val();
                const password = $("#password").val();
                if (email == "" || password === "") {
                    Toastify({
                        text: "Please Enter Email and Password",
                        duration: 3000,
                        close: true,
                        gravity: "top",
                        position: "right",
                        stopOnFocus: true,
                        backgroundColor: "#f44336",
                    }).showToast();
                    return;
                }
                const loginData = {
                    "email": email,
                    "password": password
                }
                $.ajax({
                    type: 'Post',
                    url: '/checkLogin',
                    data: loginData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Toastify({
                            text: response.message,
                            duration: 3000,
                            close: true,
                            gravity: "top",
                            position: "right",
                            stopOnFocus: true,
                            backgroundColor: "#4caf50",
                        }).showToast();

                        setTimeout(() => {
                            window.location.href = response.redirect;
                        }, 1500);
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;
                        Toastify({
                            text: response?.message || "Login failed",
                            duration: 3000,
                            close: true,
                            gravity: "top",
                            position: "right",
                            stopOnFocus: true,
                            backgroundColor: "#f44336",
                        }).showToast();
                    }
                })
            })
        });
    </script>
</body>

</html>
