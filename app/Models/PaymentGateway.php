<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUid;

class PaymentGateway extends Model
{
    use HasFactory, HasUid;

    protected $table = 'payment_gateway';

    protected $fillable = [
        'uid',
        'order_id',
        'payment_id',
        'request',
        'response',
        'success_action',
        'failed_action',
        'status',
    ];
}
