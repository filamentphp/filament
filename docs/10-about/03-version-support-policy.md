---
title: Version Support Policy
---

## Overview

| Version | New Features | Bug Fixes          | Security Fixes |
|---------|--------------|--------------------|----------------|
| 1.x     | ❌            | ❌                  | ✅              |
| 2.x     | ❌            | ✅ until Aug 1 2026 | ✅              |
| 3.x     | ❌            | ✅                  | ✅              |
| 4.x     | ✅            | ✅                  | ✅              |

## New features

Pull requests to introduce new features are only accepted for the latest major version, except in special circumstances. Once a major version is released, the Filament team will no longer accept pull requests for new features for the previous major version, and open pull requests will be targeted to the latest major version or closed, depending on any conflicts with the new target branch.

## Bug fixes

Once a major version is released, the Filament team will merge pull requests for bug fixes for the previous major version for 2 years. After 2 years, the Filament team will no longer accept pull requests for the previous major version.

The Filament team will handle bug reports for supported versions in the order that they are received. Sometimes, a particularly serious bug may be prioritized over other reports. The team will generally write bug fixes for the latest major version only, and contributors may backport fixes to another supported version by opening a pull request if they wish.

## Security fixes

The Filament team does not currently intend to stop supporting any major version of Filament with security fixes. If this policy changes, the team will provide at least a year's notice before ending support for a major version.

If you discover a security vulnerability within Filament, please email Dan Harrin via [dan@danharrin.com](mailto:dan@danharrin.com). All security vulnerabilities will be promptly addressed.

Please be aware that while a Filament version may be supported with security fixes, its underlying dependencies such as PHP, Laravel and Livewire may not be. As such, apps that use these versions of Filament may be vulnerable to security issues in their dependencies.
