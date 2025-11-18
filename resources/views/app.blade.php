<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
    <link rel="icon" type="image/x-icon" href="/assets/img/favicon.png">
    @vite('resources/js/app.js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @inertiaHead
</head>
<body>
@routes
@inertia
</body>
</html>
