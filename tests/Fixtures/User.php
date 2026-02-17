<?php

namespace SazzadBinAshique\LaravelExcelImporter\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
    ];
}
