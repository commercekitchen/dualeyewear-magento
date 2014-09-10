var Downloads = Class.create();
Downloads.prototype = {

    initialize: function(){

    },

    postInit: function(){
        this.popup = $('downloads_video_popup');
        this.overlay = $('downloads_video_overlay');
        this.closeButton = $('close_popup');

        this.overlay.observe('click', function(event){
            downloads.close();
        });

        this.closeButton.observe('click', function(event){
            downloads.close();
        });

        width = window.innerWidth || document.documentElement.clientWidth;
        posLeft = (width - $('downloads_video_popup').getWidth()) / 2;
        posLeft += 'px';
        posTop = '30%';
        this.popup.setStyle({left: posLeft, top: posTop});

        this.overlay.hide();
        this.popup.hide();
    },

    showSpinner: function(){
        $('dl-spinner').addClassName('spin');
        $('dl_video_content').hide();
    },

    hideSpinner: function(){
        $('dl-spinner').removeClassName('spin');
        $('dl_video_content').show();
    },

    open:function(url,title,params){
        this.overlay.show();
        this.popup.show();
        this.showSpinner();
        new Ajax.Request(url,{
            method: 'post',
            parameters: params,
            onSuccess:function(transport){
                response = new String(transport.responseText);
                downloads.hideSpinner();
                $('dl_video_title').update('<h2>' + title + '</h2>');
                $('dl_video').update(response);
            }
        });
    },

    updateDownloads: function(requestUrl, linkUrl, newTab){
        if(newTab){
            window.open(
                linkUrl,
                '_blank'
            );
        } else {
            setLocation(linkUrl);
        }
        new Ajax.Request(requestUrl,{
            method: 'post',
            onSuccess:function(transport){

            }
        });
    },

    close:function(){
        this.overlay.hide();
        this.popup.hide();
        $('dl_video').update('');
    }
};