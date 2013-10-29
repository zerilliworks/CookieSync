//javascript:q = location.href;
//if (document.getSelection) {
//    d = document.getSelection();
//} else {
//    d =\'\';};p=document.title;void(open(\'https://pinboard.in/add?showtags=yes&url=\'+encodeURIComponent(q)+\'&description=\'+encodeURIComponent(d)+\'&title=\'+encodeURIComponent(p),\'Pinboard\',\'toolbar=no,scrollbars=yes,width=750,height=700\'));

javascript:window.open('http://localhost:8000/external?d=' + Game.WriteSave(1), "CookieSync_Save", "toolbar=no,scrollbars=yes,width=200,height=200");