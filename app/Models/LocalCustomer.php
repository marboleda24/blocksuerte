<?php

namespace App\Models;

use Awobaz\Compoships\Compoships;
use Awobaz\Compoships\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocalCustomer extends Model
{
    use HasFactory;
    use Compoships;

    /**
     * fillable
     *
     * @var array
     */
    protected $fillable = [
        'document_type', 'document', 'first_name', 'second_name', 'surname', 'second_surname', 'business_name',
        'business_reason', 'country', 'province', 'city', 'address', 'phone', 'cellphone', 'email', 'seller_id',
        'main_activity', 'great_contributor', 'responsable_iva', 'credit_limit', 'payment_deadline', 'discount_rate',
        'email_fe', 'emails_copies_fe', 'rut_file', 'created_by', 'approved_by', 'state', 'customer_type',
        'type_legal_entity', 'gravado', 'type_third',
    ];

    protected $appends = ['company_name'];

    /**
     * created by user
     *
     * @return HasOne
     */
    public function createdby(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'created_by');
    }

    /**
     * seller assigned
     *
     * @return HasOne
     */
    public function seller(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'seller_id');
    }

    /**
     * country data
     *
     * @return HasOne
     */
    public function country_name(): HasOne
    {
        return $this->hasOne(CountryDMS::class, 'pais', 'country');
    }

    /**
     * province data
     *
     * @return HasOne
     */
    public function province_name(): HasOne
    {
        return $this->hasOne(ProvinceDMS::class, 'departamento', 'province');
    }

    /**
     * city data
     *
     * @return HasOne
     */
    public function city_name(): HasOne
    {
        return $this->hasOne(CityDMS::class, ['pais', 'departamento', 'ciudad'], ['country', 'province', 'city']);
    }

    public function getCompanyNameAttribute()
    {
        return $this->business_name ?? implode(' ', [$this->first_name, $this->second_name, $this->surname, $this->second_surname]);
    }
}
