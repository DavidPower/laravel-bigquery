<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;

    // protected $casts = [
    //     'event_datetime' => 'datetime',
    //     'loaded_to_bigquery_datetime' => 'datetime',
    //     'reviewed' => 'boolean',
    // ];

    protected $fillable = [
        'batch_id',
        'user_id',
        'source_count',
        'source_total',
        'destination_count',
        'destination_total',
    ];

    public function manualPayments()
    {
        return $this->hasMany(ManualPayment::class);
    }

    public function paymentsLink()
    {
        return "<a href=\"/admin/manual-payment?batch={$this->batch_id}\"><i class=\"las la-eye\"></i>&nbsp;Details</a>";
    }
}
