<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Permission extends \Spatie\Permission\Models\Permission
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $casts = [
        'created_at' => 'date:Y-m-d h:m:s A',
        'updated_at' => 'date:Y-m-d h:m:s A',
    ];

    /**
     * The group to which the permission belongs.
     *
     * @return BelongsTo
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(PermissionGroup::class, 'group_id');
    }

    /**
     * Gets the group to which each permission of the parameter belongs.
     *
     * @param  array  $permissions
     * @return array
     */
    public static function getGroups(array $permissions): array
    {
        return array_map(function ($permission) {
            return static::where('id', $permission)
                ->with('group')
                ->get()
                ->first()['group'];
        }, $permissions);
    }
}
