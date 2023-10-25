<?php

namespace App\Ldap;

use App\Ldap\Scopes\ImportFilter;
use LdapRecord\Models\ActiveDirectory\User as BaseModel;

class User extends BaseModel
{
    /**
     * The object classes of the LDAP model.
     *
     * @var array
     */
    public static $objectClasses = [
        'top',
        'person',
        'organizationalperson',
        'user',
    ];

    /**
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope(new ImportFilter);
    }
}
