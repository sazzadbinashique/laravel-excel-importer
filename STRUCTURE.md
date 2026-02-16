# Package Structure

```
package/
├── src/
│   ├── Console/
│   │   └── Commands/
│   │       └── CleanupImportFiles.php
│   ├── Exports/
│   │   └── ImportErrorsExport.php
│   ├── Imports/
│   │   └── BaseImport.php
│   ├── Jobs/
│   │   └── ProcessImportJob.php
│   ├── Livewire/
│   │   └── ExcelImporter.php
│   ├── Models/
│   │   └── Import.php
│   └── ExcelImporterServiceProvider.php
├── config/
│   └── excel-importer.php
├── database/
│   └── migrations/
│       └── create_imports_table.php
├── resources/
│   └── views/
│       ├── components/
│       │   └── layout.blade.php
│       └── livewire/
│           └── excel-importer.blade.php
├── public/
│   ├── sample-import.csv
│   ├── sample-10k.csv
│   └── sample-with-errors.csv
├── tests/
│   ├── Feature/
│   │   └── ImportTest.php
│   └── Unit/
│       └── CleanupTest.php
├── composer.json
├── README.md
├── INSTALLATION.md
├── CHANGELOG.md
├── LICENSE.md
└── .gitignore
```

## To Use This Package in Any Laravel Project

### Method 1: Local Package Development

1. Copy the `package/` folder to your project root
2. Add to `composer.json`:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "./package"
        }
    ],
    "require": {
        "yourvendor/laravel-excel-importer": "@dev"
    }
}
```

3. Run:
```bash
composer update yourvendor/laravel-excel-importer
```

### Method 2: Publish to GitHub/Packagist

1. Create GitHub repository
2. Push package code
3. Register on Packagist.org
4. Install via Composer:

```bash
composer require yourvendor/laravel-excel-importer
```

### Method 3: Private Repository

1. Add to `composer.json`:

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/yourcompany/laravel-excel-importer"
        }
    ]
}
```

2. Install:
```bash
composer require yourvendor/laravel-excel-importer
```
