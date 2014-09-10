/**
 * Morningtime Extensions
 * http://shop.morningtime.com
 *
 * @extension   FancyZoom
 * @type        Simple product media lightbox
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category    Morningtime
 * @package     Morningtime_FancyZoom
 * @copyright   Copyright (c) 2011-2012 Morningtime Internetbureau B.V. (http://www.morningtime.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

// append strings with compulsory suffix, if missing 
Object.extend(String.prototype, {
    ensureEndsWith: function(str) {
        return this.endsWith(str) ? this : this + str;
    },
    px: function() {
        return this.ensureEndsWith('px');
    }
});

// append 'px' suffix to dimensions, if missing
Object.extend(Number.prototype, {
    px: function() {
        return this.toString().px();
    }
});

// get window sizes correctly
var Window = {
    size: function() {
        var width = window.innerWidth || (window.document.documentElement.clientWidth || window.document.body.clientWidth);
        var height = window.innerHeight || (window.document.documentElement.clientHeight || window.document.body.clientHeight);
        var x = window.pageXOffset || (window.document.documentElement.scrollLeft || window.document.body.scrollLeft);
        var y = window.pageYOffset || (window.document.documentElement.scrollTop || window.document.body.scrollTop);
  
        return {
            'width': width, 
            'height': height, 
            'x': x, 
            'y': y
        }
    }
}

var FancyZoomBox = {
    zooming: false,
    setup: false,
  
    init: function(directory) {
        if (FancyZoomBox.setup) return;
        FancyZoomBox.setup = true;
    
        // test for IE version
        var ie = navigator.userAgent.match(/MSIE\s(\d)+/);
        if (ie) {
            var version = parseInt(ie[1]);
            Prototype.Browser['IE' + version.toString()] = true;
            Prototype.Browser.ltIE7 = (version < 7) ? true : false;
        }
        
        // fancyzoombox html
        var html = '<div id="control_overlay" style="display: none; background-color: #000000;position: fixed; top: 0px; left: 0px; width: 100%; height: 100%; z-index: 9998; opacity: 0.65;"></div>\
        <div id="zoom" style="display: none;"> \
            <table id="fancy-zoom-table"> \
                <tbody> \
                    <tr> \
                        <td> \
                            <div id="fancy-zoom-content"> \
                            </div> \
                            <div id="fancy-zoom-content-gallery"> \
                            </div> \
                        </td> \
                    </tr> \
                </tbody> \
            </table> \
            <a href="#" id="fancy-zoom-x"> \
                <img src="' + directory + '/closebox.png" /> \
            </a> \
        </div>';
    
        var body  = $$('body').first();
        body.insert(html);
    
        // do it
        FancyZoomBox.zoom = $('zoom');
        FancyZoomBox.control_overlay = $('control_overlay');
        FancyZoomBox.zoom_table = $('fancy-zoom-table');
        FancyZoomBox.zoom_close = $('fancy-zoom-x');
        FancyZoomBox.zoom_content = $('fancy-zoom-content');
        FancyZoomBox.zoom_content_gallery = $('fancy-zoom-content-gallery');
        FancyZoomBox.zoom_close.observe('click', FancyZoomBox.hide);
        FancyZoomBox.middle_row = $A([$$('td.ml'), $$('td.mm'), $$('td.mr')]).flatten();
        FancyZoomBox.cells = FancyZoomBox.zoom_table.select('td');
    
        // close if clicked outside box
        $$('html').first().observe('click', function(e) {
            var click_in_zoom = e.findElement('#zoom'), zoom_display = FancyZoomBox.zoom.getStyle('display');
            if (zoom_display == 'block' && !click_in_zoom) {
                FancyZoomBox.hide(e);
            }
        });

        // close if esc
        $(document).observe('keyup', function(e) {
            var zoom_display = FancyZoomBox.zoom.getStyle('display');
            if (e.keyCode == Event.KEY_ESC && zoom_display == 'block') {
                FancyZoomBox.hide(e);
            }
        });
    
        // IE6 does not support transparent PNG, fall back to GIF
        if (Prototype.Browser.ltIE7) {
            FancyZoomBox.switchBackgroundImagesTo('gif');
        }    
    },
  
    // show fancyzoombox
    show: function(e) {
        e.stop();
        if (FancyZoomBox.zooming) return;
        FancyZoomBox.zooming = true;
    	FancyZoomBox.control_overlay.show();
        var element = e.findElement('a');
        var related_div = element.content_div;
        var related_gallery_div = element.content_gallery_div;
        var width = 914;
        var height  = 610;
        var d = Window.size();
        var yOffset = document.viewport.getScrollOffsets()[1];
        var newTop = Math.max((d.height/2) - (height/2) + yOffset, 0);
        var newLeft = (d.width/2) - (width/2);
        var gallery = element.gallery;
    
        FancyZoomBox.curTop = e.pointerY();
        FancyZoomBox.curLeft = e.pointerX();
        FancyZoomBox.moveX = -(FancyZoomBox.curLeft - newLeft);
        FancyZoomBox.moveY = -(FancyZoomBox.curTop - newTop);
    
        FancyZoomBox.zoom.hide().setStyle({
            position: 'absolute',
            top: FancyZoomBox.curTop.px(),
            left: FancyZoomBox.curLeft.px()
        });
    
        new Effect.Parallel([
            new Effect.Appear(FancyZoomBox.zoom, {sync:true}),
            new Effect.Move(FancyZoomBox.zoom, {x: FancyZoomBox.moveX, y: FancyZoomBox.moveY, sync: true}),
            new Effect.Morph(FancyZoomBox.zoom, {
                style: {
                    width: width.px(),
                    height: height.px()
                },
                sync: true,
                beforeStart: function(effect) {
                    
                    // for IE, set middle row height
                    if (Prototype.Browser.IE) {
                        FancyZoomBox.middle_row.invoke('setStyle', {height: (height - 40).px()});
                    }
                    FancyZoomBox.fixBackgroundsForIE();
                },
                afterFinish: function(effect) {
                    FancyZoomBox.zoom_content.innerHTML = related_div.innerHTML;
                    if (gallery == 'true') {
                        FancyZoomBox.zoom_content_gallery.innerHTML = related_gallery_div.innerHTML;    
                    }
                    FancyZoomBox.unfixBackgroundsForIE();
                    FancyZoomBox.zoom_close.show();
                    FancyZoomBox.zooming = false;
                    
                    // listen to swap active images
                    if (gallery == 'true') {
                        $$("#zoom a.fancy-zoom-gallery-active").each(function(e) {
                            var href = e.readAttribute('href').gsub(/^#/, '');
                            e.observe('click', function(e) {
                                FancyZoomBox.zoom_content.innerHTML = $(href).innerHTML;
                            }); 
                        });
                    }
                }
            })
        ], { 
            duration: 0.5 
        });
    },
  
    // hide fancybox
    hide: function(e) {
        e.stop();
        if (FancyZoomBox.zooming) return;
        FancyZoomBox.control_overlay.hide();
        FancyZoomBox.zooming = true;  
        new Effect.Parallel([
            new Effect.Move(FancyZoomBox.zoom, {x: FancyZoomBox.moveX*-1, y: FancyZoomBox.moveY*-1, sync: true}),
            new Effect.Morph(FancyZoomBox.zoom, {
                style: {
                    width: '1'.px(),
                    height: '1'.px()
                },
                sync: true,
                beforeStart: function(effect) {
                    FancyZoomBox.fixBackgroundsForIE();
                    FancyZoomBox.zoom_content.innerHTML = '';
                    FancyZoomBox.zoom_content_gallery.innerHTML = '';
                    FancyZoomBox.zoom_close.hide();
                },
                afterFinish: function(effect) {
                    FancyZoomBox.unfixBackgroundsForIE();
                    FancyZoomBox.zooming = false;
                }
            }),
            new Effect.Fade(FancyZoomBox.zoom, {sync:true})
        ], { duration: 0.5 });
    },
  
    // switches the backgrounds of the cells and the close image to png's or gif's
    // fixes ie's issues with fading and appearing transparent png's with 
    // no background and ie6's craptacular handling of transparent png's
    switchBackgroundImagesTo: function(to) {
        FancyZoomBox.cells.each(function(td) {
            var bg = td.getStyle('background-image').gsub(/\.(png|gif|none)\)$/, '.' + to + ')');
            td.setStyle('background-image: ' + bg);
        });
        var close_img = FancyZoomBox.zoom_close.firstDescendant();
        var new_img = close_img.readAttribute('src').gsub(/\.(png|gif|none)$/, '.' + to);
        close_img.writeAttribute('src', new_img);
    },
  
    // prevents the thick black border that happens when appearing or fading png in IE
    fixBackgroundsForIE: function() {
        if (Prototype.Browser.IE7) { FancyZoomBox.switchBackgroundImagesTo('gif'); }
    },
 
    // swaps back to png's for prettier shadows
    unfixBackgroundsForIE: function() {
        if (Prototype.Browser.IE7) { FancyZoomBox.switchBackgroundImagesTo('png'); }
    }
}

var FancyZoom = Class.create({
    initialize: function(element) {
        this.options = arguments.length > 1 ? arguments[1] : {};
        FancyZoomBox.init(this.options.directory);
        this.element = $(element);
        if (this.element) {
            this.element.content_div = $(this.element.readAttribute('href').gsub(/^#/, ''));
            this.element.content_div.hide();
            if (this.options.gallery == 'true') {
                this.element.content_gallery_div = $('fancy-zoom-gallery');
                this.element.content_gallery_div.hide();    
            }
            this.element.zoom_width = this.options.width;
            this.element.zoom_height = this.options.height;
            this.element.gallery = this.options.gallery;
            this.element.observe('click', FancyZoomBox.show);
        }
    }
});
