/**
 * Cloudworks.Streams
 * Renders the HTML for Cloudstream/ tag widgets.
 *
 * @package    api
 */
Cloudworks.Streams = function() {
    return {

        //! Default options used for render()
        defaults: {
            'BASE_URL':   'http://cloudworks.ac.uk/',
            'title':      'My Cloudstream',
            'count':      10, //15,
            'sort':       'date',
            'logo_shown': true, //false
			'bullet':     false,
            'icon':       'cs',

            EOF:null
        },

        /**
         *
         */
        writeln: function(o, posts) {
            //if (typeof posts != )
            document.write(this.render(o, posts));
        },

        /**
         * Given an array of options and a set of posts, build the HTML for the
         * linkroll.  The available options are:
         *
         *     sort: 'date', 'alpha'
         *     style: 'none'
         *     user: 'username'
         *     usertags: false, 'foo+bar'
         *     title: false, 'my cloudworks bookmarks'
         *     bullet: '&raquo;'
         *     icon: false, 'm', 's', 'rss'
         *     name: true, false
         *     showadd: true, false
         *     write: function()
         */
        render: function(o, posts) {

            var out = [];
            w = function (s) { out.push(s); }

            if (o.icon === true) o.icon = 'm';

            // Apply default options to incoming options.
            for (k in this.defaults)
                if (typeof o[k] == 'undefined')
                    o[k] = this.defaults[k];
                
            // Do some HTML escaping to avoid formatting probs and XSS
            o.title_h    = this.htmlEscape(o.title);
            o.user_h     = this.htmlEscape(o.user);
            o.user_q     = encodeURIComponent(o.user);
            o.usertags_q = encodeURIComponent(o.usertags);

            // Sort the posts alphabetically if necessary.
            if (o.sort == 'alpha') {
                posts.sort(function(a,b) {
                    var ad = (''+a.d).toLowerCase();
                    var bd = (''+b.d).toLowerCase();
                    return ( (ad > bd) ? 1 : ( (ad < bd) ? -1 : 0) );
                });
            } else {
                posts.sort(function(b,a) {
                    return ( (a.dt > b.dt) ? 1 : (a.dt < b.dt) ? -1 : 0 );
                });
            }

            // Include the blob of CSS if not disabled.
            if (o.style != 'none') {
                w('<style type="text/css"> .cloudworks-posts ul {list-style-type:none; margin:0 0 1em 0; padding:0} .__cloudworks-posts [rel=author],.cloudworks-end {font-size:smaller} .cw-item-link{background:url('+o.BASE_URL+'_design/icon-link.gif)top left no-repeat;padding-left:20px} .cw-item-cloud{background:url('+o.BASE_URL+'_design/icon-cloud.gif)top left no-repeat;padding-left:20px}</style>');
            }
            //id="cloudworks-posts-'+o.user+'"
            w('\n<div class="cloudworks-posts cw-item-'+o.item_type+' cw-id-'+o.item_id+' cw-rel-'+o.related+'" id="cloudworks-posts-'+o.related+'">');

            // If the plain logo will be used somewhere in the main body of the
            // linkroll, generate it.
            if (o.icon && (o.title || !(o.name || o.showadd))) {
                o.logo = this.getIcon(o, 'logo', o.icon, 'cloudworks',
                    (o.icon != 'rss') ? '' : 'rss/'+o.user+(o.usertags?'/'+o.usertags_q:'')
                );
            }

            // Build the title if necessary.  The icon appears before the
            // title, unless it's the RSS icon.
            if (o.title) {
                w('<h2 class="cloudworks-banner sidebar-title">');
                if (o.icon && o.icon != 'rss') { w(o.logo+' '); }
                w('<a href="'+o.html_url+'">'+o.title_h+'</a>');
                if (o.icon == 'rss') { w(' '+o.logo); }
                w('</h2>');
            }

            w('<ul>');

            // Iterate through all the posts and build the linkroll main body.
            //class="cloudworks-post cloudworks-even/odd "
            for(var i=0,p;( i<o.count ) && ( p=posts[i] );i++){
                w('\n<li class="'+(i%2?'even':'odd')+' cw-item-'+p.item_type+' ">');
                if (o.bullet) { w(o.bullet+'&#160;'); }
                if (p.status) {
                    w(p.status);
                } else {
                    w('<a href="'+p.html_url+'">'+p.title+'</a>');
                }
                w('</li>');
            }

            // If the logo wasn't used in the title, and there are neither name
            // nor network add options to come, then insert the logo now so it
            // appears somewhere at least.
            if (o.icon && !(o.title || o.name || o.showadd)) {
                w('\n<li class="cloudworks-endlogo">'+o.logo+'</li>');
            }
            w('\n</ul>');

            // Include the link to the user's bookmarks, if needed.
            //+o.BASE_URL+'user/view/'+o.user_id //cloudworks-network-username
            if (o.html_url) {
                w('<span class="cloudworks-end">' +
                  this.getIcon(o, 'name', o.icon, '')+
                  ' <a href="'+o.html_url+'">'+o.title_h+'</a> '+
                    'on <a href="'+o.BASE_URL+'">Cloudworks</a></span>');
            }

            //(Include the 'add me to your network' link if needed.)

            w('</div>');

            return out.join('');
        },

        //! Icons by type and size, used by getIcon()
        icons: {
            logo: {
                'cs':  [ 'icon-cloudscape.gif',17, 13 ],
                't':   [ 'icon-tag.gif',       17, 13 ],
                //'cw':  [ 'cloudworks.small.gif', 10, 10 ],
                'rss': [ 'icon-rss.gif', 16, 16 ]
            },
            name: {
                'cs':  [ 'icon-cloudscape.gif', 17, 13 ],
                't':   [ 'icon-tag.gif',        17, 13 ],
                //'cw':  [ 'cloudworks.small.gif',10, 10 ],
                'rss': [ 'icon-rss.gif', 16, 16 ]
            }
        },

        /**
         * Common method for generating linked icons based on the user-supplied
         * size crossed with the kind of icon needed.  Also ensures that the
         * del logo appears somewhere at least once, regardless of what icon
         * kind is preferred at any particular part of the markup.
         */
        getIcon: function(o, kind, size, alt) {
            if (!o.logo_shown) {
                o.logo_shown = true;
                kind = 'logo';
            }
            var ic = this.icons[kind][size];
            if (!ic) {
                return '';
            } else {
                var out = '<img src="'+o.BASE_URL+'_design/'+ic[0]+'" '+
                    'width="'+ic[1]+'" height="'+ic[2]+'" '+
                    'alt="" border="0">';
                return out;
            }
        },

        /**
         * Apply rough HTML escaping to a string.
         */
        htmlEscape: function(s) {
            return (''+s).replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
        },

        EOF: null
    };
}();
