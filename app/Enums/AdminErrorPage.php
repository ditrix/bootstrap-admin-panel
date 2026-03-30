<?php

namespace App\Enums;

enum AdminErrorPage: int
{
    case Unauthorized = 401;
    case NotFound = 404;
    case ServerError = 500;

    public function title(): string
    {
        return match ($this) {
            self::Unauthorized => '401 Error - SB Admin',
            self::NotFound => '404 Error - SB Admin',
            self::ServerError => '500 Error - SB Admin',
        };
    }

    public function displayCode(): string
    {
        return (string) $this->value;
    }

    public function lead(): string
    {
        return match ($this) {
            self::Unauthorized => 'Unauthorized',
            self::NotFound => 'This requested URL was not found on this server.',
            self::ServerError => 'Internal Server Error',
        };
    }

    public function extraMessage(): ?string
    {
        return match ($this) {
            self::Unauthorized => 'Access to this resource is denied.',
            default => null,
        };
    }

    public function usesIllustration(): bool
    {
        return $this === self::NotFound;
    }
}
