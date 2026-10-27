<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentProvider extends Model
{
    protected $fillable = [
        'name', 'code', 'api_format', 'base_url',
        'api_key_encrypted', 'server_key_encrypted', 'client_key_encrypted',
        'merchant_id', 'extra_config',
        'is_active', 'is_default', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'extra_config' => 'array',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function getDecryptedApiKey(): ?string
    {
        return $this->api_key_encrypted ? decrypt($this->api_key_encrypted) : null;
    }

    public function getDecryptedServerKey(): ?string
    {
        return $this->server_key_encrypted ? decrypt($this->server_key_encrypted) : null;
    }

    public function getDecryptedClientKey(): ?string
    {
        return $this->client_key_encrypted ? decrypt($this->client_key_encrypted) : null;
    }

    public function getDecryptedPrivateKey(): ?string
    {
        $config = $this->extra_config ?? [];
        return isset($config['private_key']) ? decrypt($config['private_key']) : null;
    }

    public function getDecryptedMerchantCode(): ?string
    {
        $config = $this->extra_config ?? [];
        return $config['merchant_code'] ?? $this->merchant_id;
    }
}
