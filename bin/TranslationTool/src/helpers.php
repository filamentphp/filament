<?php

function createLink(string $url, string $text): string
{
    return "\e]8;;{$url}\e\\{$text}\e]8;;\e\\";
}
