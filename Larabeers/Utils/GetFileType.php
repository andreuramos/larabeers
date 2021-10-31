<?php

namespace Larabeers\Utils;

class GetFileType
{
    public function execute(string $file_path): string
    {
        return mime_content_type($file_path);
    }
}
