<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Profile</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            height: 100vh;
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        button {
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

    </style>
</head>
<body>
 <h1 class="username">Hey, {{ auth()->user()->username }}</h1>

 <form action="{{ route('logout') }}" method="post">
    @csrf

    <button class="logout">Log out</button>
 </form>
</body>
</html>
