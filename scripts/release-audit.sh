#!/usr/bin/env bash
#
# Release validation for biopentra-blocksy-child standalone repo.
#
set -euo pipefail

REPO_ROOT="$(cd "$(dirname "$0")/.." && pwd)"
VERIFY_ZIP="${REPO_ROOT}/scripts/lib/verify-release-zip.py"
STYLE_FILE="${REPO_ROOT}/style.css"
FUNCTIONS_FILE="${REPO_ROOT}/functions.php"

fail() {
	echo "ERROR: $*" >&2
	exit 1
}

echo "==> Biopentra Blocksy Child: release audit"

[[ -f "${STYLE_FILE}" ]] || fail "Missing style.css"
[[ -f "${FUNCTIONS_FILE}" ]] || fail "Missing functions.php"

VERSION_CONST="$(grep -E "define\s*\(\s*'BLOCKSY_CHILD_VERSION'" "${FUNCTIONS_FILE}" | head -n1 | sed -E "s/.*'([^']+)'.*/\1/")"
HEADER_VERSION="$(grep -E '^\s*Version:\s*' "${STYLE_FILE}" | head -n1 | sed -E 's/^[[:space:]]*Version:[[:space:]]*//')"
[[ "${VERSION_CONST}" == "${HEADER_VERSION}" ]] || fail "Version mismatch"
echo "    Version: ${VERSION_CONST}"

[[ -f "${REPO_ROOT}/docs/GITHUB_RELEASE_NOTES_${VERSION_CONST}.md" ]] || fail "Missing release notes"
[[ -f "${REPO_ROOT}/.github/workflows/release.yml" ]] || fail "Missing release workflow"

ZIP_PATH="${REPO_ROOT}/builds/blocksy-child-${VERSION_CONST}.zip"
if [[ ! -f "${ZIP_PATH}" ]]; then
	bash "${REPO_ROOT}/scripts/build-zip.sh"
fi

python3 "${VERIFY_ZIP}" "${ZIP_PATH}" "${VERSION_CONST}"
echo "==> Release audit passed (${VERSION_CONST})"
