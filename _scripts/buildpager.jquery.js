//-------------------------------------------------
//Quick Pager jquery plugin
//Created by dan and emanuel @geckonm.com
//www.geckonewmedia.com

//v1.1beta + proposal
//16/09/10 * added paggination
//18/09/09 * bug fix by John V - http://blog.geekyjohn.com/
//16/10/09 * proposal to uncouple page changing and selector by fairsayan@gmail.com
//-------------------------------------------------

(function($) {
	//
	var maxPages=6;

	$.fn.buildPager = function(page) {
		if(countPages > maxPages)
		{
			// disable all elements
			for(i=1;i<=countPages;i++){
				$("li.simplePageNav"+i).css('display','none');
			}

			// enable first and last element
			$("li.simplePageNav1").css('display','block');
			$("li.simplePageNav"+countPages).css('display','block');
			// if we are on first page show 2 next pages
			if(page==1)
			{
				$("li.simplePageNav2").css('display','block');
				$("li.simplePageNav3").css('display','block');
			}
			// if we are on the last page, show 2 prev pages
			if(page==countPages)
			{
				$("li.simplePageNav"+parseInt(countPages-1)).css('display','block');
				$("li.simplePageNav"+parseInt(countPages-2)).css('display','block');
			}

			// show one prev and one next element
			$("li.simplePageNav"+parseInt(page-1)).css('display','block');
			$("li.simplePageNav"+page).css('display','block');
			$("li.simplePageNav"+(parseInt(page)+1)).css('display','block');
			// skip 
			if(page-2  > 1)
				$("li.simplePageNavNext").css('display','block');
			else
				$("li.simplePageNavNext").css('display','none');
			if(countPages-2 > page)
				$("li.simplePageNavPrev").css('display','block');
			else
				$("li.simplePageNavPrev").css('display','none');

		}

	}

	$.fn.selectPage = function(page) {
		this.buildPager(page);
		$("li.currentPage").removeClass('currentPage');
		$("li.simplePageNav"+page).addClass('currentPage');
		// return
		return this.each(function() {
			//grab the REL attribute
			this.pagerOptions.currentPage = page;
			var selector = $(this);

			if(this.pagerOptions.holder) {
				$(this).parent("li").parent("ul").parent(this.pagerOptions.holder).find("li.currentPage").removeClass("currentPage");
				$(this).parent("li").parent("ul").parent(this.pagerOptions.holder).find("a[rel='"+page+"']").parent("li").addClass("currentPage");
			}
			else {
				//remove current current (!) page
				$(this).parent("li").parent("ul").parent(".simplePagerContainer").find("li.currentPage").removeClass("currentPage");
				//Add current page highlighting
				$(this).parent("li").parent("ul").parent(".simplePagerContainer").find("a[rel='"+page+"']").parent("li").addClass("currentPage");
			}

			//hide and show relevant links
			selector.children().hide();
			selector.find(".simplePagerPage"+page).show();
		});
	}

	$.fn.quickPager = function(options) {

		var defaults = {
				pageSize: 15,
				currentPage: 1,
				holder: null,
				pagerLocation: "before",
				usePageNumbers: true,
				usePrevNext: true,
				prevText: "",
				nextText: "",
        resetPaging: false,
        pageCounter: 1,
		};


		var options = $.extend(defaults, options);

		return this.each(function() {
			this.pagerOptions = options;

			var selector = $(this);	
      var pageCounter = options.pageCounter;

      if (!options.resetPaging) {
        
  			selector.wrap("<div class='simplePagerContainer'></div>");
  
  			selector.children().each(function(i){ 
  
  				if(i < pageCounter*options.pageSize && i >= (pageCounter-1)*options.pageSize) {
  					$(this).addClass("simplePagerPage"+pageCounter);
  				}
  				else {
  					$(this).addClass("simplePagerPage"+(pageCounter+1));
  					pageCounter ++;
  				}	
  
  			});
      }

			// show/hide the appropriate regions 
			selector.children().hide();
			selector.children(".simplePagerPage"+options.currentPage).show();

			if(pageCounter <= 1) {
				return;
			}

			countPages = pageCounter;
			var nextPageNumber = options.currentPage + 1;
			var pageNav = "<ul class='simplePagerNav'>";	
			//Build pager navigation
			pageNav += "<li id='prevNav' class='nextback' ><a rel='0' href='#'>" + options.prevText + "</a></li>";
			for (i=1;i<=pageCounter;i++){
				if (i==options.currentPage) {
					pageNav += "<li class='currentPage simplePageNav"+i+" search-page-number'><a rel='"+i+"' href='#'>"+i+"</a></li>";
				}
				else {
					if(i==2)
						pageNav +="<li class='simplePageNavNext' style='display: none;'>...</li>";			
					if(i==pageCounter-1)
						pageNav +="<li class='simplePageNavPrev' style='display: none;'>...</li>";

					pageNav += "<li class='simplePageNav"+i+" search-page-number'><a rel='"+i+"' href='#'>"+i+"</a></li>";
				}
			}
			pageNav += "<li id='nextNav' class='nextback'><a rel='" + nextPageNumber + "' href='#'>" + options.nextText + "</a></li>";
			pageNav += "</ul>";


			if(!options.holder) {
				switch(options.pagerLocation)
				{
				case "before":
					selector.before(pageNav);
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

			$(selector).buildPager(1);

			//pager navigation behaviour
			selector.parent().find(".simplePagerNav a").click(function (){

				//grab the REL attribute 
				var clickedLink = $(this).attr("rel");
				options.currentPage = clickedLink;

				if (options.usePageNumbers === true) {
					if (options.holder) {
						$(this).parent("li").parent("ul").parent(options.holder).find("li.currentPage").removeClass("currentPage");
						$(this).parent("li").parent("ul").parent(options.holder).find("a[rel='" + clickedLink + "']").parent("li:not(li.nextback)").addClass("currentPage");
					}
					else {
						//remove current current (!) page
						$(this).parent("li").parent("ul").parent(".simplePagerContainer").find("li.currentPage").removeClass("currentPage");
						//Add current page highlighting
						$(this).parent("li").parent("ul").parent(".simplePagerContainer").find("a[rel='" + clickedLink + "']").parent("li").addClass("currentPage");
					}
				}

				if (options.usePrevNext === true) {
					var $prevNav = $('#prevNav');
					var $nextNav = $('#nextNav');
					$prevNav.find('a').attr('rel', parseInt(clickedLink, 10) - 1); // Uses radix to indicate that this is a decimal value
					$nextNav.find('a').attr('rel', parseInt(clickedLink, 10) + 1);
					if (clickedLink == "1") {
						$prevNav.hide();
					}
					else {
						$prevNav.show();
					}
					if (clickedLink == pageCounter) {
						$nextNav.hide();
					}
					else {
						$nextNav.show();
					}
				}


				$(selector).selectPage($(this).attr("rel"));

				//hide and show relevant links
				selector.children().hide();
				selector.find(".simplePagerPage" + clickedLink).show();

				return false;

			});
		});
	}


})(jQuery);

