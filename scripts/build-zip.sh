#!/usr/bin/env bash
#
# Build production release ZIP: builds/blocksy-child-{version}.zip
#
set -euo pipefail

readonly THEME_SLUG="blocksy-child"
readonly REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
readonly VERIFY_ZIP="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/lib/verify-release-zip.py"
readonly STYLE_FILE="${REPO_ROOT}/style.css"
readonly FUNCTIONS_FILE="${REPO_ROOT}/functions.php"
readonly OUT_DIR="${REPO_ROOT}/builds"
readonly STAGE_ROOT="$(mktemp -d "${TMPDIR:-/tmp}/bbc-zip.XXXXXX")"

cleanup() {
	rm -rf "${STAGE_ROOT}"
}
trap cleanup EXIT

[[ -f "${STYLE_FILE}" ]] || {
	echo "ERROR: Missing ${STYLE_FILE}" >&2
	exit 1
}
[[ -f "${FUNCTIONS_FILE}" ]] || {
	echo "ERROR: Missing ${FUNCTIONS_FILE}" >&2
	exit 1
}

HEADER_VERSION="$(
	grep -E '^\s*Version:\s*' "${STYLE_FILE}" \
		| head -n 1 \
		| sed -E 's/^[[:space:]]*Version:[[:space:]]*//'
)"

VERSION_CONST="$(
	grep -E "define\s*\(\s*'BLOCKSY_CHILD_VERSION'" "${FUNCTIONS_FILE}" \
		| head -n 1 \
		| sed -E "s/.*'([^']+)'.*/\1/"
)"

[[ "${HEADER_VERSION}" == "${VERSION_CONST}" ]] || {
	echo "ERROR: Version mismatch style.css=${HEADER_VERSION} functions.php=${VERSION_CONST}" >&2
	exit 1
}

readonly VERSION="${VERSION_CONST}"
readonly ZIP_PATH="${OUT_DIR}/${THEME_SLUG}-${VERSION}.zip"
readonly PACKAGE_DIR="${STAGE_ROOT}/${THEME_SLUG}"

echo "==> Biopentra Blocksy Child: build production ZIP"
echo "    Version: ${VERSION}"
echo "    Output:  ${ZIP_PATH}"

mkdir -p "${OUT_DIR}" "${PACKAGE_DIR}"

tar -C "${REPO_ROOT}" \
	--exclude='.git' \
	--exclude='.github' \
	--exclude='vendor' \
	--exclude='node_modules' \
	--exclude='scripts' \
	--exclude='tests' \
	--exclude='docs' \
	--exclude='build' \
	--exclude='builds' \
	--exclude='.phpcs-cache' \
	--exclude='.phpunit.result.cache' \
	--exclude='.env' \
	--exclude='.env.*' \
	--exclude='*.log' \
	--exclude='*.sql' \
	--exclude='*.sql.gz' \
	--exclude='*.dump' \
	--exclude='*.sqlite' \
	--exclude='.write-test' \
	--exclude='.DS_Store' \
	--exclude='README.md' \
	--exclude='THEME-README.md' \
	--exclude='CHANGELOG.md' \
	--exclude='LICENSE' \
	--exclude='.gitignore' \
	-cf - . \
	| tar -C "${PACKAGE_DIR}" -xf -

rm -f "${ZIP_PATH}"
if command -v zip >/dev/null 2>&1; then
	( cd "${STAGE_ROOT}" && zip -qr "${ZIP_PATH}" "${THEME_SLUG}" )
else
	python3 - "${STAGE_ROOT}" "${ZIP_PATH}" "${THEME_SLUG}" <<'PY'
import os
import sys
import zipfile
from pathlib import Path

staging_dir = Path(sys.argv[1])
zip_path = Path(sys.argv[2])
theme_slug = sys.argv[3]
root = staging_dir / theme_slug

with zipfile.ZipFile(zip_path, "w", compression=zipfile.ZIP_DEFLATED) as zf:
    for dirpath, _dirnames, filenames in os.walk(root):
        for name in filenames:
            full = Path(dirpath) / name
            zf.write(full, full.relative_to(staging_dir).as_posix())
PY
fi

python3 "${VERIFY_ZIP}" "${ZIP_PATH}" "${VERSION}"
echo "==> Build complete: ${ZIP_PATH}"
