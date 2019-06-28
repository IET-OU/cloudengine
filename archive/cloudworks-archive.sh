#
# Archive / crawl / mirror / spider / recursively-download the Cloudworks web-site (Bash script).
#
# NDF / 28-June-2019, 12:03.
#
# See: https://gist.github.com/steveosoule/79d0ba5f2cad558642aace43c7126946

# wget --no-clobber --convert-links --random-wait -r -p --level 1 -E -e robots=off --user-agent="Mozilla/5.0 (Macintosh; Intel Mac OS X 10.14; rv:66.0) Gecko/20100101 Firefox/66.0" https://cloudworks.ac.uk/

wget \
  --level=10 \
 	--mirror \
 	--recursive \
 	--execute robots=off \
 	--user-agent='Mozilla/5.0 (Macintosh; Intel Mac OS X 10.14; rv:66.0) Gecko/20100101 Firefox/66.0' \
 	--timestamping \
 	--page-requisites \
 	--html-extension \
 	--restrict-file-names=windows \
 	--wait=1 \
 	--random-wait \
 	--domains cloudworks.ac.uk \
	--debug \
	--output-file=cloudworks.ac.uk-wget-2019-06-28--p2.log \
	--progress=dot \
	--show-progress \
	--no-clobber \
	--input-file=cloudworks-url-list.txt \
		https://cloudworks.ac.uk/

#  --directory-prefix=sample \

# End.
