
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <title>{{$title or 'Crud'}}</title>

    @yield('head')
</head>
<body>
{!!$presenter->snippets['header']!!}
    @yield('body')

    @include('global.inc.angular.initialize')
{!!$presenter->snippets['footer']!!}
</body>
</html>