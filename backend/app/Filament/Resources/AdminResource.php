<?php

namespace App\Filament\Resources;

use Filament\Resources\Resource;

abstract class AdminResource extends Resource
{
    public static function canViewAny(): bool
    {
        return auth()->check() && (auth()->user()->role ?? null) === 'admin';
    }

    public static function canCreate(): bool
    {
        return static::canViewAny();
    }

    public static function canEdit($record): bool
    {
        return static::canViewAny();
    }

    public static function canDelete($record): bool
    {
        return static::canViewAny();
    }

    public static function canDeleteAny(): bool
    {
        return static::canViewAny();
    }
}

