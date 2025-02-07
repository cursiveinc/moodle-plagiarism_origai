<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitd8b05de54d16e8b2e4db14ec8f9fe8ba
{
    public static $files = array (
        '0e6d7bf4a5811bfa5cf40c5ccd6fae6a' => __DIR__ . '/..' . '/symfony/polyfill-mbstring/bootstrap.php',
    );

    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Symfony\\Polyfill\\Mbstring\\' => 26,
        ),
        'P' => 
        array (
            'PhpOffice\\PhpWord\\' => 18,
            'PhpOffice\\Math\\' => 15,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Symfony\\Polyfill\\Mbstring\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-mbstring',
        ),
        'PhpOffice\\PhpWord\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpoffice/phpword/src/PhpWord',
        ),
        'PhpOffice\\Math\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpoffice/math/src/Math',
        ),
    );

    public static $prefixesPsr0 = array (
        'S' => 
        array (
            'Smalot\\PdfParser\\' => 
            array (
                0 => __DIR__ . '/..' . '/smalot/pdfparser/src',
            ),
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitd8b05de54d16e8b2e4db14ec8f9fe8ba::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitd8b05de54d16e8b2e4db14ec8f9fe8ba::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInitd8b05de54d16e8b2e4db14ec8f9fe8ba::$prefixesPsr0;
            $loader->classMap = ComposerStaticInitd8b05de54d16e8b2e4db14ec8f9fe8ba::$classMap;

        }, null, ClassLoader::class);
    }
}
