<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionAlert extends Model
{
    protected $fillable = [
        'type','ip','method','user_agent','request_uri','file_path', 'payload','params','headers','asset','tx_id','amount',
        'address','chain','blockNumber','counterAddress','subscriptionId'
    ];

    protected $casts = [
        'payload' => 'array',
        'params'  => 'array',
        'headers' => 'array',
    ];
}
