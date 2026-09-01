# Biopentra Blocksy Child 1.2.14 — release notes

## Added

- Self-updates from a private update server via the bundled
  [Plugin Update Checker](https://github.com/YahnisElsts/plugin-update-checker) v5
  library (`lib/plugin-update-checker/`), registered as a theme update checker
  only when `PRIVATE_UPDATE_SERVER` is defined in `wp-config.php`.
- CI workflow that uploads the release ZIP to the update server on each `v*` tag.

## Install

Deploy the `blocksy-child` theme **1.2.14** / tag **`v1.2.14`**.

Rollback: **1.2.13** / `v1.2.13`.
