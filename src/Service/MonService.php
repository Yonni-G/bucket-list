<?php

// src/Service/MyService.php
namespace App\Service;

class MonService
{
    public function saluer(string $name): string
    {
        return "Hello, " . $name;
    }
}
