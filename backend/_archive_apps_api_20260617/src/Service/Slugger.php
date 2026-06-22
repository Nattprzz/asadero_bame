<?php

namespace App\Service;

final class Slugger
{
    public function slug(string $value): string
    {
        $slug = iconv('UTF-8', 'ASCII//TRANSLIT', $value);
        $slug = strtolower((string) $slug);
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug) ?? '';
        return trim($slug, '-') ?: bin2hex(random_bytes(4));
    }
}
