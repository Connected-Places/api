<?php

namespace App\Generators;

use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Str;

class UniqueSlugGenerator
{
    /**
     * @var \Illuminate\Database\DatabaseManager
     */
    protected $db;

    /**
     * UniqueSlugGenerator constructor.
     */
    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    /**
     * @param string $string The string to slugify
     */
    public function generate(string $string, string $table, string $column = 'slug', int $index = 0): string
    {
        $slug = $this->slugify($string);
        $slug .= $index === 0 ? '' : "-{$index}";

        $slugAlreadyUsed = $this->db->table($table)
            ->where($column, '=', $slug)
            ->exists();

        if ($slugAlreadyUsed) {
            return $this->generate($string, $table, $column, $index + 1);
        }

        return $slug;
    }

    /**
     * @param string $string The string to slugify
     * @param string $slug The existing slug to compare against
     * @return bool Check whether the input string would slugify into the provided slug
     */
    public function compareEquals(string $string, string $slug): bool
    {
        $stringSlugRegex = preg_quote($this->slugify($string));

        return preg_match("/^{$stringSlugRegex}(-[0-9]+)?$/", $slug) === 1;
    }

    protected function slugify(string $string): string
    {
        return Str::slug($string);
    }
}
