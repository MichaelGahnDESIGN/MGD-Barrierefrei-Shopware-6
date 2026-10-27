#!/usr/bin/env sh
set -eu

project=$(pwd)
# Storefront-dist zuerst frisch bauen, sonst fehlt der Bedienpanel-Code im Shop
sh scripts/build-storefront.sh >/dev/null
version=$(php -r '$data=json_decode((string) file_get_contents("composer.json"), true, 32, JSON_THROW_ON_ERROR); echo $data["version"];')
case "$version" in [0-9]*.[0-9]*.[0-9]*) ;; *) echo "Ungültige Version: $version" >&2; exit 1;; esac
root="MgdBarrierefrei"
stage=$(mktemp -d)
output="release/${root}-${version}.zip"
mkdir -p release "$stage/$root"
cp composer.json LICENSE README.md SECURITY.md CHANGELOG.md "$stage/$root/"
cp -R src docs "$stage/$root/"
(cd "$stage" && zip -qr "$project/$output" "$root" -x '*/.DS_Store')
# dist muss im ZIP liegen
unzip -l "$output" | grep -q "storefront/dist/storefront/js/mgd-barrierefrei/mgd-barrierefrei.js" || { echo "dist fehlt im ZIP" >&2; exit 1; }
shasum -a 256 "$output" > "${output}.sha256"
echo "Paket erstellt: $output"
