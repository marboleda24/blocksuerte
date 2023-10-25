<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class QualityControlReview extends Model
{
    use HasFactory;

    /**
     * @var array
     */
    protected $fillable = [
        'production_order', 'quantity_inspected', 'conforming_quantity', 'non_conforming_quantity', 'cause_id',
        'operator_id', 'inspector_id', 'non_compliant_treatment', 'actions', 'observations', 'work_center',
        'register_by', 'type', 'reported_to_id'
    ];

    /**
     * @var string[]
     */
    protected $hidden = [
        'cause_id', 'operator_id', 'inspector_id', 'register_by',
    ];

    /**
     * @return HasOne
     */
    public function inspector(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'inspector_id');
    }

    /**
     * @return HasOne
     */
    public function operator(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'operator_id');
    }

    /**
     * @return HasOne
     */
    public function cause(): HasOne
    {
        return $this->hasOne(QualityControlReviewCause::class, 'id', 'cause_id');
    }

    /**
     * @return mixed
     */
    public function inspection_user(): mixed
    {
        return $this->has(QualityControlInspectionUser::class, 'id', 'inspection_user_id');
    }

    /**
     * @return HasMany
     */
    public function files(): HasMany
    {
        return $this->hasMany(QualityControlReviewFile::class, 'review_id', 'id');
    }

    /**
     * @return HasOne
     */
    public function reported_to(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'reported_to_id');
    }
}
