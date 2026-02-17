<?php

namespace SazzadBinAshique\LaravelExcelImporter\Tests\Fixtures;

use Illuminate\Support\Facades\Hash;
use SazzadBinAshique\LaravelExcelImporter\Imports\BaseImport;
use SazzadBinAshique\LaravelExcelImporter\Models\Import;

class UsersImport extends BaseImport
{
    public function __construct(Import $import)
    {
        parent::__construct($import);
    }

    public function model(array $row)
    {
        return new User([
            'name' => $row['name'],
            'email' => $row['email'],
            'password' => Hash::make($row['password'] ?? 'password'),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'email' => ['required', 'email'],
        ];
    }
}
