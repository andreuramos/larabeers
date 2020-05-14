<?php

namespace Larabeers\External;

use App\Beer;
use Larabeers\Domain\Beer\Style;
use Larabeers\Domain\Beer\StyleRepository;

class EloquentStyleRepository implements StyleRepository
{
    public function search(string $query): array
    {
        $results = [];

        $matching_styles = Beer::select('type')
            ->where('type','ilike',"%$query%")
            ->distinct()
            ->get();

        foreach($matching_styles as $style) {
            $results[] = new Style($style->type);
        }

        return $results;
    }
}
