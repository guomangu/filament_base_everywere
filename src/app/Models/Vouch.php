<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vouch extends Model
{
    use HasFactory;

    protected $fillable = ['voucher_id', 'vouchee_id', 'score_impact'];

    public function voucher()
    {
        return $this->belongsTo(User::class, 'voucher_id');
    }

    public function vouchee()
    {
        return $this->belongsTo(User::class, 'vouchee_id');
    }
}
