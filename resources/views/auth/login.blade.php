<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
    <form class="login-form" onsubmit="event.preventDefault(); loginUser();">
        @csrf
        <h2>Nice to see you again</h2>

        <input type="text" id="email" name="email" placeholder="Email">

        <input type="password" id="password" name="password" placeholder="Password">

        <div class="checkbox">
           <input type="checkbox" name="remember" id="remember">
           <label for="remember">Remember me</label>
        </div>

        <button type="submit">Login</button>

        <p id="error" class="error"></p> <!-- Display error message here -->
    </form>

    <script>
        function loginUser() {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            fetch('/api/auth/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    email: email,
                    password: password
                })
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(errorData => {
                        throw new Error(errorData.message);
                    });
                }
                return response.json();
            })
            .then(data => {
                // Login successful, show an alert
                alert('Login successful!');

                // Store token in local storage (if needed)
                localStorage.setItem('authToken', data.token);

                // Redirect to profile page
                window.location.href = data.redirect_url;
            })
            .catch(error => {
                // Show error message
                document.getElementById('error').textContent = error.message;
            });
        }
    </script>
</body>
</html>
