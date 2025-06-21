<?php

namespace App\Models;

use App\Enums\SiteType;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $domain
 * @property SiteType $type
 * @property string $root_dir
 * @property string $group
 * @property array $tags
 */
class Site extends Model
{

    protected $fillable = [
        'domain',
        'type',
        'root_dir',
        'group',
        'tags',
    ];

    protected function casts(): array
    {
        return [
            'type' => SiteType::class,
            'tags' => 'array',
        ];
    }

}
