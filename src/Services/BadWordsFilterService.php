<?php

namespace App\Services;

class BadWordsFilterService
{
    private $badWords;

    public function __construct(array $badWords)
    {
        $this->badWords = $badWords;
    }

    public function filter(string $text): string
    {
        /*$pattern = '/(' . implode('|', $this->badWords) . ')/i';
        $replacement = str_repeat('*', strlen('$1'));
        return preg_replace($pattern, $replacement, $text);*/
        return str_ireplace($this->badWords, '***', $text);
    }
}