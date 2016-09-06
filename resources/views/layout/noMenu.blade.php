<!doctype html>
<html lang="en" ng-app="app">

@include('tiles.head')

<header>
    @yield('additional_header_content')
</header>


<body>
    @yield('content')
</body>


@yield('scripts')

</html>