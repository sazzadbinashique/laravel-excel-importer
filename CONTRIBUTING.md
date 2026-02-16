# Contributing to Laravel Excel Importer

Thank you for considering contributing to Laravel Excel Importer! This document outlines the process and guidelines for contributing.

## Table of Contents

- [Code of Conduct](#code-of-conduct)
- [How Can I Contribute?](#how-can-i-contribute)
- [Development Setup](#development-setup)
- [Pull Request Process](#pull-request-process)
- [Coding Standards](#coding-standards)
- [Testing Guidelines](#testing-guidelines)

## Code of Conduct

This project and everyone participating in it is governed by a Code of Conduct. By participating, you are expected to uphold this code.

### Our Standards

- Be respectful and inclusive
- Welcome newcomers and help them get started
- Accept constructive criticism gracefully
- Focus on what is best for the community
- Show empathy towards other community members

## How Can I Contribute?

### Reporting Bugs

Before creating bug reports, please check existing issues to avoid duplicates.

**When reporting bugs, include:**

1. **Description:** Clear and concise description of the bug
2. **Steps to Reproduce:** Detailed steps to reproduce the issue
3. **Expected Behavior:** What you expected to happen
4. **Actual Behavior:** What actually happened
5. **Environment:**
   - Laravel version
   - PHP version
   - Package version
   - Operating system
6. **Code Samples:** Relevant code snippets
7. **Screenshots:** If applicable

### Suggesting Enhancements

Enhancement suggestions are tracked as GitHub issues.

**When suggesting enhancements, include:**

1. **Use Case:** Why this enhancement would be useful
2. **Proposed Solution:** How you envision it working
3. **Alternatives:** Any alternative solutions you've considered
4. **Additional Context:** Any other relevant information

### Pull Requests

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Make your changes
4. Write or update tests
5. Ensure all tests pass
6. Update documentation
7. Commit your changes (`git commit -m 'Add amazing feature'`)
8. Push to the branch (`git push origin feature/amazing-feature`)
9. Open a Pull Request

## Development Setup

### Prerequisites

- PHP 8.2 or higher
- Composer
- Laravel 11.0 or higher
- Git

### Installation

```bash
# Clone your fork
git clone https://github.com/YOUR-USERNAME/laravel-excel-importer.git
cd laravel-excel-importer

# Install dependencies
composer install

# Create a test Laravel application (optional)
composer create-project laravel/laravel test-app
cd test-app

# Add local package
composer config repositories.local '{"type": "path", "url": "../"}'
composer require sazzadbinashique/laravel-excel-importer @dev
```

### Running Tests

```bash
# Run PHPUnit tests
composer test

# Run tests with coverage
composer test-coverage

# Run code style checks
composer check-style

# Fix code style issues
composer fix-style
```

## Pull Request Process

### Before Submitting

1. **Update Documentation:** Ensure README.md and relevant docs are updated
2. **Write Tests:** Add tests for new features or bug fixes
3. **Check Code Style:** Follow PSR-12 coding standards
4. **Update Changelog:** Add entry to CHANGELOG.md
5. **Test Locally:** Ensure all tests pass

### PR Title Format

Use conventional commit format:

- `feat: Add new import type for orders`
- `fix: Resolve progress tracking issue`
- `docs: Update installation guide`
- `refactor: Improve BaseModelImport performance`
- `test: Add tests for validation`
- `chore: Update dependencies`

### PR Description Template

```markdown
## Description
Brief description of changes

## Type of Change
- [ ] Bug fix (non-breaking change)
- [ ] New feature (non-breaking change)
- [ ] Breaking change (fix or feature that breaks existing functionality)
- [ ] Documentation update

## How Has This Been Tested?
Describe the tests you ran

## Checklist
- [ ] My code follows the style guidelines
- [ ] I have performed a self-review
- [ ] I have commented my code where necessary
- [ ] I have updated the documentation
- [ ] My changes generate no new warnings
- [ ] I have added tests that prove my fix/feature works
- [ ] New and existing tests pass locally
- [ ] I have updated CHANGELOG.md
```

## Coding Standards

### PHP Standards

- Follow [PSR-12](https://www.php-fig.org/psr/psr-12/) coding style
- Use type hints for parameters and return types
- Write docblocks for all public methods
- Use meaningful variable and method names

### Example

```php
<?php

namespace SazzadBinAshique\LaravelExcelImporter;

use Illuminate\Support\ServiceProvider;

/**
 * Service provider for Excel Importer package.
 */
class ExcelImporterServiceProvider extends ServiceProvider
{
    /**
     * Register package services.
     *
     * @return void
     */
    public function register(): void
    {
        // Implementation
    }
}
```

### Laravel Conventions

- Use Laravel's helper functions where appropriate
- Follow Laravel's directory structure
- Use Eloquent relationships properly
- Leverage Laravel's validation system

### Database

- Use migrations for all database changes
- Make migrations reversible (implement `down()` method)
- Use descriptive column names
- Add indexes where necessary

## Testing Guidelines

### Test Structure

```php
<?php

namespace SazzadBinAshique\LaravelExcelImporter\Tests;

use Orchestra\Testbench\TestCase;

class ImportTest extends TestCase
{
    /** @test */
    public function it_can_import_users_from_excel()
    {
        // Arrange
        $file = $this->getSampleFile();
        
        // Act
        $import = new UsersImport();
        Excel::import($import, $file);
        
        // Assert
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com'
        ]);
    }
}
```

### Test Coverage

- Aim for at least 80% code coverage
- Test happy paths and edge cases
- Test error handling
- Test validation rules
- Test with different file formats

### Test Types

1. **Unit Tests:** Test individual methods
2. **Feature Tests:** Test complete workflows
3. **Integration Tests:** Test package integration with Laravel

## Documentation

### Code Documentation

- Write clear docblocks for all public methods
- Include `@param`, `@return`, and `@throws` tags
- Provide usage examples in docblocks

### User Documentation

- Keep README.md up to date
- Update relevant guides
- Add examples for new features
- Include screenshots where helpful

## Release Process

Maintainers will handle releases following semantic versioning:

- **MAJOR:** Breaking changes
- **MINOR:** New features (backward compatible)
- **PATCH:** Bug fixes (backward compatible)

## Questions?

Feel free to:

- Open an issue for questions
- Email: sazzad.sumon35@gmail.com
- Start a discussion on GitHub

## Recognition

Contributors will be recognized in:

- GitHub contributors page
- Release notes
- README.md (for significant contributions)

---

Thank you for contributing to Laravel Excel Importer! 🎉

Your contributions help make this package better for everyone.
