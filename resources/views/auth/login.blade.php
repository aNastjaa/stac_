<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log in</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }

        .login-form {
            background-color: white;
            padding: 20px 40px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
        }

        .login-form h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .login-form input {
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
        }

        .login-form button {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: none;
            background-color: black;
            color: white;
            border-radius: 8px;
            cursor: pointer;
        }

        /* .checkbox{
            margin-bottom: 10px;
        } */

        button:hover {
            background-color: #333;
        }

        p.error {
            color: red;
            font-size: 0.9rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <form action="{{ route('login') }}" method="POST" class="login-form">
        @csrf
        <h2>Nice you see you again</h2>

        <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
        @error('email')
            <p class="error">{{ $message }}</p>
        @enderror

        <input type="password" name="password" placeholder="Password" required>
        @error('password')
            <p class="error">{{ $message }}</p>
        @enderror

        <div class="checkbox">
           <input type="checkbox" name="remember" id="remember">
           <label for="remember">Remember me</label>
        </div>

        @error('failed')
            <p class="error">{{ $message }}</p>
        @enderror

        <button type="submit">Login</button>
    </form>
</body>
</html>
