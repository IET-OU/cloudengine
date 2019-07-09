#!/usr/bin/env bash
#
# Archive / crawl / mirror / recursively-download the Cloudworks web-site (Bash script).
#
# NDF / 28-June-2019, 12:03.
#
# See: https://gist.github.com/steveosoule/79d0ba5f2cad558642aace43c7126946
# See: https://gnu.org/software/wget/manual/

# wget --no-clobber --convert-links --random-wait -r -p --level 1 -E -e robots=off --user-agent="Mozilla/5.0 (Macintosh; Intel Mac OS X 10.14; rv:66.0) Gecko/20100101 Firefox/66.0" https://cloudworks.ac.uk/

wget \
  --level=2 \
 	--mirror \
 	--recursive \
 	--execute robots=off \
	--user-agent='wget/cloudworks-archiver' \
 	--timestamping \
 	--page-requisites \
 	--html-extension \
	--convert-links \
 	--restrict-file-names=windows \
  --reject-regex '(auth|add|rss)' \
 	--domains cloudworks.ac.uk \
	--output-file=cloudworks.ac.uk-wget-2019-07-02--do-not-delete.log \
	--progress=dot \
	--show-progress \
  --input-file=/ABSOLUTE/PATH/TO/cloudworks-do-not-delete-users.txt
	# --input-file=/ABSOLUTE/PATH/TO/cloudworks-url-list.txt

# 	--wait=1 \
# 	--random-wait \
#  	--mirror \  # shortcut for -N -r -l inf --no-remove-listing.
#  	--timestamping \
#   --no-clobber \
#	--debug \
#  --directory-prefix=sample \

# tail ~/Downloads/cloudworks--28-jun-2019/cloudworks.ac.uk-wget-2019-06-28--p2.log

# End.
