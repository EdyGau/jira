<?php

namespace App\Enum;

use InvalidArgumentException;

enum StatusTask
{
    case NEW;
    case TODO;
    case INPROGRESS;
    case ACCEPTED;
    case TEST;
    case READY;
    case CLOSED;

    /**
     * Get the human-readable value of the StatusTask enum.
     *
     * @return string
     */
    public function getValue(): string
    {
        return match ($this) {
            self::NEW => 'Nowy',
            self::TODO => 'TODO',
            self::INPROGRESS => 'W trakcie',
            self::ACCEPTED => 'Zaakceptowany',
            self::TEST => 'Do testów',
            self::READY => 'Gotowy do zamknięcia',
            self::CLOSED => 'Zamknięty',
        };
    }

    /**
     * Create a StatusTask enum instance from a string representation.
     *
     * @param string $statusString
     * @return self
     */
    public static function fromString(string $statusString): self
    {
        switch (strtolower($statusString)) {
            case 'new':
                return self::NEW;
            case 'todo':
                return self::TODO;
            case 'inprogress':
                return self::INPROGRESS;
            case 'accepted':
                return self::ACCEPTED;
            case 'test':
                return self::TEST;
            case 'ready':
                return self::READY;
            case 'closed':
                return self::CLOSED;
            default:
                throw new InvalidArgumentException('Niepoprawny status: ' . $statusString);
        }
    }
}
