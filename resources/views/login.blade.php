<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>BianMart - Login</title>




    <link href="{{ asset('assets') }}/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">


    <link href="{{ asset('assets') }}/css/sb-admin-2.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(to right, #344b49, #444680);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .card {
            border-radius: 12px;
            box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.1);
            border: none;
        }

        .btn-primary {
            background-color: #4a69bd;
            border-color: #4a69bd;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #3b5998;
            border-color: #3b5998;
        }

        .form-control {
            border-radius: 8px;
            padding: 14px;
            font-size: 16px;
        }

        .login-logo {
            width: 60%;
            max-width: 200px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="d-flex justify-content-center align-items-center">
            <div class="col-lg-6 col-md-8 col-sm-10">
                <div class="card o-hidden border-0 shadow-lg">
                    <div class="card-body p-5">
                        <div class="text-center">
                            {{-- <img src="#" alt="Logo" class="login-logo mb-3"> --}}
                            <h1 class="h4 text-gray-900 mb-4">Welcome Back!</h1>
                        </div>

                        <form class="user" action="{{ route('cek-login') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <input type="text" class="form-control" name="login"
                                    placeholder="Enter Email or Name" required autofocus autocomplete="off"
                                    oninput="this.value = this.value.replace(/ /g, '')">
                            </div>
                            <div class="form-group position-relative">
                                <input type="password" class="form-control" name="password" placeholder="Password"
                                    id="password" required>
                                <span toggle="#password"
                                    class="fa fa-fw fa-eye field-icon toggle-password position-absolute"
                                    style="right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;"></span>
                            </div>


                            <div class="form-group">
                                <div class="custom-control custom-checkbox small">
                                    <input type="checkbox" class="custom-control-input" id="customCheck">
                                    <label class="custom-control-label" for="customCheck">Remember Me</label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-user btn-block">
                                Login
                            </button>
                        </form>
                        <hr>
                        <div class="text-center">
                            <a class="small" href="forgot-password.html">Forgot Password?</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="{{ asset('assets') }}/vendor/jquery/jquery.min.js"></script>
    <script src="{{ asset('assets') }}/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="{{ asset('assets') }}/vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="{{ asset('assets') }}/js/sb-admin-2.min.js"></script>

    <script>
        document.querySelector('.toggle-password').addEventListener('click', function() {
            const passwordField = document.querySelector(this.getAttribute('toggle'));
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
        });
    </script>

</body>

</html>



{{-- <form action="{{ route('cek-login') }}" method="POST">
    @csrf
    <div class="form-group">
        <input type="text" name="email" id="" class="form-control" required>
    </div>

    <div class="form-group">
        <input type="password" name="password" id="" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary">login</button>
</form> --}}
