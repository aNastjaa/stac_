<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>stac</title>
    @vite(['resources/js/app.jsx']) <!-- Vite will handle the assets for you -->
</head>
<body>
    <div id="root"></div> <!-- React will mount here -->
</body>
</html>
