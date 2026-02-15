<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Sushi\Sushi;

class ReactionType extends Model
{
    use Sushi;

    protected $rows = [
        ['id' => 1, 'name' => 'Like', 'emoji' => '👍', 'slug' => 'like'],
        ['id' => 2, 'name' => 'Love', 'emoji' => '❤️', 'slug' => 'love'],
        ['id' => 3, 'name' => 'Haha', 'emoji' => '😂', 'slug' => 'haha'],
        ['id' => 4, 'name' => 'Wow', 'emoji' => '😮', 'slug' => 'wow'],
        ['id' => 5, 'name' => 'Sad', 'emoji' => '😢', 'slug' => 'sad'],
        ['id' => 6, 'name' => 'Angry', 'emoji' => 'jg', 'slug' => 'angry'],
    ];

    protected $schema = [
        'id' => 'integer',
        'name' => 'string',
        'emoji' => 'string',
        'slug' => 'string',
    ];
}
