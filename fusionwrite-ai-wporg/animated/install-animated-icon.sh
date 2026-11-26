#!/bin/bash
# Replaces the still plugin icon on wordpress.org with the animated GIF icon.
# Run it after the plugin release is published:  bash install-animated-icon.sh
set -euo pipefail

USERNAME="mostafijemon"
HERE="$(cd "$(dirname "$0")" && pwd)"
SVN_DIR="${1:-$HOME/Desktop/fusionwrite-svn}"

fail() { printf '\nSTOPPED: %s\nNothing was published.\n' "$1"; exit 1; }

[ -d "$SVN_DIR/.svn" ] || fail "$SVN_DIR is not the wordpress.org SVN folder."
[ -f "$HERE/icon-256x256.gif" ] || fail "The GIF icons are missing next to this script."
cd "$SVN_DIR"

printf '\n== Getting the latest state from wordpress.org\n'
svn update
if [ -n "$(svn status)" ]; then
	svn status
	fail "The SVN folder has changes that are not published yet. Finish the plugin release first, then run this again."
fi

printf '\n== Putting the animated icon in place\n'
cp "$HERE/icon-128x128.gif" "$HERE/icon-256x256.gif" assets/
svn add -q --force assets
svn propset -q svn:mime-type image/gif assets/icon-128x128.gif assets/icon-256x256.gif
for still in assets/icon-128x128.png assets/icon-256x256.png; do
	if [ -e "$still" ]; then
		svn rm -q "$still"
	fi
done
svn status

printf '\nReady to switch the wordpress.org icon to the animated GIF.\nType yes and press Enter to publish, or anything else to stop: '
read -r answer
if [ "$answer" != "yes" ]; then
	svn revert -R -q assets
	rm -f assets/icon-128x128.gif assets/icon-256x256.gif
	echo "Stopped. Nothing was published and the folder was put back as it was."
	exit 0
fi

svn commit -m "Animated plugin icon" --username "$USERNAME"
printf '\n== Published. The new icon can take a while to appear because wordpress.org caches images.\n'
