<?php

namespace Larabeers\External;

use App\Tag as EloquentTag;
use Larabeers\Domain\Label\Tag;
use Larabeers\Domain\Label\TagRepository;
use Larabeers\Exceptions\TagNotFoundException;

class EloquentTagRepository implements TagRepository
{
    public function search(string $query): array
    {
        $results = [];
        foreach (EloquentTag::where('text', 'ilike', "%$query%")->get() as $db_tag) {
            $results[] = new Tag($db_tag->text, $db_tag->id);
        }
        return $results;
    }

    public function findById(int $id): ?Tag
    {
        $tag = EloquentTag::find($id);
        if (!$tag) {
            throw new TagNotFoundException($id);
        }
        return new Tag($tag->text, $tag->id);
    }
}
