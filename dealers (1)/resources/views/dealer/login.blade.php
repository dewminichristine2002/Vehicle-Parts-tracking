<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome Back!</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f8f9fa;
            background-image: url('../images/login.png'); 
            background-size: cover;
            background-position: center;
            padding: 20px;
        }
        
        .login-container {
            background: white;
            border-radius: 10px;
            padding: 30px;
            max-width: 750px;
            width: 100%;
            box-shadow: 0px 0px 20px rgba(0, 0, 0, 0.1);
        }
        
        .login-content {
            display: flex;
            flex-direction: row;
            align-items: center;
        }
        
        .login-form {
            flex: 1;
            padding-right: 30px;
            font-family: 'Nunito Sans', sans-serif;
        }
        
        .image-section {
            flex: 1;
            display: flex;
            justify-content: center;
        }
        
        .image-section img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            object-fit: cover;
        }
        
        .btn-login {
    background-color: #BC171E;
    border: none;
    color: white;
    padding: 10px;
    width: 50%;
    font-weight: 600;
    display: block; 
    margin: 0 auto; 
    text-align: center;
}
        
        .btn-login:hover {
            background-color: #a0141a;
            color: white;
        }
        
        .form-control {
            padding: 12px 15px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }
        
        .form-control:focus {
            border-color: #BC171E;
            box-shadow: 0 0 0 0.25rem rgba(188, 23, 30, 0.25);
        }
        
        .password-container {
            position: relative;
        }
        
        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
        }
        
        .forgot-password {
            display: flex;
            justify-content: flex-end;
        }
        
        .error-message {
            color: #BC171E;
            text-align: center;
            margin-bottom: 15px;
            font-weight: 500;
        }
        
        @media (max-width: 768px) {
            .login-content {
                flex-direction: column;
            }
            
            .login-form {
                padding-right: 0;
                padding-bottom: 30px;
            }
            
            .image-section {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-content">
            <div class="login-form">
                <h2 class="fw-bold text-center mb-3">Welcome Back! </h2>
                <p class="text-center text-muted mb-4">Sign in to your dealer account</p>
                
                @if ($errors->any())
                    <div class="error-message">
                        {{ $errors->first() }}
                    </div>
                @endif
                
                <form method="POST" action="{{ route('dealer.login') }}">
                    @csrf
                    <div class="mb-3">
                        <input type="email" name="email" class="form-control" placeholder="Email" required>
                    </div>
                    <div class="mb-3 password-container">
                        <input type="password" name="password" class="form-control" placeholder="Password" id="password" required>
                        <span class="password-toggle" id="togglePassword">
                            <i class="bi bi-eye"></i>
                        </span>
                    </div>
                    <div class="mb-4 forgot-password">
                        <a href="#" class="text-decoration-none" style="color: #1403ff;">Forgot Password?</a>
                    </div>
                    <button type="submit" class="btn btn-login">Login</button>
                </form>
            </div>
            <div class="image-section">
                <img src="{{ URL('images/front image.png') }}" alt="Dealer Login Image">
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');
        
        togglePassword.addEventListener('click', function() {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.querySelector('i').classList.toggle('bi-eye');
            this.querySelector('i').classList.toggle('bi-eye-slash');
        });
    </script>
</body>
</html>