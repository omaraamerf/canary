<?php

namespace App\Models;

use App\Support\PhoneNumber;
use Illuminate\Database\Eloquent\Model;

class SellerProfile extends Model
{
    protected $fillable = [
        'user_id', 'display_name', 'bio', 'whatsapp', 'region_id', 'approval_status', 'rejection_reason',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    /**
     * Click-to-chat link, only when the seller chose to publish a WhatsApp number.
     */
    public function whatsappUrl(string $message = ''): ?string
    {
        return PhoneNumber::whatsappUrl($this->whatsapp, $this->region?->country?->code, $message);
    }
}
