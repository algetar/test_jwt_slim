<?php

declare(strict_types=1);

namespace App\Facades;

class Html
{
    /**
     * Encodes special characters into HTML entities.
     * @param string $content the content to be encoded
     * @param bool $doubleEncode whether to encode HTML entities in `$content`. If false,
     * HTML entities in `$content` will not be further encoded.
     * @return string the encoded content
     * @see decode()
     * @see https://www.php.net/manual/en/function.htmlspecialchars.php
     */
    public static function encode(string $content, bool $doubleEncode = true): string
    {
        return htmlspecialchars($content, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', $doubleEncode);
    }
}