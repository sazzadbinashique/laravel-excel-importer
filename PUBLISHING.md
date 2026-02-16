# Publishing Guide - Laravel Excel Importer

Complete guide to publish your Laravel Excel Importer package to GitHub and Packagist.

**Author:** Sazzad Bin Ashique  
**Email:** sazzad.sumon35@gmail.com  
**Package:** `sazzadbinashique/laravel-excel-importer`

---

## 📋 Prerequisites Checklist

Before publishing, ensure you have:

- [x] Git repository initialized with initial commit
- [x] All package files in place
- [x] LICENSE file (MIT License)
- [x] README.md with complete documentation
- [x] CHANGELOG.md with version history
- [x] CONTRIBUTING.md with contribution guidelines
- [x] composer.json properly configured
- [x] .gitignore file configured
- [ ] GitHub account
- [ ] Packagist account

---

## 🚀 Step 1: Create GitHub Repository

### Option A: Using GitHub Website (Recommended)

1. **Go to GitHub:**
   - Visit: https://github.com/new
   - Or click "+" → "New repository"

2. **Repository Details:**
   ```
   Owner: sazzadbinashique
   Repository name: laravel-excel-importer
   Description: A Laravel package for importing any model from Excel files with preview, validation, and progress tracking
   Visibility: Public ✓
   ```

3. **Initialize settings:**
   ```
   ❌ Do NOT initialize with README (we already have one)
   ❌ Do NOT add .gitignore (we already have one)
   ✅ Add LICENSE: MIT License (or skip, we already have one)
   ```

4. **Click "Create repository"**

### Option B: Using GitHub CLI

If you have GitHub CLI installed:

```bash
# Login to GitHub
gh auth login

# Create repository
gh repo create sazzadbinashique/laravel-excel-importer \
  --public \
  --description "A Laravel package for importing any model from Excel files" \
  --source=. \
  --remote=origin \
  --push
```

---

## 🔗 Step 2: Connect Local Repository to GitHub

After creating the GitHub repository, you'll see instructions. Follow them:

```bash
# Navigate to package directory
cd C:\laragon\www\excel-import-preview\package

# Add GitHub as remote origin
git remote add origin https://github.com/sazzadbinashique/laravel-excel-importer.git

# Verify remote
git remote -v

# Push to GitHub
git push -u origin main
```

**Troubleshooting:**

If you get authentication error:
```bash
# Use personal access token
# Go to: https://github.com/settings/tokens
# Generate new token with 'repo' scope
# Use token as password when prompted
```

---

## 📦 Step 3: Create Initial Release

### 3.1 Create Git Tag

```bash
# Tag the release
git tag -a v1.0.0 -m "Initial release: Multi-Model Import System"

# Push the tag
git push origin v1.0.0

# Or push all tags
git push --tags
```

### 3.2 Create GitHub Release

1. **Go to your repository:**
   - https://github.com/sazzadbinashique/laravel-excel-importer

2. **Create Release:**
   - Click "Releases" → "Create a new release"
   - Choose tag: `v1.0.0`
   - Release title: `v1.0.0 - Multi-Model Import System`

3. **Release Description:**

```markdown
# Laravel Excel Importer v1.0.0 🎉

First stable release of Laravel Excel Importer - A powerful multi-model import system for Laravel.

## ✨ Features

- ✅ Import **any Laravel model** from Excel/CSV files
- ✅ Real-time progress tracking with Livewire
- ✅ Validation with detailed error reporting
- ✅ Error export to Excel format
- ✅ Queue processing for large files
- ✅ Automatic file cleanup scheduler
- ✅ Modern, responsive UI
- ✅ Easy extension system

## 📥 Installation

```bash
composer require sazzadbinashique/laravel-excel-importer
php artisan vendor:publish --provider="SazzadBinAshique\LaravelExcelImporter\ExcelImporterServiceProvider"
php artisan migrate
```

## 📚 Documentation

- [Installation Guide](INSTALLATION.md)
- [README](README.md)
- [Package Structure](STRUCTURE.md)

## 🎯 Quick Start

1. Extend `BaseModelImport` class
2. Register in config file
3. Access at `/import/yourtype`

## 🐛 Bug Reports

Found a bug? Please [open an issue](https://github.com/sazzadbinashique/laravel-excel-importer/issues/new).

## 📝 Changelog

See [CHANGELOG.md](CHANGELOG.md) for details.

---

**Author:** Sazzad Bin Ashique  
**Email:** sazzad.sumon35@gmail.com
```

4. **Attach files (optional):**
   - Sample CSV files
   - Screenshots

5. **Click "Publish release"**

---

## 🎨 Step 4: Register on Packagist

### 4.1 Create Packagist Account

1. **Visit:** https://packagist.org/
2. **Sign up** or **Login with GitHub** (recommended)

### 4.2 Submit Package

1. **Click "Submit"** (top right)

2. **Enter Repository URL:**
   ```
   https://github.com/sazzadbinashique/laravel-excel-importer
   ```

3. **Click "Check"**
   - Packagist will validate your repository
   - Ensure composer.json is properly configured

4. **Click "Submit"**

### 4.3 Enable Auto-Update Hook

Packagist will provide a webhook URL. Add it to GitHub:

1. **Go to GitHub repository settings:**
   - Settings → Webhooks → Add webhook

2. **Configure webhook:**
   ```
   Payload URL: [URL from Packagist]
   Content type: application/json
   Secret: [Leave empty or use Packagist secret]
   Events: Just the push event
   Active: ✓
   ```

3. **Click "Add webhook"**

