<?php

namespace App\Ldap\Scopes;

use LdapRecord\Models\ActiveDirectory\Group;
use LdapRecord\Models\Model;
use LdapRecord\Models\ModelNotFoundException;
use LdapRecord\Models\Scope;
use LdapRecord\Query\Model\Builder;

class ImportFilter implements Scope
{
    /**
     * Apply the scope to the given query.
     *
     * @param  Builder  $query
     * @param  Model  $model
     * @return void
     *
     * @throws ModelNotFoundException
     */
    public function apply(Builder $query, Model $model): void
    {
        $group = Group::findByAnrOrFail('EV-PIU');
        $query->whereMemberOf($group);
    }
}
