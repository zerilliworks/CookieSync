## Cookie Sync
### A Silly Application to Store Cookie Clicker Saves

This application is the result of some weekend tinkering after becoming sick of emailing Cookie Clicker save data to myself.

CookieSync is written PHP using Laravel. It is capable of decoding Cookie Clicker save data (mostly) and keeping
track of saves for each user. Log in or create an account in one step to start saving. Very little data is collected --
It's pretty much anonymous. CookieSync does incorporate a sharing feature to make saved games public with a shortened
link.

The application has a simple Bootstrap web interface to stretch a thin skin over the backend for now. A better
interface is on the short part of the long list of upgrades.

As it stands, CookieSync is perhaps 50% complete. To be honest, the only remotely interesting part of this is the Cookie
Clicker save decoding, found in `app/models/Save.php`.

Though the migrations specify MySQL engines, the application is database-agnostic and seriously simple.

Because of its simplicity, you can clone this sucker, do `php artisan serve` and hit the ground running. In fact, the 
testing version is just stored in my Dropbox and run that way. So if you're the paranoid type and don't trust me
with your saves, then you are free to run it privately. *(In fact, it runs pretty great with Dropbox doing the syncing.)*

It is currently hosted at [cookiesync.zerilliworks.net](http://cookiesync.zerilliworks.net).
