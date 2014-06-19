<nav class="navbar navbar-default" role="navigation">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
        <a class="navbar-brand" href="/cookiesync" style="font-family: Kavoon, Helvetica, Arial, sans-serif">CookieSync</a>
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#main-navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse navbar-ex1-collapse" id="main-navbar-collapse">
        <ul class="nav navbar-nav">
            <li class="{{ if_page('*mysaves*', 'active') }}"><a href="{{ action('SavesController@index') }}">My Saves</a></li>
            <li class="{{ if_page('*games*', 'active') }}"><a href="{{ action('GamesController@index') }}">My Games</a></li>
            <li class="{{ if_page('*options*', 'active') }}"><a href="{{ action('OptionsController@getIndex') }}">Options</a></li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
            @if(Auth::check())
            <li><a href="{{ route('logout') }}">Log Out</a></li>
            @else
            <li><a href="{{ route('login') }}">Log In</a></li>
            @endif
        </ul>
    </div><!-- /.navbar-collapse -->
</nav>
