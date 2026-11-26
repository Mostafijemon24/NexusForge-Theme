#!/bin/bash
# Publishes FusionWrite AI to wordpress.org from the SVN folder on this computer.
# Run it from the unpacked release package:  bash release.sh
# It prepares everything, shows what will change, and asks before publishing.
set -euo pipefail

VERSION="2.0.2"
USERNAME="mostafijemon"
HERE="$(cd "$(dirname "$0")" && pwd)"
SVN_DIR="${1:-$HOME/Desktop/fusionwrite-svn}"

say()  { printf '\n== %s\n' "$1"; }
fail() { printf '\nSTOPPED: %s\nNothing was published.\n' "$1"; exit 1; }

[ -d "$SVN_DIR/.svn" ] || fail "$SVN_DIR is not the wordpress.org SVN folder."
[ -f "$HERE/fusionwrite-ai/fusionwrite-ai.php" ] || fail "The plugin files are missing next to this script."
[ -f "$HERE/wporg-assets/icon-256x256.png" ] || fail "The icon and banner files are missing next to this script."
cd "$SVN_DIR"

say "Getting the latest state from wordpress.org"
svn update

if [ -e "tags/$VERSION" ]; then
	if svn info "tags/$VERSION" 2>/dev/null | grep -q '^Schedule: add'; then
		# Left over from an earlier run that was stopped before publishing.
		svn revert -R -q "tags/$VERSION"
		rm -rf "tags/$VERSION"
	else
		fail "Version $VERSION is already on wordpress.org."
	fi
fi

say "Copying plugin $VERSION into trunk"
cp -R "$HERE/fusionwrite-ai/." trunk/

# Remove files that are no longer part of the plugin.
(cd trunk && find . -type f -not -name .DS_Store | sort) > /tmp/fswrt-trunk.txt
(cd "$HERE/fusionwrite-ai" && find . -type f -not -name .DS_Store | sort) > /tmp/fswrt-new.txt
comm -23 /tmp/fswrt-trunk.txt /tmp/fswrt-new.txt | while IFS= read -r old; do
	svn rm -q --force "trunk/$old"
done

say "Copying the icon, banner and screenshots into assets"
cp "$HERE"/wporg-assets/*.png assets/

find trunk assets -name .DS_Store -delete
svn add -q --force trunk assets
svn propset -q svn:mime-type image/png assets/*.png

say "Creating the $VERSION release tag"
svn cp -q trunk "tags/$VERSION"

say "Checking"
grep -q "^Stable tag: $VERSION\$" trunk/readme.txt || fail "trunk/readme.txt does not say Stable tag: $VERSION."
grep -q "^Tested up to: 7.2\$" trunk/readme.txt || fail "trunk/readme.txt does not say Tested up to: 7.2."
grep -q "Version: $VERSION\$" trunk/fusionwrite-ai.php || fail "The plugin file does not say Version: $VERSION."
grep -q "^Stable tag: $VERSION\$" "tags/$VERSION/readme.txt" || fail "The release tag was not created correctly."
if svn status | grep -E '^(\?|!|C|~)'; then
	fail "SVN reports the files above as unexpected. Send this output to get it sorted."
fi
echo "Version $VERSION, Stable tag $VERSION, Tested up to 7.2: all correct."
echo "Files that will change on wordpress.org: $(svn status | wc -l | tr -d ' ')"

printf '\nReady to publish FusionWrite AI %s to wordpress.org.\nType yes and press Enter to publish, or anything else to stop: ' "$VERSION"
read -r answer
if [ "$answer" != "yes" ]; then
	echo "Stopped. Nothing was published. Run the script again whenever you are ready."
	exit 0
fi

svn commit -m "Release $VERSION: new icon, banner, logo and screenshots, tour fix, tested up to 7.2" --username "$USERNAME"

say "Published. wordpress.org will show version $VERSION within a few minutes."
