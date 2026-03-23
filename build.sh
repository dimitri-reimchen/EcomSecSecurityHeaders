#!/bin/bash
# ============================================================
# Build script for EcomSec Security Headers Plugin
# Creates a store-ready ZIP package
# ============================================================

set -e

PLUGIN_NAME="EcomSecSecurityHeaders"
VERSION=$(php -r "echo json_decode(file_get_contents('composer.json'))->version;")
BUILD_DIR="build/tmp"
DIST_DIR="dist"

echo "================================================"
echo "Building ${PLUGIN_NAME} v${VERSION}"
echo "================================================"

# Clean up previous builds
rm -rf ${BUILD_DIR}
mkdir -p ${BUILD_DIR}/${PLUGIN_NAME}
mkdir -p ${DIST_DIR}

# Copy plugin files (excluding dev files)
echo "Copying plugin files..."
rsync -a \
    --exclude='.git' \
    --exclude='.github' \
    --exclude='build' \
    --exclude='dist' \
    --exclude='tests' \
    --exclude='phpunit.xml' \
    --exclude='phpunit.xml.dist' \
    --exclude='.phpunit.result.cache' \
    --exclude='phpstan.neon' \
    --exclude='phpstan.neon.dist' \
    --exclude='*.log' \
    --exclude='var' \
    --exclude='build.sh' \
    --exclude='.env' \
    --exclude='.env.local' \
    --exclude='*.md' \
    --exclude='LICENSE' \
    ./ ${BUILD_DIR}/${PLUGIN_NAME}/

# Copy LICENSE separately (needed for store)
cp LICENSE ${BUILD_DIR}/${PLUGIN_NAME}/

# Create ZIP file
ZIP_NAME="${PLUGIN_NAME}-${VERSION}.zip"
echo ""
echo "Creating ZIP file: ${ZIP_NAME}..."
cd ${BUILD_DIR}
zip -r ../../${DIST_DIR}/${ZIP_NAME} ${PLUGIN_NAME} -q

# Go back to root
cd ../..

# Show result
echo ""
echo "================================================"
echo "✅ Build successful!"
echo "================================================"
echo "Package: ${DIST_DIR}/${ZIP_NAME}"
echo "Size: $(du -h ${DIST_DIR}/${ZIP_NAME} | cut -f1)"
echo ""
echo "ZIP contents:"
unzip -l ${DIST_DIR}/${ZIP_NAME} | head -30
echo "..."
echo "Total files: $(unzip -l ${DIST_DIR}/${ZIP_NAME} | tail -1 | awk '{print $2}')"
echo "================================================"
