<!DOCTYPE html>
<html lang="en" class="h-100">

<head>
    <!-- Title Meta -->
    <meta charset="utf-8" />
    @section('title', 'Login')
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A fully responsive premium admin dashboard template" />
    <meta name="author" content="Techzaa" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">

    <!-- Vendor CSS -->
    <link href="{{ asset('assets/css/vendor.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- Icons CSS -->
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- App CSS -->
    <link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- Theme Config JS -->
    <script src="{{ asset('assets/js/config.js') }}"></script>

    <script src="https://code.iconify.design/iconify-icon/1.0.8/iconify-icon.min.js"></script>

    <style>
        .captcha-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 0.375rem 0.75rem;
            background-color: #f8f9fa;
            flex: 1;
        }

        .captcha-wrapper img {
            max-width: 100%;
            height: auto;
        }
    </style>

</head>

<body class="h-100">
    <div class="d-flex flex-column h-100 p-3">
        <div class="d-flex flex-column flex-grow-1">
            <div class="row h-100">
                <div class="col-xxl-7">
                    <div class="row justify-content-center h-100">
                        <div class="col-lg-6 py-lg-5">
                            <div class="d-flex flex-column h-100 justify-content-center">
                                <div class="auth-logo mb-4">

                                    <a href="index.html" class="logo-dark">
                                        <img src="{{ asset('assets/images/logo.png') }}" height="75" alt="logo dark">
                                    </a>

                                    <a href="index.html" class="logo-light">
                                        <img src="{{ asset('assets/images/logo.png') }}" height="75" alt="logo light">
                                    </a>
                                </div>

                                <h2 class="fw-bold fs-24">Sign In</h2>

                                <p class="text-muted mt-1 mb-4">Enter your email address and password to access admin
                                    panel.</p>

                                <div class="mb-5">
                                    <form action="{{ route('login') }}" method="POST" class="authentication-form">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label" for="email">Email</label>
                                            <input type="email" id="email" name="email" class="form-control bg-"
                                                placeholder="Enter your email" required value="{{ old('email') }}"
                                                autofocus>
                                            @error('email')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <!-- <a href="{{ route('password.request') }}" class="float-end text-muted text-unline-dashed ms-1">Reset password</a> -->
                                            <label class="form-label" for="password">Password</label>
                                            <div class="input-group">
                                                <input type="password" id="password" name="password"
                                                    class="form-control @error('password') is-invalid @enderror"
                                                    placeholder="Enter your password" required>
                                                <button class="btn btn-outline-secondary" type="button"
                                                    onclick="togglePassword()">
                                                    <iconify-icon icon="solar:eye-bold"
                                                        id="password-icon"></iconify-icon>
                                                </button>
                                            </div>
                                            @error('password')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="checkbox-signin">
                                                <label class="form-check-label" for="checkbox-signin">Remember
                                                    me</label>
                                            </div>
                                        </div>

                                        <div class="mb-1 text-center d-grid">
                                            <button class="btn btn-soft-primary" type="submit">Sign In</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/vendor.js') }}"></script>
    <script src="https://code.iconify.design/iconify-icon/1.0.8/iconify-icon.min.js"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>

    <script>
        // Password toggle function
        function togglePassword() {
            const field = document.getElementById('password');
            const icon = document.getElementById('password-icon');
            if (field.type === 'password') {
                field.type = 'text';
                icon.setAttribute('icon', 'solar:eye-closed-bold');
            } else {
                field.type = 'password';
                icon.setAttribute('icon', 'solar:eye-bold');
            }
        }


    </script>

</body>

</html>