<nav class="navbar navbar-inverse" role="navigation">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
        <a class="navbar-brand" href="/">CookieSync</a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse navbar-ex1-collapse">
        <ul class="nav navbar-nav">
            <li class="{{ if_page('mysaves*', 'active') }}"><a href="/mysaves">My Saves</a></li>
            <li class="{{ if_page('games*', 'active') }}"><a href="/games">My Games</a></li>
            <li class="{{ if_page('options*', 'active') }}"><a href="/options">Options</a></li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
            <li><a href="/logout">Log Out</a></li>
    </div><!-- /.navbar-collapse -->
</nav>