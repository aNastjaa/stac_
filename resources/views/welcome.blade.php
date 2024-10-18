<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landing Page</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
            margin: 0;
        }

        .container {
            text-align: center;
        }

        h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
        }

        .buttons {
            margin-top: 20px;
        }

        .buttons a {
            display: inline-block;
            margin: 0 10px;
            padding: 10px 20px;
            background-color: black;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: background-color 0.3s ease;
        }

        .buttons a:hover {
            background-color: #333;
        }

        p {
            font-size: 1.2rem;
            color: gray;
        }
    </style>
</head>
<body>
    <div class="container">
        @auth
            <h1>Welcome, {{auth()->user()->username}}</h1>
        @else
            <h1>Welcome, Guest</h1>
        @endauth

        <p>Discover the platform and start creating!</p>

        <div class="buttons">
            @guest
                <a href="{{ route('login') }}">Log In</a>
                <a href="{{ route('register') }}">Register</a>
            @else
                <a href="{{ route('profile') }}">Go to Profile</a>
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" style="background: black; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer;">Logout</button>
                </form>
            @endguest
        </div>
    </div>
</body>
</html>
