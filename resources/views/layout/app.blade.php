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
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <a class="navbar-brand" href="{{url("/")}}">Aseop</a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">My Creations <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="{{url('/dungeons')}}">Dungeons</a></li>
                            <li><a href="{{url('/traps')}}">Traps</a></li>
                        </ul>
                    </li>

                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">My Creation Traits <span class="caret"></span></a>
                        <ul class="dropdown-menu">
                            <li><a href="{{url('/dungeonTraits')}}">Dungeons</a></li>
                        </ul>
                    </li>

                    @yield('menu_right')
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <!-- Authentication Links -->
                    @if (Auth::guest())
                        <li><a href="{{ url('/login') }}">Login</a></li>
                        <li><a href="{{ url('/register') }}">Register</a></li>
                    @else
                        <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                <li>
                                    <a href="{{ url('/logout') }}"
                                       onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();">
                                        Logout
                                    </a>

                                    <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                                        {{ csrf_field() }}
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endif
                    @yield('menu_left')

                </ul>
            </div>
        </div>
    </nav>

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

