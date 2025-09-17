---
title: Version support policy
contents: false
---

## Introduction

| Version | New features               | Bug fixes                          | Security fixes                      |
|---------|----------------------------|------------------------------------|-------------------------------------|
| 1.x     | ❌ ended Jan 1 2022         | ❌ ended Jan 1 2025                 | ❌ ended Jul 1 2025                  |
| 2.x     | ❌ ended Jul 1 2023         | ❌ ended Jan 1 2025                 | ✅ until Jan 1 2026                  |
| 3.x     | ❌ ended Aug 1 2024         | ✅ until Aug 1 2026                 | ✅ until Jan 1 2028                  |
| 4.x     | ✅ until 5.x stable release | ✅ ~1 year after 5.x stable release | ✅ ~2 years after 5.x stable release |

## New features

Pull requests for new features are only accepted for the latest major version, except in special circumstances. Once a new major version is released, the Filament team will no longer accept pull requests for new features in previous versions. Any open pull requests will either be redirected to target the latest major version or closed, depending on conflicts with the new target branch.

## Bug fixes

After a major version is released, the Filament team will continue to merge pull requests for bug fixes in the previous major version for a year. After this period, pull requests for that version will no longer be accepted.

The Filament team processes bug reports for supported versions in chronological order, though critical bugs may be prioritized. Bug fixes are typically developed only for the latest major version. However, contributors can backport fixes to other supported versions by submitting pull requests.

## Security fixes

The Filament team currently plans to continue providing security fixes for major versions for at least two years.

If you discover a security vulnerability in Filament, please [report it through GitHub](https://github.com/filamentphp/filament/security/advisories). All security vulnerabilities will be addressed promptly.

Please note that while a Filament version may receive security fixes, its underlying dependencies (PHP, Laravel, and Livewire) may no longer be supported. Therefore, applications using older versions of Filament could still be vulnerable to security issues in these dependencies.
