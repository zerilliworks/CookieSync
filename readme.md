## Cookie Sync
### A Silly Application to Store Cookie Clicker Saves

![CookieSync saves list page](http://zerilliworks.net/cookiesync/images/screen-home.png)

This application is the result of some weekend tinkering after becoming sick of emailing Cookie Clicker save data to
myself. The project took off, and now it's become rather impressive (ya know, for something that exists because of
Cookie Clicker).

CookieSync is written PHP using Laravel. It is capable of decoding Cookie Clicker save data and keeping
track of saves for each user. Log in or create an account in one step to start saving. Very little data is collected --
It's pretty much anonymous. CookieSync does incorporate a sharing feature to make saved games public with a shortened
link.

![CookieSync saves list page](http://zerilliworks.net/cookiesync/images/screen-list.png)

The application has a simple Bootstrap web interface to stretch a thin skin over the backend for now. A better
interface is on the short part of the long list of upgrades.

CookieSync has complex stat tracking and statistics features to chart and visualise save data. View breakdowns
of buildings, income, ROI, expenses, and your cookies earned over over time.

![CookieSync game list graphs](http://zerilliworks.net/cookiesync/images/screen-game.png)
![CookieSync single save graphs](http://zerilliworks.net/cookiesync/images/screen-income.png)

As it stands, CookieSync is perhaps 80% complete. Recent additions include warp-speed caching, deep stat tracking,
massive speed improvements, career histories, charts and graphs, worldwide stat aggregation, and user-friendly features
like *password resets* (which, believe it or not, I didn't bother with at first).

CookieClicker doesn't ask for or store your email address yet, so there are no options for password recovery if you
forget your login. I plan to include email as part of the signup process to facilitate such a feature. I
really have no other use for your email, and by the time I ask for that information, the system will be prepared to
encrypt it.

At one point, CookieSync was simple enough to clone and run it on your own. Now, it's a high-performance
hosted service with nontrivial configuration. It's been incredibly fun (though frustrating) to puzzle out all of the
details for CookieSync, mainly as a lesson to myself. CookieSync is the proving grounds for my newest and most gonzo
development styles.

It is currently hosted at [zeril.li/cookiesync](http://zeril.li/cookiesync).

(As if anyone cares) the technologies at work within CookieSync include:

- Redis [redis.io][redis]
- MySQL [mysql.org][msql]
- HHVM [hhvm.com][hhvm]
- Laravel 4.1 [laravel.com][laravel]
- Microsoft Asirra [research.microsoft.com][asirra]
- Twitter Bootstrap 3 [getbootstrap.com][bootstrap]
- Cartalyst Arsenal [cartalyst.com][arsenal]
- IronMQ [iron.io][iron]
- nginx [nginx.org][nginx]

[redis]: http://redis.io  "Redis"
[msql]: http://www.myql.com "MySQL"
[hhvm]: http://hhvm.com "HHVM"
[laravel]: http://laravel.com "Laravel"
[asirra]: http://research.microsoft.com/en-us/um/redmond/projects/asirra/ "Microsoft Asirra"
[bootstrap]: http://getbootstrap.com "Twitter Bootstrap 3"
[arsenal]: https://cartalyst.com/#arsenal-start "Cartalyst Arsenal"
[iron]: http://www.iron.io/mq "IronMQ"
[nginx]: http://nginx.org "Nginx"

By this time, I've figured that some of you might stand to learn something from the way I've architected my
application. Aspiring Laravel developers may get a kick out of it. Seasoned Laravel developers will probably get
a few good laughs. I make no apologies: This thing is a toy, but it's been the foundation of incredible learning
for me. It makes no money and has had me tearing my hair out, but I don't regret even one second of the time
spent building it.