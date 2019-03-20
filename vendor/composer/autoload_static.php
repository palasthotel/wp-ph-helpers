<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit206482f222522a72bcbd4cbf1231fc39
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PhHelpers\\' => 10,
        ),
        'C' => 
        array (
            'Cocur\\Slugify\\' => 14,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PhHelpers\\' => 
        array (
            0 => __DIR__ . '/../..' . '/classes',
        ),
        'Cocur\\Slugify\\' => 
        array (
            0 => __DIR__ . '/..' . '/cocur/slugify/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit206482f222522a72bcbd4cbf1231fc39::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit206482f222522a72bcbd4cbf1231fc39::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
