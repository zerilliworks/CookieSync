<nav class="navbar" role="navigation">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
        <a class="navbar-brand" href="/cookiesync" style="font-family: Kavoon, Helvetica, Arial, sans-serif">CookieSync</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse navbar-ex1-collapse">
        <ul class="nav navbar-nav">
            <li class="{{ if_page('mysaves*', 'active') }}"><a href="{{ action('SavesController@index') }}">My Saves</a></li>
            <li class="{{ if_page('games*', 'active') }}"><a href="{{ action('GamesController@index') }}">My Games</a></li>
            <li class="{{ if_page('options*', 'active') }}"><a href="{{ action('OptionsController@getIndex') }}">Options</a></li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
            <li><a href="{{ route('logout') }}">Log Out</a></li>
    </div><!-- /.navbar-collapse -->
</nav>
