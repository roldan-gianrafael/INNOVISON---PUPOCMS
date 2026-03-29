<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Admin extends Model
{
    protected $table = 'admins';
    protected $primaryKey = 'admin_id';

    public $timestamps = true;
    public $incrementing = true;

    protected $keyType = 'int';

    protected $guarded = [];

    protected static function booted(): void
    {
        static::saving(function (Admin $admin) {
            $firstName = trim((string) $admin->first_name);
            $lastName = trim((string) $admin->last_name);
            $suffixName = trim((string) $admin->suffix_name);
            $fullName = trim(implode(' ', array_filter([$firstName, $lastName, $suffixName])));

            if ($fullName !== '' && static::hasColumn('name')) {
                $admin->name = $fullName;
            }
        });
    }

    public static function availableColumns(): array
    {
        static $columns;

        if ($columns === null) {
            $columns = Schema::hasTable('admins')
                ? Schema::getColumnListing('admins')
                : [];
        }

        return $columns;
    }

    public static function hasColumn(string $column): bool
    {
        return in_array($column, static::availableColumns(), true);
    }
}
