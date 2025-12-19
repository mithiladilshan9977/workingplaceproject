<!DOCTYPE html>
<html>
<head>
    <title>Sign Up</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .signup-container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            width: 350px;
        }
        .signup-container h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }
        .signup-container input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }
        .signup-container button {
            width: 100%;
            padding: 12px;
            background-color: #3490dc;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .signup-container button:hover {
            background-color: #2779bd;
        }
        .error-message, .success-message {
            text-align: center;
            margin-bottom: 10px;
            font-size: 14px;
        }
        .error-message {
            color: red;
        }
        .success-message {
            color: green;
        }
        .login-link {
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
        }
        .login-link a {
            color: #3490dc;
            text-decoration: none;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <h2>Create Account</h2>

        <!-- Display errors -->
        @if($errors->any())
            <div class="error-message">
                {{ $errors->first() }}
            </div>
        @endif

        <!-- Display success message -->
        @if(session('success'))
            <div class="success-message">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('signup') }}">
            @csrf
            <input type="text" name="name" placeholder="Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="password_confirmation" placeholder="Confirm Password" required>
            <button type="submit">Sign Up</button>
        </form>

        <div class="login-link">
            Already have an account? <a href="{{ route('login') }}">Login</a>
        </div>
    </div>
</body>
</html>
