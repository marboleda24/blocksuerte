<?php

namespace App\Models\Dian;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollDocument extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    protected $connection = 'API_DIAN';

    /**
     * @var string
     */
    protected $table = 'document_payrolls';

    /**
     * @var string[]
     */
    protected $appends = ['year', 'month'];

    /**
     * @var string[]
     */
    protected $casts = [
        'request_api' => 'array',
    ];

    /**
     * @var string[]
     */
    protected $fillable = [
        'identification_number', 'state_document_id', 'type_document_id', 'prefix', 'consecutive', 'xml', 'pdf', 'cune',
        'employee_id', 'date_issue', 'accrued_total', 'deductions_total', 'total_payroll', 'request_api',
    ];

    /**
     * @return string
     */
    public function getYearAttribute(): string
    {
        return $this->request_api['year'] ?? Carbon::parse($this->request_api['payment_dates'][0]['payment_date'])->format('Y');
    }

    /**
     * @return string
     */
    public function getMonthAttribute(): string
    {
        return $this->request_api['month'] ?? Carbon::parse($this->request_api['payment_dates'][0]['payment_date'])->format('m');
    }
}
