<!doctype html>
<html lang="en" ng-app="app">
<head>
    <meta charset="UTF-8">
    @yield('additional_meta_data')

    <title>@yield('page_title')</title>

    <script type="text/javascript" src="{{asset(elixir('js/app.js'))}}"></script>
    <script type="text/javascript" src="{{asset('js/utils.js')}}"></script>
    <script type="text/javascript" src="http://cassiohudson.com/utilities/js/standardUtilities/su_1_0_0.js"></script>
    <script type="text/javascript" src="http://cassiohudson.com/utilities/js/angularStandardUtilities/asu_1_0_0.js"></script>
    <script type="text/javascript" src="http://cassiohudson.com/utilities/js/showServerMessages/ssm_3_0_0.js"></script>

    @yield('required_scripts')

    <script>
        const PROJECT_BASE = "{{url('/')}}";
        const API_URL_LOCATION = "{{url('/api')}}";
        displayMessageFromUrlPrams();
    </script>

    <link type="text/css" rel="stylesheet" href="{{asset(elixir('css/app.css'))}}">
    @yield('styles')

    @yield("additional_head_content")
</head>

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

