#!/bin/bash

######################################
##
## Build WP2Static Copy to Folder Addon
##
## script archive_name dont_minify
##
## places archive in $HOME/Downloads
##
######################################

# run script from project root
EXEC_DIR="$(pwd)"
PKG_NAME="wp2static-addon-copy"

TMP_DIR="$HOME/plugintmp"
rm -Rf "$TMP_DIR"
mkdir -p "$TMP_DIR"

rm -Rf "$TMP_DIR/$PKG_NAME"
mkdir "$TMP_DIR/$PKG_NAME"

# clear dev dependencies
rm -Rf "$EXEC_DIR/vendor/*"
# load prod deps and optimize loader
composer update
composer install --quiet --no-dev --optimize-autoloader

# cp all required sources to build dir
cp -r "$EXEC_DIR"/*.php "$TMP_DIR/$PKG_NAME/"
cp -r "$EXEC_DIR/src" "$TMP_DIR/$PKG_NAME/"
cp -r "$EXEC_DIR/vendor" $TMP_DIR/$PKG_NAME/
cp -r "$EXEC_DIR/views" "$TMP_DIR/$PKG_NAME/"

cd "$TMP_DIR"

# tidy permissions
find . -type d -exec chmod 755 {} \;
find . -type f -exec chmod 644 {} \;

zip --quiet -r -9 "./$PKG_NAME.zip" "./$PKG_NAME"

cd -

mkdir -p "$EXEC_DIR/release/"

cp "$TMP_DIR/$PKG_NAME.zip" "$EXEC_DIR/release/"

# reset dev dependencies
cd "$EXEC_DIR"
# clear dev dependencies
rm -Rf "$EXEC_DIR/vendor/*"
# load prod deps
composer install --quiet
