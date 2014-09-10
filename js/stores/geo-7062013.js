    function addNewMarkers(page){
        
        var to;
        
        map.clearOverlays();
        bounds = null; 
        bounds = new GLatLngBounds(); 
        
        if (storesCount<(page)*storePageSize) {
            to = storesCount;
        } else {
            to = (page)*storePageSize;
        }
        
        for (var i=(page-1)*storePageSize+1; i<=to; i++){
                showAddress(globalAddress[i].lat,globalAddress[i].lng,globalAddress[i].address,i+'. '+globalAddress[i].title,i);
        }        
    }
  //var $jqr=jQuery.noConflict();
 jQuery(document).ready(function(){
    $.fn.quickPager = function(options) {

        var defaults = {
            pageSize: 5,
            currentPage: 1,
            holder: null,
            pagerLocation: "after"
        };

        var options = $.extend(defaults, options);
        var total=0;
        
        var prodShow;
        var prodCount;
        var first; 
        var last;

        return this.each(function() {

            var selector = $(this);    
            var pageCounter = 1;
            
            selector.children(".page_it").each(function(i){ 
                total++;
            });
            
            if (total==0) {
                $("div.toolbar label strong #inform").html('No Items, Please change filter');
            }  
            else {
                first=options.pageSize*(options.currentPage-1)+1;
                last=options.pageSize*(options.currentPage);
                if (last>total) {last=total;}
                $("div.toolbar label strong #inform").html(first+' - '+last+' OF '+total+' | ');
            }    
            
            total=0;
            selector.children(".page_it").each(function(i){ 
                total++;
                if(i < pageCounter*options.pageSize && i >= (pageCounter-1)*options.pageSize) {
                $(this).addClass("simplePagerPage"+pageCounter);
                }
                else {
                    $(this).addClass("simplePagerPage"+(pageCounter+1));
                    pageCounter ++;
                }    

            });

            // show/hide the appropriate regions 
            selector.children().hide();
            selector.children(".simplePagerPage"+options.currentPage).show();

            if(pageCounter <= 1) {
                return;
            }

            //Build pager navigation
            function buildPager(){
                var pagePrevios="<td class='back'></td>";
                var pageNext="<td class='next'></td>";
                
                var pageNav='';
                var pageNavFirstPart = "<ul class='simplePagerNav'><table class='pager-jquery'><tr>";
                
                var difference = 0;
                
                for (i=1;i<=pageCounter;i++){                
                    
                    difference = Math.abs(options.currentPage-i);
                    
                    if ((i==options.currentPage-1)) {
                        pagePrevios="<td class='back'><li class='simplePageNav"+i+"'><a class='link' rel='"+i+"' href='#'><<</a></li></td>";
                    }
                    if (i==options.currentPage+1) {
                        pageNext="<td class='next'><li class='simplePageNav"+i+"'><a class='link' rel='"+i+"' href='#'>>></a></li></td>";
                    }
                    
                    if (i==options.currentPage) {
                        pageNav += "<td><li class='currentPage simplePageNav"+i+"'><a class='link' rel='"+i+"' href='#'>"+i+"</a></li></td>";
                    }
                    else {
                        if (difference < 10){
                              pageNav += "<td><li class='simplePageNav"+i+"'><a class='link' rel='"+i+"' href='#'>"+i+"</a></li></td>";
                        } else {
                            if (i%20==0){
                              pageNav += "<td><li class='simplePageNav"+i+"'><a class='link' style='color:blue;' rel='"+i+"' href='#'>"+i+"</a></li></td>";  
                            }
                        }    
                    }
                }
                pageNav=pageNavFirstPart+pagePrevios+pageNav+pageNext;
                pageNav += "</tr></table></ul>";
                
                return pageNav;
            }
            
            
            var pageNav = buildPager();
            
            
            
            if(!options.holder) {
                switch(options.pagerLocation)
                {
                case "before":
                    
                    prodShow = $('#product_li > li:visible');
                    prodCount = prodShow.length;
                    
                    first=options.pageSize*(options.currentPage-1)+1;
                    last=options.pageSize*(options.currentPage);
                    if (last>total) {last=total;}
                    
                    $("div.toolbar label strong #inform").html(first+' - '+last+' OF '+total+' | ');
                    $("div.toolbar label strong #pagination").html(pageNav);
                    
                    //selector.before(pageNav);
                break;
                case "both":
                    selector.before(pageNav);
                    selector.after(pageNav);
                break;
                default:
                    selector.after(pageNav);
                }
            }
            else {
                $(options.holder).append(pageNav);
            }
            
            //pager navigation behaviour
            $(".simplePagerNav a").live('click', function() {

                //grab the REL attribute 
                var clickedLink = $(this).attr("rel");
                options.currentPage = clickedLink;
                
                addNewMarkers(clickedLink);
                
                pageNav = buildPager();
                $("div.toolbar label strong #pagination").html(pageNav);
                
                if(options.holder) {
                    $(this).parent("li").parent("ul").parent(options.holder).find("li.currentPage").removeClass("currentPage");
                    $(this).parent("li").parent("ul").parent(options.holder).find("a[rel='"+clickedLink+"']").parent("li").addClass("currentPage");
                }
                else {
                    
                    var temp_link=0;
                    if (clickedLink>1){
                        temp_link=parseInt(clickedLink)-1;
                        pagePrevios="<li class='simplePageNav"+temp_link+"'><a class='link' rel='"+temp_link+"' href='#'><<</a></li>";
                    } else {
                        pagePrevios="";
                    }
                    
                    if (parseInt(clickedLink)<(total/options.pageSize)){
                        temp_link=parseInt(clickedLink)+1;
                        pageNext="<li class='simplePageNav"+temp_link+"'><a class='link' rel='"+temp_link+"' href='#'>>></a></li>";
                    } else {
                        pageNext="";
                    }
                    
                    $('.next').html(pageNext);
                    $('.back').html(pagePrevios);
                    
                    
                    //remove current current (!) page
                    $(".simplePagerNav").find("li.currentPage").removeClass("currentPage");
                    //Add current page highlighting
                    $(".simplePagerNav").find("a[rel='"+clickedLink+"']").parent("li").addClass("currentPage");
                }

                //hide and show relevant links
                selector.children().hide();            
                selector.find(".simplePagerPage"+clickedLink).show();
                
                prodShow = $('#product_li > li:visible');
                prodCount = prodShow.length;
                
                first=options.pageSize*(options.currentPage-1)+1;
                last=options.pageSize*(options.currentPage);
                if (last>total) {last=total;}
                
                $("div.toolbar label strong #inform").html(first+' - '+last+' OF '+total+' | ');
                  
                return false;
            });
        });
    }
 });