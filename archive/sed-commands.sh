
# DEPRECATED! Use: "cloudworks-clean.php"

# Cloudworks archive #

## Javascript ##

$# find . -type f -path '*/*.html' -exec sed -i .BAK 's/https:\/\/cloudworks.ac.uk\/_scr/\/_scr/g' {} +
$# find . -type f -path '*/*.html' -exec sed -i .BAK 's/custom.js"><\/script>/custom.js"><\/script><script src="\/_scripts\/archive-fix.js"><\/script><!--A-->/g' {} +

## CSS, logos etc. ##

$# find . -type f -path '*/*.html' -exec sed -i .BAK 's/https:\/\/cloudworks.ac.uk\/+_des/\/_des/g' {} +
$# find . -type f -path '*/*.html' -exec sed -i .BAK 's/https:\/\/cloudworks.ac.uk\/them/\/them/g' {} +

# ---

## 'User' directory ##

$# find ./user -type f -path '*/*.html' -exec sed -i .BAK 's/https:\/\/cloudworks.ac.uk\/_scr/\/_scr/g' {} +
$# find ./user -type f -path '*/*.html' -exec sed -i .BAK 's/custom.js"><\/script>/custom.js"><\/script><script src="\/_scripts\/archive-fix.js"><\/script><!--A-->/g' {} +

$# find ./user -type f -path '*/*.html' -exec sed -i .BAK 's/https:\/\/cloudworks.ac.uk\/_des/\/_des/g' {} +
$# find ./user -type f -path '*/*.html' -exec sed -i .BAK 's/https:\/\/cloudworks.ac.uk\/\/_des/\/_des/g' {} +
$# find ./user -type f -path '*/*.html' -exec sed -i .BAK 's/https:\/\/cloudworks.ac.uk\/them/\/them/g' {} +

$# find ./user -type f -path '*/*.html' -exec sed -i .BAK "s/<script> console.warn('rel=nofollow count:', 0) <\/script>/\<\!--RNF-->/g" {} +

$# find ./user/user_test.html -type f -path '*/*.html' -exec sed -i .BAK 's/<script src="https:\/\/unp.+?gaad-widget.min.js"\n\s+.+\}.+?><\/script>/\<\!-- GAAD -->/g' {} +

### WOOPS !!

$# find ./user -type f -path '*/*.html' -exec sed -i .BAK 's/archive-fix.js"><\/script><script src="\/_scripts\/archive-fix.js"><\/script>/archive-fix.js"><\/script><!--A-->/g' {} +

# ---
