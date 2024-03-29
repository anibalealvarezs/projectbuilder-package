# Change Log

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [1.3.7.2] - 2022-02-21

- BREAKING CHANGES:
    - [x] [1.3.7.1]

### Changed

- Controllers refactored
- Components refactored

## [1.3.7.1] - 2022-02-19

### Added

- Implemented cache for controllers
- Added facades por ```PbDebugbar``` class
- Added ```PbCacheController```

### Changed

- 'Permissions' and 'Navigations' seeders updated

### Fixed

- Fixed errors in ```Loggers``` CRUD
- Fixed 'module' fields in forms
- Fixed 'settings' menu button

## [1.3.7] - 2022-02-14

- BREAKING CHANGES:
  - [x] [1.3.6.1]

### Added

- Added Middleware to store config data in session
- Added singleton for utilities
- Added performance flags to CRUD controllers
- Added facades por ```PbUtilities``` class

### Changed

- Helpers moved from classes to functions in ```helpers.php``` file
- ```Helpers``` folder renamed as ```Utilities```
- ```PbHelpers``` class renamed as ```PbUtilities```
- CRUD permitions refactored
- ```crud``` attribute is now optional for models
- Overrides moved to ```Overrides``` folder

### Fixed

- Performance significantly improved

## [1.3.6.1] - 2022-02-11

### Added

- Added singleton for current user
- Added option for removing previous migration files

### Changed

- PbLogger and PbConfig tables names updated
- spatie/laravel-translatable minimum requirement updated
- Schema dump updated
- Providers installation moved to Project Builder section

### Fixed

- Fixed typo in Installation Command
- Fixed error on saving profile data

## [1.3.6] - 2022-02-08

### Added

- Laravel 9.x support added.

### Changed

- Unnecessary packages removed from composer.json.
- Schema's default string length moved to Project Builder's app service provider
- Installation updated.
    - Ask for confirmation for installing Inertia or leave.
    - Jetstream and Fortify no longer being asked to be ignored in composer.json.

### Fixed

- Fix: Inertia class no longer being imported on installation trait to prevent exception on fresh installations.
- webpack.config.js insertions format fixed.

## [1.3.5] - 2022-02-04

### Added

- New release after several months of work and missing changelog entries.

## [1.2.2.2] - 2021-07-18

### Changed

- Forms reset after creation
- Dragging no longer requires delay on mobile

### Fixed

- Form fields validations

## [1.2.2] - 2021-07-06

### Added

- Models are now sortable
- Draggable table rows for sortable models (using [Sortablejs](https://github.com/SortableJS/Sortable))
- ID and Position columns are now removable
- PbModule model added
- Customizable blade template in controllers

### Changed

- Status columns now show reference icons
- Config vars and Permissions are now associated to modules
- Seeders updated
- Custom "app.blade" file

### Fixed

- Added missing fields from models in forms and tables
- Sorting "icon" replaced by sorting "td"

## [1.2.1.1] - 2021-07-06

### Changed

- Views paths are now dynamic

### Fixed

## [1.2.1] - 2021-07-06

### Changed

- Controllers Refactored

### Fixed

## [1.2.0] - 2021-07-06

### Added

- Added `PbBuilderController`
- Added `PbBuilder` model

### Changed

- All controllers but "Auth" ones now extend from `PbBuilderController`
- All models but "Users & roles/permissions" ones now extend from `PbBuilder`
- `AeasHelpers` renamed as `PbHelpers`
- `PbRoles` model renamed as `PbRole`

### Fixed

- CRUD fully fixed

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
- Controllers/Traits/Utilities refactored

### Fixed

- CRUD errors in Controllers and Views


## [1.1.3.1] - 2021-06-27

### Added

- Translatable alias for Roles and Permissions

### Fixed

- Seeders debugged


## [1.1.3] - 2021-06-26

### Added

- Spatie Permission based Roles & Permissions now supported
- ControllerTrait for controller's shared methods
- Changelog

### Changed

- Inertia::share() instructions removed from PbAppServiceProvider. Global variable generation methods move to
  Utilities/Shares. Sharing is now specified in controller methods.
- 'isAdmin' middleware removed. Middlewares are now managed in controllers based on permissions
- New Roles & Permissions models extended from Spatie's models

### Fixed

- Menu CSS classes
- Directly routed CRUD forms
- projectbuilder.js helpers optimized
