<?php

declare(strict_types=1);

namespace MetaFramework\Inputable\Enum;

enum ContentTypeEnum: string
{
    case HTML = 'html';
    case MARKDOWN = 'markdown';
    case TEXT = 'text';

    public static function default(): string
    {
        return self::TEXT->value;
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function resolve(?string $value): ?self
    {
        if (!$value) {
            return null;
        }

        $normalized = strtolower(trim($value));

        if ($normalized === '') {
            return null;
        }

        return self::tryFrom($normalized) ?? self::TEXT;
    }
}
