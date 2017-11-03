<?php
/**
 * Sanitizer class for sanitizing data
 */

class Sanitizer {

    /**
     * Sanitizes data for database insertion by adding slashes to avoid SQL injection and decoding special characters to
     * prevent XSS attacks when rendered back out.
     *
     * @param string $text
     *
     * @return string
     */
    public static function escapeTextForDatabase(string $text): string
    {
       return addslashes(htmlspecialchars_decode($text));
    }
}