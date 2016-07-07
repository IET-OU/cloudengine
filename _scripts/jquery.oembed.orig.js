(function($) {
    $.fn.oembed = function(url, options) {

		options = $.extend({}, $.fn.oembed.defaults, options);

        return this.each(function() {

			var container = $(this),
				target = (url !== null) ? url : container.attr("href"), 
				provider;

			if (target !== null) {

				provider = getOEmbedProvider(target);

				if (provider !== null) {
					provider.maxWidth = options.maxWidth;
					provider.maxHeight = options.maxHeight;

					provider.embedCode(target, function(code) { container.html(code); });
				}
			}
        });
    };

    // Plugin defaults
    $.fn.oembed.defaults = {
        maxWidth: 500,
        maxHeight: 400
    };

    $.fn.oembed.getPhotoCode = function(url, data) {
        var alt = data.title ? data.title : 'Photo';
        alt += data.author_name ? ', by '+data.author_name :'';
        alt += data.provider_name ? ', on '+data.provider_name :'';
        var code= '<a class="photo" href="'+ url +'"><img src="'+ data.url +'" alt="'+ alt +'"/></a>';
        if (data.html)
            code += "<div>" + data.html + "</div>";
        return code;
    };

    $.fn.oembed.getVideoCode = function(url, data) {
        var code = data.html;
        return code;
    };

    $.fn.oembed.getRichCode = function(url, data) {
        var code = data.html;
        return code;
    };

    $.fn.oembed.getGenericCode = function(url, data) {
        var title = (data.title !== null) ? data.title : url,
			code = '<a href="' + url + '">' + title + '</a>';
        if (data.html)
            code += "<div>" + data.html + "</div>";
		return code;
    };

    $.fn.oembed.isAvailable = function(url) {
        var provider = getOEmbedProvider(url);
        return (provider !== null);
    };

    /* Private Methods */
    function getOEmbedProvider(url) {
        for (var i = 0; i < providers.length; i++) {
            if (providers[i].matches(url))
                return providers[i];
        }
        return null;
    }

    var providers = [
        new OEmbedProvider("fivemin", "5min.com"),
        new OEmbedProvider("amazon", "amazon.com"),
        new OEmbedProvider("flickr", "flickr", "http://flickr.com/services/oembed", "jsoncallback"),
        new OEmbedProvider("googlevideo", "video.google."),
        new OEmbedProvider("hulu", "hulu.com"),
        new OEmbedProvider("imdb", "imdb.com"),
        new OEmbedProvider("metacafe", "metacafe.com"),
        new OEmbedProvider("qik", "qik.com"),
        new OEmbedProvider("revision3", "revision3.com"),
        new OEmbedProvider("slideshare", "slideshare.net"),
        new OEmbedProvider("twitpic", "twitpic.com"),
        new OEmbedProvider("viddler", "viddler.com"),
        new OEmbedProvider("vimeo", "vimeo.com", "http://vimeo.com/api/oembed.json"),
        new OEmbedProvider("wikipedia", "wikipedia.org",  "http://oohembed.com/oohembed/"),
        new OEmbedProvider("wordpress", "wordpress.com"),
//ou-specific
        new OEmbedProvider("NFB", "nfb.ca"),
        new OEmbedProvider("blip","blip.tv"),
        new OEmbedProvider("last.fm", "last.fm"),
        new OEmbedProvider("dotSUB", "dotsub.com"),
        new OEmbedProvider("twitter", "twitter.com"),
        new OEmbedProvider("scribd",  "scribd.com"),

        new OEmbedProvider("maltwiki", "mathtran.org", "http://olnet.org/oembed"),
        new OEmbedProvider("maltwiki", "cohere.open.ac.uk", "http://olnet.org/oembed"),
        new OEmbedProvider("maltwiki", "youtube.com", "http://maltwiki.org/oembed"),
        //new OEmbedProvider("youtube", "youtube.com"),
//ou-specific ends.
        new OEmbedProvider("vids.myspace.com", "vids.myspace.com", "http://vids.myspace.com/index.cfm?fuseaction=oembed"),
		new OEmbedProvider("screenr", "screenr.com", "http://screenr.com/api/oembed.json")
    ];

    function OEmbedProvider(name, urlPattern, oEmbedUrl, callbackparameter) {
        this.name = name;
        this.urlPattern = urlPattern;
//ou-specific -- http://code.google.com/p/oohembed/issues/detail?id=14
        this.oEmbedUrl = (oEmbedUrl !== null) ? oEmbedUrl :
        //"http://oohembed.com/oohembed/";
        "http://api.embed.ly/v1/api/oembed";
//ou-specific ends.
        this.callbackparameter = (callbackparameter !== null) ? callbackparameter : "callback";
        this.maxWidth = 500;
        this.maxHeight = 400;

        this.matches = function(externalUrl) {
            // TODO: Convert to Regex
            return externalUrl.indexOf(this.urlPattern) >= 0;
        };

        this.getRequestUrl = function(externalUrl) {

            var url = this.oEmbedUrl;

            if (url.indexOf("?") <= 0)
                url = url + "?";

            url += "maxwidth=" + this.maxWidth +

//ou-specific
                "&maxheight=" + this.maxHeight +
                //#todo Bug "&maxHeight=" + this.maxHeight +

                "&client=" + "org.maltwiki:jquery.oembed.js" +
                "&ref=" + escape(location) +
//ou-specific ends.

						"&format=json" +
						"&url=" + escape(externalUrl) +
						"&" + this.callbackparameter + "=?";
            return url;
        }

        this.embedCode = function(externalUrl, embedCallback) {

            var request = this.getRequestUrl(externalUrl);

            $.getJSON(request, function(data) {

				var code, type = data.type;

                switch (type) {
                    case "photo":
                        code = $.fn.oembed.getPhotoCode(externalUrl, data);
                        break;
                    case "video":
                        code = $.fn.oembed.getVideoCode(externalUrl, data);
                        break;
                    case "rich":
                        code = $.fn.oembed.getRichCode(externalUrl, data);
                        break;
                    default:
                        code = $.fn.oembed.getGenericCode(externalUrl, data);
                        break;
                }

                embedCallback(code);
            });
        }
    }
})(jQuery);
