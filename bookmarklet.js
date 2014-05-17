(function () {
    (window.jQuery || document.write('<script src="js/vendor/jquery-1.11.0.min.js"><\/script>'));

    var jA = document.createElement('script');
    jA.setAttribute('type', 'text/javascript');
    jA.setAttribute('src', 'http://cookiesync.zerilliworks.net/autosave.js?' + new Date().getTime());
    document.body.appendChild(jA);
}());

javascript:(function(){window.open('http://zeril.li/cookiesync/external?d=' + Game.WriteSave(1), 'CookieSync_Save', 'toolbar=no,scrollbars=yes,width=750,height=700');window.focus();}());







Game.WriteSave();

if(!Game.CookieSync) {
    Game.CookieSync = {
        token: token,
        asyncSave: function() {
            var asyncCSRequest = new XMLHttpRequest();
            asyncCSRequest.onload = function() {
                Game.CookieSync.token = this.responseText;
            };
            asyncCSRequest.onerror = function() {

            };

            asyncCSRequest.open('POST', "http://zeril.li/cookiesync/external/autosave", true);
            asyncCSRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            asyncCSRequest.send('token=' + Game.CookieSync.token + '&data=' + Game.WriteSave(1));
        }
    };

    Game.CookieSync.saveInterval = setInterval(Game.CookieSync.asyncSave, 60000);
}

if(Game.CookieSync.token) {
    var asyncCSRequest = new XMLHttpRequest();
    asyncCSRequest.onload = function() {
        Game.CookieSync.token = this.responseText;
    };
    asyncCSRequest.onerror = function() {

    };

    asyncCSRequest.open('POST', "http://zeril.li/cookiesync/external/autosave", true);
    asyncCSRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    asyncCSRequest.send('token=' + Game.CookieSync.token + '&data=' + Game.WriteSave(1));
}
else
if(location.search.match(new RegExp("cs_token"))) {

    var tokenRegEx = new RegExp("[\\?&]cs_token=([^&#]*)"),
        token = tokenRegEx.exec(window.location.search);

    if(Game.CookieSync && typeof Game.CookieSync === 'object') {
        Game.CookieSync.token = token;
    } else {
        Game.CookieSync = {
            token: token
        };

        Game.CookieSync.saveInterval = setInterval(function() {

        }, 60000);
    }
}
else {
    window.open('http://zeril.li/cookiesync/external?d=' + Game.WriteSave(1), 'CookieSync_Save', 'toolbar=no,scrollbars=yes,width=750,height=700');
}
