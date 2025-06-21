<?php

namespace App\Enums;

enum SiteType: string
{

    case PHP = 'php';
    case NodeJS = 'nodejs';
    case StaticHTML = 'static_html';
    case ReverseProxy = 'reverse_proxy';

    public static function getTypes(): array
    {
        return [
            self::PHP->value => 'PHP',
            self::NodeJS->value => 'Node.js',
            self::StaticHTML->value => 'Static HTML',
            self::ReverseProxy->value => 'Reverse Proxy',
        ];
    }

    public static function getOptions(): array
    {
        return [
            self::PHP->value => 'PHP',
            self::NodeJS->value => 'Node.js',
            self::StaticHTML->value => 'Static HTML',
            self::ReverseProxy->value => 'Reverse Proxy',
        ];
    }

}
