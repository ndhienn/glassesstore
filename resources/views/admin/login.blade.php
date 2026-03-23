<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Trang quản trị</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header h1 {
            color: #333;
            font-size: 24px;
            margin-bottom: 10px;
        }
        .login-form .form-group {
            margin-bottom: 20px;
        }
        .login-form label {
            font-weight: 500;
            color: #555;
        }
        .login-form .form-control {
            padding: 10px;
            border-radius: 5px;
        }
        .login-form .btn-login {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            border: none;
            color: white;
            font-weight: 500;
            border-radius: 5px;
            cursor: pointer;
        }
        .login-form .btn-login:hover {
            background-color: #0056b3;
        }
        .back-to-home {
            text-align: center;
            margin-top: 20px;
        }
        .back-to-home a {
            color: #007bff;
            text-decoration: none;
        }
        .back-to-home a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="login-header">
                <h1>Đăng nhập trang quản trị</h1>
                <p>Vui lòng đăng nhập để tiếp tục</p>
            </div>
            
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <form class="login-form" method="POST" action="{{ route('admin.login.submit') }}">
                @csrf
                <div class="form-group">
                    <label for="email-login">Email</label>
                    <input type="email" class="form-control" id="email-login" name="email-login" required>
                </div>
                <div class="form-group">
                    <label for="password-login">Mật khẩu</label>
                    <input type="password" class="form-control" id="password-login" name="password-login" required>
                </div>
                <button type="submit" class="btn btn-login">Đăng nhập</button>
            </form>
        </div>
    </div>
</body>
</html> 