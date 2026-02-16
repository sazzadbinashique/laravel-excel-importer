# Changelog

All notable changes to `laravel-excel-importer` will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2026-02-16

### Added
- 🎉 Initial release
- Multi-model import system supporting unlimited model types
- `BaseModelImport` abstract class for easy extension
- Dynamic Livewire component that adapts to any import type
- Built-in support for Users and Products imports
- Real-time progress tracking with batch processing
- Validation with detailed error reporting
- Error export to Excel format
- Queue processing for large files
- Automatic file cleanup scheduler
- Import dashboard with history
- Configurable batch size, chunk size, and retention
- Comprehensive documentation and examples
- Sample CSV files for testing
- Modern, responsive UI with Tailwind CSS

### Features
- ✅ Import any Laravel model
- ✅ Preview data before import (first 10 rows)
- ✅ Live progress updates every 2 seconds
- ✅ Download error reports as Excel files
- ✅ Background job processing with Laravel queues
- ✅ Daily scheduled cleanup (configurable time)
- ✅ Batch processing (100 rows per batch by default)
- ✅ Chunk reading for memory efficiency
- ✅ Extensible architecture for custom imports
- ✅ Type-specific validation rules
- ✅ Custom validation messages
- ✅ File size limits (10MB by default)
- ✅ Support for XLSX, XLS, and CSV formats

### Developer Experience
- Easy installation via Composer
- Auto-discovery of service provider
- Publishable config, migrations, and views
- Clear documentation with examples
- Step-by-step guides for adding custom imports
- Best practices and advanced examples included

## [Unreleased]

### Planned Features
- Multi-sheet Excel support
- Import templates download
- Import scheduling
- Import validation preview
- Webhook notifications
- Import history export
- API endpoints for programmatic access
- Import templates with pre-filled data
- Data mapping configuration UI
- Import rollback functionality

---

## Release Notes

### Version 1.0.0

This is the initial stable release of Laravel Excel Importer. The package provides a complete solution for importing Excel/CSV files into any Laravel model with:

**Core Features:**
- Multi-model support out of the box
- Real-time progress tracking
- Comprehensive error handling
- Queue integration
- Automatic cleanup

**For Developers:**
- Simple 3-step process to add new import types
- Extend `BaseModelImport` abstract class
- Register in config file
- Ready to use!

**Production Ready:**
- Tested with files up to 10,000 rows
- Memory efficient with chunking
- Proper error handling and recovery
- Configurable timeouts and batch sizes
- Scheduled maintenance tasks

---

## Upgrade Guide

As this is the first release, no upgrade guide is needed.

For future upgrades, please refer to this section for breaking changes and migration steps.

---

## Contributing

See [CONTRIBUTING.md](CONTRIBUTING.md) for guidelines on how to contribute to this project.

---

## Security

If you discover any security-related issues, please email sazzad.sumon35@gmail.com instead of using the issue tracker.

---

[1.0.0]: https://github.com/sazzadbinashique/laravel-excel-importer/releases/tag/v1.0.0
