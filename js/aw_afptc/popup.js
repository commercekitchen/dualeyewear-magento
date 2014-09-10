var awAfptcPopup = Class.create();
awAfptcPopup.prototype = {
    initialize: function(config) {
       this.config = config;
    },
    init: function() {
        var options = {
            parameters : {},
            method: 'get',
            onSuccess: this.onSuccess(this)
        };
        this.onSuccess = this.onSuccess.bindAsEventListener(this);
        new Ajax.Request(this.config.requestUrl, options);
    },
    onSuccess: function(object) {
        return function(transport) {
            try {
                eval("var json = " + transport.responseText + " || {}");
            } catch(e) {
                console.log(e);
                return;
            }
            $(object.config.el).update(json.content);
            $(object.config.decline).observe('click', object._observeDeclineAction.bind(object));
            $(object.config.overlay).observe('click', object._observeDeclineAction.bind(object));
            object.align();
        };
    },
    align: function() {
        var el = $(this.config.el);
        el.setStyle({
            top: document.viewport.getHeight() / 2 - el.getHeight() / 2 + 'px',
            left: document.viewport.getWidth() / 2 - el.getWidth() / 2 + 'px'
        });   
        
        Event.observe(window, 'resize', function() { this.resizeBlock(el) }.bind(this));
        
        Effect.Appear(el, {duration: 0.4});
        $(this.config.overlay).show();
    },
    collectPos: function(el) {
        var x, y;
        var elWidth = el.getWidth();
        var docWidth = document.viewport.getWidth();
        x = docWidth/2 - elWidth/2;
        var elHeight = el.getHeight();
        var docHeight = document.viewport.getHeight();
        y = docHeight/2 - elHeight/2;
        
        return [x, y];
    },
    resizeBlock: function(el) {
  
        el.setStyle({
            height: 'auto', width: 'auto'
        });
        var xy = this.collectPos(el);
        
        if (xy[0] < 50) {
            xy[0] = 50;
            el.setStyle({
                width: (document.viewport.getWidth() - 100) + 'px'
            });
        }
        if (xy[1] < 50) {
            xy[1] = 50;
            el.setStyle({
                height: (document.viewport.getHeight() - 100) + 'px'
            });
        }
        el.setStyle({ 'left': xy[0] + 'px', 'top': xy[1] + 'px'});
    },
    _observeDeclineAction: function() {
        if(this.config.declineCheck
            && $(this.config.declineCheck)
            && $(this.config.declineCheck).checked
            && $(this.config.cookie)
        ) {
            var date = new Date();
            date.setTime(date.getTime() + this.config.cookieLifetime);
            Mage.Cookies.set($(this.config.cookie).value, true, date);
        }
        try {
             Effect.Fade($(this.config.el), {duration: 0.4});
             Effect.Fade($(this.config.overlay), {duration: 0.4});
        } catch(e) {
            console.log(e);
            return;
        }
    }
};