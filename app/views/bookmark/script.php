javascript:(function(){if(!Game.CookieSync){Game.CookieSync={enabled:false,last_saved_at:new Date,save_interval:0,original_save_method:Game.WriteSave,enable:function(){var e=this.original_save_method;Game.WriteSave=function(t){e(t);var n=e(1);console.log('CookieSync: Writing save...');if(Game.CookieSync.shouldSave()){Game.CookieSync.markSave();(function(){console.log('CookieSync: Firing iframe save');var e=document.createElement('iframe');e.setAttribute('src','https://zeril.li/cookiesync/external?d='+n);e.setAttribute('id','cookiesync-frame');if(document.getElementById('cookiesync-frame')){document.body.replaceChild(e,document.getElementById('cookiesync-frame'))}else{document.body.appendChild(e)}})()}return n};this.enabled=true},disable:function(){Game.WriteSave=this.original_save_method},isEnabled:function(){return this.enabled},isDisabled:function(){return!this.enabled},setAutoSaveInterval:function(e){this.save_interval=e*60*1e3},markSave:function(){this.last_saved_at=new Date},shouldSave:function(){return true}}}if(Game.CookieSync.isDisabled()){Game.CookieSync.enable();Game.CookieSync.setAutoSaveInterval(2);Game.Notify('CookieSync auto-saves enabled.')}})()