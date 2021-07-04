# Change Log

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [1.1.5] - 2021-07-03

### Added

- Main Seeder
- Service Provider Trait
- AEAS Helper constants
- Dashboard Controller

### Changed

- Seeders and Service Providers refactored
- AEAS Helper methods as static

### Fixed

- Admin role permissions overlapping: [issue 3](https://github.com/anibalealvarezs/projectbuilder-package/issues/3)

## [1.1.4] - 2021-07-02

### Added

- Additional validations for Users CRUD to prevent Super-Admin/Admin accounts eliminations
- CRUD actions individually enabled/disabled

### Changed

- Permissions now as middlewares

### Fixed

- CRUD views actions to dropdowns

## [1.1.3.2] - 2021-06-29

### Added

- Restrictions for User, Roles and Permissions when creating/updating

### Changed

- Trailts moved to "Traits" folder and its names changed
- Controllers/Traits/Helpers refactored

### Fixed

- CRUD errors in Controllers and Views


## [1.1.3.1] - 2021-06-27

### Added

- Translatable alias for Roles and Permissions

### Changed

### Fixed

- Seeders debugged


## [1.1.3] - 2021-06-26

### Added

- Spatie Permission based Roles & Permissions now supported
- ControllerTrait for controller's shared methods
- Changelog

### Changed

- Inertia::share() instructions removed from PbAppServiceProvider. Global variable generation methods move to
  Helpers/Shares. Sharing is now specified in controller methods.
- 'isAdmin' middleware removed. Middlewares are now managed in controllers based on permissions
- New Roles & Permissions models extended from Spatie's models

### Fixed

- Menu CSS classes
- Directly routed CRUD forms
- projectbuilder.js helpers optimizated
