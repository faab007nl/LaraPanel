<?php

namespace App\Enums;

enum UserRole: string
{

    case Admin = 'admin';
    case User = 'user';
    case SiteManager = 'site-manager';

    public static function getRoles(): array
    {
        return [
            self::Admin->value,
            self::User->value,
            self::SiteManager->value,
        ];
    }

    public static function getOptions(): array
    {
        return [
            self::Admin->value => 'Admin',
            self::User->value => 'User',
            self::SiteManager->value => 'Site Manager',
        ];
    }

}
