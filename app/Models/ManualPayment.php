<?php

namespace App\Models;

use Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class ManualPayment extends Model
{
    use \Backpack\CRUD\app\Models\Traits\CrudTrait;
    use HasFactory;

    protected $casts = [
        'event_datetime' => 'datetime',
        'loaded_to_bigquery_datetime' => 'datetime',
        'reviewed' => 'boolean',
    ];

    protected $fillable = [
        'drip_id',
        'customer_name',
        'email',
        'event',
        'license_type',
        'event_datetime',
        'order_id',
        'product_id',
        'product_name',
        'amount_collected',
        'currency',
        'payment_type',
        'reviewed',
        'loaded_to_bigquery_datetime',
    ];
    
    // protected function setDripIdAttribute($value)
    // {
    //     $this->attributes['drip_id'] = $this->getDripSubscriberId($value);
    // }

    public function returnToBatchView()
    {
        return "<a class=\"btn btn-primary\" href=\"" .backpack_url()."/batch\">".
                "<span class\"ladda-label\"><i class=\"las la-arrow-circle-left\"></i>&nbsp;Return to Batch list</span></a>";
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }


    protected function setEmailAttribute(string $value)
    {
        $this->attributes['email'] = $value;
        $this->attributes['drip_id'] = $this->getDripSubscriberId($value);
    }
    
    // protected function setPaymentTypeAttribute(string $value)
    // {
    //     $this->attribute['payment_type'] = $value;
    // }

    // protected function setCurrencyAttribute(string $value)
    // {
    //     $this->attribute['currency'] = $value;
    // }

    // protected function setOrderIdAttribute(string $value)
    // {
    //     $this->attribute['order_id'] = $value;
    // }

    protected function setProductIdAttribute(string $value)
    {
        // $this->attributes['license_type'] = $value;
        $this->attributes['product_id'] = $value;
        $this->attributes['product_name'] = $this->extractProductName(config('manualpayments.license_types')[$value]);
        $this->attributes['event'] = $this->extractEvent(config('manualpayments.license_types')[$value]);
    }

    protected function extractProductName(string $productLabel)
    {
        $dashPos = strpos($productLabel, '-');
        $name = substr($productLabel, $dashPos + 2, strlen($productLabel));
        return $name;
    }

    protected function extractEvent(string $productLabel)
    {
        if (stripos($productLabel, 'renew') !== false) {
            return 'renewal';
        }
        return 'license';
    }

    protected function getDripSubscriberId(string $email)
    {
        $response = Http::acceptJson()
                    ->withBasicAuth(config('manualpayments.drip_api_token'), '')
                    ->get(config('manualpayments.drip_api_url') . "/subscribers/$email");
        if ($response->successful()) {
            return $response['subscribers'][0]['id'];
        }
        return 'unknown';
    }
    // protected function dripid(): Attribute
    // {
    //     return Attribute::make(
    //         set: fn ($value) => 'abcdefg',
    //     );
    // }

    // protected function email(): Attribute
    // {
    //     return Attribute::make(
    //         set: fn ($value) => 'abcdefg',
    //     );
    // }
}
