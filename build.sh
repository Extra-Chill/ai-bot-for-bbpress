#!/bin/bash

# Standardized build script for ai-bot-for-bbpress
# Creates optimized .zip for production use with all production dependencies bundled

set -e

PLUGIN_NAME="ai-bot-for-bbpress"
BUILD_DIR="build"
TEMP_DIR="$BUILD_DIR/temp"

# Create build directory
mkdir -p "$BUILD_DIR"

# Create temp directory for build
rm -rf "$TEMP_DIR"
mkdir -p "$TEMP_DIR"

# Copy plugin files, excluding development files
rsync -av --exclude='vendor/' --exclude='node_modules/' --exclude='.git/' --exclude='docs/' --exclude="$BUILD_DIR/" --exclude='composer.lock' --exclude='package-lock.json' --exclude='.DS_Store' --exclude='.claude/' --exclude='README.md' --exclude='.buildignore' --exclude='build.sh' --exclude='AGENTS.md' . "$TEMP_DIR/"

# Install production dependencies only
cd "$TEMP_DIR"
composer install --no-dev --optimize-autoloader

# Create zip file
cd ..
zip -r "$PLUGIN_NAME.zip" temp/

# Cleanup
rm -rf "$TEMP_DIR"

echo "Production build created: $BUILD_DIR/$PLUGIN_NAME.zip"