Now Packagist will automatically update when you push to GitHub!

---

## 🏷️ Step 5: Add Badges to README

Add these badges to your `package/README.md`:

```markdown
[![Latest Version](https://img.shields.io/packagist/v/sazzadbinashique/laravel-excel-importer.svg?style=flat-square)](https://packagist.org/packages/sazzadbinashique/laravel-excel-importer)
[![Total Downloads](https://img.shields.io/packagist/dt/sazzadbinashique/laravel-excel-importer.svg?style=flat-square)](https://packagist.org/packages/sazzadbinashique/laravel-excel-importer)
[![GitHub Stars](https://img.shields.io/github/stars/sazzadbinashique/laravel-excel-importer.svg?style=flat-square)](https://github.com/sazzadbinashique/laravel-excel-importer/stargazers)
[![License](https://img.shields.io/packagist/l/sazzadbinashique/laravel-excel-importer.svg?style=flat-square)](https://packagist.org/packages/sazzadbinashique/laravel-excel-importer)
```

---

## ✅ Step 6: Verify Installation

Test your published package:

```bash
# Create a new Laravel project
composer create-project laravel/laravel test-project
cd test-project

# Install your package
composer require sazzadbinashique/laravel-excel-importer

# Verify installation
composer show sazzadbinashique/laravel-excel-importer
```

---

## 📢 Step 7: Announce Your Package

### 7.1 Laravel News

Submit to Laravel News:
- https://laravel-news.com/submit

### 7.2 Social Media

Share on:
- Twitter/X with hashtags: #Laravel #PHP #OpenSource
- LinkedIn
- Reddit (r/laravel, r/php)
- Dev.to

### 7.3 Laravel Forums

Post in:
- https://laracasts.com/discuss
- https://laravel.io/forum

---

## 🔄 Step 8: Future Updates

### Publishing Updates

1. **Make changes** and commit:
   ```bash
   git add .
   git commit -m "feat: Add new feature"
   git push origin main
   ```

2. **Update version** in `composer.json` (follow semantic versioning)

3. **Update CHANGELOG.md**

4. **Create new tag:**
   ```bash
   git tag -a v1.1.0 -m "Release version 1.1.0"
   git push origin v1.1.0
   ```

5. **Create GitHub release** as in Step 3

6. **Packagist auto-updates** via webhook

### Semantic Versioning

- **MAJOR (1.0.0 → 2.0.0):** Breaking changes
- **MINOR (1.0.0 → 1.1.0):** New features (backward compatible)
- **PATCH (1.0.0 → 1.0.1):** Bug fixes (backward compatible)

---

## 📊 Step 9: Monitor Your Package

### Packagist Statistics

Visit: https://packagist.org/packages/sazzadbinashique/laravel-excel-importer/stats

Track:
- Daily downloads
- Total downloads
- Dependent packages

### GitHub Insights

Visit: https://github.com/sazzadbinashique/laravel-excel-importer/pulse

Track:
- Stars
- Forks
- Issues
- Pull requests
- Contributors

---

## 🛡️ Step 10: Maintain Your Package

### Best Practices

1. **Respond to Issues:**
   - Reply within 48 hours
   - Be polite and helpful
   - Label issues appropriately

2. **Review Pull Requests:**
   - Check code quality
   - Run tests
   - Provide constructive feedback
   - Merge or request changes

3. **Keep Dependencies Updated:**
   ```bash
   composer update
   ```

4. **Test with New Laravel Versions:**
   - Test beta releases
   - Update compatibility

5. **Security:**
   - Monitor for security issues
   - Patch quickly
   - Announce security updates

---

## 📝 Checklist: Publishing Completion

After completing all steps, verify:

- [ ] GitHub repository created and pushed
- [ ] Initial release (v1.0.0) created
- [ ] Packagist package submitted
- [ ] Webhook configured (auto-update)
- [ ] Badges added to README
- [ ] Installation tested from Packagist
- [ ] Package announced
- [ ] Documentation complete

---

## 🎉 Congratulations!

Your package is now published and available to the Laravel community!

**Package URL:**
- GitHub: https://github.com/sazzadbinashique/laravel-excel-importer
- Packagist: https://packagist.org/packages/sazzadbinashique/laravel-excel-importer

**Installation Command:**
```bash
composer require sazzadbinashique/laravel-excel-importer
```

---

## 🆘 Troubleshooting

### Issue: Packagist Can't Find Repository

**Solution:**
- Ensure repository is public
- Check composer.json is in root
- Verify composer.json is valid JSON

### Issue: Webhook Not Working

**Solution:**
- Check webhook URL is correct
- Verify webhook is active
- Test webhook in GitHub settings

### Issue: Version Not Updating

**Solution:**
- Create git tag: `git tag v1.0.1`
- Push tag: `git push origin v1.0.1`
- Wait a few minutes for Packagist to sync

### Issue: Can't Install Package

**Solution:**
- Run `composer clear-cache`
- Check minimum PHP/Laravel version
- Verify dependencies are available

---

## 📧 Support

**Questions?** Contact:
- Email: sazzad.sumon35@gmail.com
- GitHub Issues: https://github.com/sazzadbinashique/laravel-excel-importer/issues

---

## 📚 Additional Resources

- [Packagist Documentation](https://packagist.org/about)
- [Creating a Package - Laravel Docs](https://laravel.com/docs/packages)
- [Semantic Versioning](https://semver.org/)
- [Keep a Changelog](https://keepachangelog.com/)

---

**Good luck with your open-source journey!** 🚀
