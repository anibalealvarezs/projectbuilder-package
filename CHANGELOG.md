# Change Log

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

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
