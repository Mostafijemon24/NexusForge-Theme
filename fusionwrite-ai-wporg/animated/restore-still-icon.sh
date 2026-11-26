#!/bin/bash
# Puts the still PNG icon back on wordpress.org and removes the animated one.
# Use it if the animated icon does not show up:  bash restore-still-icon.sh
set -euo pipefail

USERNAME="mostafijemon"
HERE="$(cd "$(dirname "$0")" && pwd)"
SVN_DIR="${1:-$HOME/Desktop/fusionwrite-svn}"

fail() { printf '\nSTOPPED: %s\nNothing was published.\n' "$1"; exit 1; }

[ -d "$SVN_DIR/.svn" ] || fail "$SVN_DIR is not the wordpress.org SVN folder."
[ -f "$HERE/still/icon-256x256.png" ] || fail "The PNG icons are missing next to this script."
cd "$SVN_DIR"

svn update
cp "$HERE/still/icon-128x128.png" "$HERE/still/icon-256x256.png" assets/
svn add -q --force assets
svn propset -q svn:mime-type image/png assets/icon-128x128.png assets/icon-256x256.png
for moving in assets/icon-128x128.gif assets/icon-256x256.gif; do
	if [ -e "$moving" ]; then
		svn rm -q --force "$moving"
	fi
done
svn status
svn commit -m "Back to the still plugin icon" --username "$USERNAME"
printf '\n== Published. The still icon is back.\n'
