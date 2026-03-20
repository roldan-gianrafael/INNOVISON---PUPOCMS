<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class Admin extends Model
{
    protected $table = 'admins';
    protected $primaryKey = 'admin_id';

    public $timestamps = false;
    public $incrementing = true;

    protected $keyType = 'int';

    protected $guarded = [];

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
