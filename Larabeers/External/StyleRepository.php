<?php

namespace Larabeers\External;

use App\Beer;
use Larabeers\Entities\Style;

class StyleRepository
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
