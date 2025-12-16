<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <title>Post Test</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-full bg-gray-100 dark:bg-azwara-darkest text-gray-900 dark:text-white">
    @yield('content')
@stack('script')
</body>
</html>
