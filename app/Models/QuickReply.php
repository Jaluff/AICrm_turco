<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Support\Traits\BelongsToCompany;

class QuickReply extends Model
{
    use HasFactory, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'department_id',
        'shortcut',
        'title',
        'body',
        'is_shared_with_ai',
        'is_active',
    ];

    protected $casts = [
        'is_shared_with_ai' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Interpolate dynamic variables into the body.
     * Supported: {{contactName}}, {{contactNumber}}, {{contactEmail}}, {{userName}}, {{greeting}}
     */
    public function interpolate(array $vars = []): string
    {
        $defaults = [
            '{{contactName}}'   => $vars['contactName'] ?? '',
            '{{contactNumber}}' => $vars['contactNumber'] ?? '',
            '{{contactEmail}}'  => $vars['contactEmail'] ?? '',
            '{{userName}}'      => $vars['userName'] ?? '',
            '{{greeting}}'      => $vars['greeting'] ?? self::greeting(),
        ];

        return str_replace(array_keys($defaults), array_values($defaults), $this->body);
    }

    public static function greeting(): string
    {
        $hour = now()->hour;
        return match (true) {
            $hour < 12 => 'Buenos días',
            $hour < 19 => 'Buenas tardes',
            default    => 'Buenas noches',
        };
    }
}
