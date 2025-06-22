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
            self::StaticHTML->value => 'Static HTML',
            self::NodeJS->value => 'Node.js',
            self::ReverseProxy->value => 'Reverse Proxy',
        ];
    }

    public static function getIcons(): array
    {
        return [
            self::PHP->value => 'si-php',
            self::NodeJS->value => 'si-nodedotjs',
            self::StaticHTML->value => 'si-html5',
            self::ReverseProxy->value => 'eos-proxy-o',
        ];
    }

}
