<!doctype html>
<html lang="en" ng-app="app">

@include('tiles.head')

<header>
    @include('tiles.menu.mainMenu')

    @yield('additional_header_content')
</header>


<body>
@yield('content')
</body>

<footer>
    @yield('footer')
</footer>

@yield('scripts')

</html>

