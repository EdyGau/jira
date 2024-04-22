<?php

namespace App\Enum;

use InvalidArgumentException;

enum PriorityTask
{
    case LOW;
    case MEDIUM;
    case HIGH;
    case URGENT;

    public function getValue(): string
    {
        return match ($this) {
            self::LOW => 'Niski',
            self::MEDIUM => 'Średni',
            self::HIGH => 'Wysoki',
            self::URGENT => 'Pilny',
        };
    }

    public static function fromString(string $priorityString): self
    {
        switch (strtolower($priorityString)) {
            case 'low':
                return self::LOW;
            case 'medium':
                return self::MEDIUM;
            case 'high':
                return self::HIGH;
            case 'urgent':
                return self::URGENT;
            default:
                throw new InvalidArgumentException('Niepoprawny priorytet: ' . $priorityString);
        }
    }
}
