[![Build status — Travis-CI][travis-icon]][travis]


CloudEngine
===========

CloudEngine is an easy way to create a social web site for discussing
and aggregating ideas and resources. It is ideal for running events
and fostering discussion. It is the free, open source software
that powers [Cloudworks][].

* <http://cloudworks.ac.uk>

## Installation

* [Release notes][releases] ([legacy releases][release-old])

* [Installation guide][install] ([legacy guide][wiki-old])

Requires php-gd, php-curl, php-mbstring, php-mysql (or other DB driver)

## Build and test

```sh
composer install
composer npm-install
composer test
```

## GDPR

Details of GDPR / privacy fixes can be found in [Bug #377][].

## License

[CloudEngine][gh] is Copyright © 2009-2019, [The Open University][ou]. ([Institute of Educational Technology][iet])

* License: [GNU General Public License version 2][gpl].

* See [CREDITS.txt][] for a list of the third-party libraries incorporated
  in CloudEngine, and their authors and licenses.


[gh]: https://github.com/IET-OU/cloudengine
[bb]: https://bitbucket.org/cloudengine/cloudengine "Legacy code"
[install]: https://github.com/IET-OU/cloudengine/wiki
[releases]: https://github.com/IET-OU/cloudengine/releases
[wiki-old]: https://bitbucket.org/cloudengine/cloudengine/wiki/Install "Legacy install guide"
[release-old]: https://bitbucket.org/cloudengine/cloudengine/wiki/Releases "Legacy release notes"
[travis]: https://travis-ci.org/IET-OU/cloudengine "Build status — Travis-CI"
[travis-icon]: https://travis-ci.org/IET-OU/cloudengine.svg
[gpl]: https://gnu.org/licenses/gpl-2.0.html
[license.txt]: https://github.com/IET-OU/cloudengine/blob/master/LICENCE.txt
[credits.txt]: https://github.com/IET-OU/cloudengine/blob/master/CREDITS.txt
[cloudworks]: http://cloudworks.ac.uk/
[iet]: https://iet.open.ac.uk/ "Developed by the Institute of Educational Technology"
[ou]: https://www.open.ac.uk/

[Bug #377]: https://github.com/IET-OU/cloudengine/issues/377 "GDPR/data privacy"

[End]: //end.
