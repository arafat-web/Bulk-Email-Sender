<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Meta -->
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Login to Dashboard</title>

    <!-- vendor css -->
    <link href="{{ asset('assets/lib/ionicons/css/ionicons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/lib/typicons.font/typicons.css') }}" rel="stylesheet">

    <!-- azia CSS -->
    <link href="{{ asset('assets/css/azia.css') }}" rel="stylesheet">

</head>

<body class="az-body">

    <div class="az-signin-wrapper">
        <div class="az-card-signin">
            <h1 class="az-logo text-uppercase">Bulk Email Sender</h1>
            <div class="az-signin-header">
                <h2>Welcome back!</h2>
                <h4>Please sign in to continue</h4>
                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label>Email</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div><!-- form-group -->
                    <div class="form-group">
                        <label>Password</label>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div><!-- form-group -->
                    <button type="submit" class="btn btn-az-primary btn-block">Sign In</button>
                </form>
            </div><!-- az-signin-header -->
            <div class="text-center az-signin-footer">
                <p><a target="_blank" href="https://facebook.com/arafathossain000">Arafat Hossain Ar</a></p>
            </div><!-- az-signin-footer -->
        </div><!-- az-card-signin -->
    </div><!-- az-signin-wrapper -->

    <link href="{{ asset('assets/lib/jquery/jquery.min.js') }}" rel="stylesheet">
    <link href="{{ asset('assets/lib/bootstrap/js/bootstrap.bundle.min.js') }}" rel="stylesheet">
    <link href="{{ asset('assets/lib/ionicons/ionicons.js') }}" rel="stylesheet">
    <link href="{{ asset('assets/js/azia.js') }}" rel="stylesheet">
    <script>
        localStorage.removeItem('instructionUnderstood');
        $(function() {
            'use strict'
        });
    </script>
</body>

</html>
