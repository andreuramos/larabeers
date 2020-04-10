<?php

namespace Larabeers\External;

use App\Tag as EloquentTag;
use Larabeers\Entities\Tag;

class TagRepository
{
    public function search(string $query): array
    {
        $results = [];
        foreach (EloquentTag::where('text', 'ilike', "%query%")->get() as $db_tag) {
            $results[] = new Tag($db_tag->text);
        }
        return $results;
    }
}
