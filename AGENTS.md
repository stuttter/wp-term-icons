# WP Term Icons contributor guidance

## Compatibility

- Preserve PHP 7.4 and WordPress 6.4 compatibility unless a dedicated pull
  request explicitly changes the published minimums.
- Preserve the `icon` term-meta key, public classes, hooks, filters, asset
  handles, and stored `dashicons-*` values unless a deprecation path is part of
  the change.
- Treat term metadata writes, taxonomy queries, quick editing, and icon-picker
  behavior as compatibility-sensitive.

## Tests

- Add or update a regression test before changing observed PHP behavior.
- Characterize metadata persistence, taxonomy targeting, sorting, rendered
  markup, picker state, and shipped asset behavior when touching those paths.
- Run `composer test`, the declared PHP syntax matrix, and metadata/artifact
  validation before requesting review.

## Releases

The source version, readme stable tag, Git tag, and WordPress.org version must
agree before publishing. WordPress.org currently trails the unreleased GitHub
source and requires explicit reconciliation.

## Automation

Follow the organization-level safety boundaries. AI-authored implementation
must remain a draft pull request and cannot modify workflows, release policy,
ownership, security policy, dependencies, or this file without a specific
maintainer decision for that change.
