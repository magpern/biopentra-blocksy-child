#!/usr/bin/env python3
"""Verify blocksy-child production release ZIP."""
from __future__ import annotations

import re
import sys
import zipfile

FORBIDDEN_SEGMENTS = frozenset(
    {
        ".git",
        "node_modules",
        "scripts",
        "tests",
        "docs",
        ".github",
        "build",
        "builds",
        ".phpcs-cache",
        ".phpunit.result.cache",
    }
)

FORBIDDEN_ROOT_FILES = frozenset(
    {
        "README.md",
        "THEME-README.md",
        "CHANGELOG.md",
        "LICENSE",
        ".gitignore",
        ".write-test",
    }
)

THEME_SLUG = "blocksy-child"
VERSION_CONSTANT = "BLOCKSY_CHILD_VERSION"


def segment_violations(path: str) -> list[str]:
    parts = [p for p in path.split("/") if p]
    hits: list[str] = []
    for i, part in enumerate(parts):
        if part not in FORBIDDEN_SEGMENTS:
            continue
        if part == "vendor" and i > 0:
            continue
        hits.append(part)
    if len(parts) == 1 and parts[0] in FORBIDDEN_ROOT_FILES:
        hits.append(parts[0])
    if parts:
        leaf = parts[-1]
        if leaf.startswith(".env"):
            hits.append(leaf)
        for suffix in (".log", ".sql", ".sql.gz", ".dump", ".sqlite"):
            if leaf.endswith(suffix):
                hits.append(leaf)
    return hits


def verify(zip_path: str, expected_version: str | None) -> int:
    root_prefix = f"{THEME_SLUG}/"
    style_file = f"{root_prefix}style.css"
    functions_file = f"{root_prefix}functions.php"

    with zipfile.ZipFile(zip_path) as zf:
        names = [n for n in zf.namelist() if n]
        if not names:
            print("ERROR: zip is empty", file=sys.stderr)
            return 1

        if style_file not in names:
            print(f"ERROR: missing {style_file}", file=sys.stderr)
            return 1
        if functions_file not in names:
            print(f"ERROR: missing {functions_file}", file=sys.stderr)
            return 1

        forbidden_hits: list[str] = []
        for name in names:
            rel = name[len(root_prefix):] if name.startswith(root_prefix) else name
            if not rel:
                continue
            for hit in segment_violations(rel):
                forbidden_hits.append(f"{name} ({hit})")

        if forbidden_hits:
            print("ERROR: zip contains forbidden paths:", file=sys.stderr)
            for line in forbidden_hits[:20]:
                print(f"  - {line}", file=sys.stderr)
            return 1

        try:
            style_text = zf.read(style_file).decode("utf-8", errors="replace")
            functions_text = zf.read(functions_file).decode("utf-8", errors="replace")
        except KeyError:
            print("ERROR: cannot read style.css or functions.php", file=sys.stderr)
            return 1

        header_match = re.search(
            r"^\s*Version:\s*(.+)$", style_text, re.MULTILINE | re.IGNORECASE
        )
        const_match = re.search(
            rf"define\s*\(\s*['\"]{VERSION_CONSTANT}['\"]\s*,\s*['\"]([^'\"]+)['\"]",
            functions_text,
        )
        if not header_match or not const_match:
            print("ERROR: missing Version header or version constant", file=sys.stderr)
            return 1

        header_v = header_match.group(1).strip()
        const_v = const_match.group(1).strip()
        if header_v != const_v:
            print(f"ERROR: style.css Version {header_v} != functions.php constant {const_v}", file=sys.stderr)
            return 1
        if expected_version and header_v != expected_version:
            print(
                f"ERROR: zip version {header_v} != expected {expected_version}",
                file=sys.stderr,
            )
            return 1

        template_match = re.search(
            r"^\s*Template:\s*(.+)$", style_text, re.MULTILINE | re.IGNORECASE
        )
        if not template_match or template_match.group(1).strip() != "blocksy":
            print("ERROR: style.css Template header must be 'blocksy'", file=sys.stderr)
            return 1

        print(f"OK: {len(names)} entries under {root_prefix}")
        print(f"    version: {header_v}")
        return 0


def main() -> int:
    if len(sys.argv) < 2:
        print(f"usage: {sys.argv[0]} ZIP_PATH [EXPECTED_VERSION]", file=sys.stderr)
        return 2
    expected = sys.argv[2] if len(sys.argv) > 2 else None
    return verify(sys.argv[1], expected)


if __name__ == "__main__":
    sys.exit(main())
