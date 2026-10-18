<?php

declare(strict_types=1);

/**
 * Falha se achar hex fora da paleta oficial (e derivados documentados) em CSS/Blade.
 * Uso: php scripts/check-palette.php
 */
$permitidos = [
    // oficiais claro/escuro
    'f4f6f3', 'ffffff', '1c2a1f', '667568', 'dfe5dc', '1f6b3a', 'e6f2ea',
    'b26a00', 'fff3dc', 'b3261e', 'fdeceb', '1d4ed8', 'dbeafe',
    '121813', '1b231d', 'e8efe9', '9bab9e', '2d392f', '5fbf82', '1f3326',
    'f0b44c', '352a12', 'f2877f', '3a1a18', '93c5fd', '1e3a5f',
    // AA ink + brand profunda + hover ac
    '5b695d', '975a00', '166534', '14532d', '052e16', '175530', '4aa86c',
    'b8c4b6', '3d4a3f', '8a5200',
    // ficha impressão sempre clara
    '111111', '000000',
];

$permitidos = array_flip($permitidos);

$dirs = [
    __DIR__.'/../resources/css',
    __DIR__.'/../resources/views',
];

$re = '/#([0-9a-fA-F]{3,8})\b/';
$violacoes = [];

$iterator = static function (string $dir) use (&$iterator): Generator {
    if (! is_dir($dir)) {
        return;
    }
    foreach (scandir($dir) ?: [] as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }
        $path = $dir.'/'.$item;
        if (is_dir($path)) {
            yield from $iterator($path);
        } elseif (preg_match('/\.(css|blade\.php|php|html)$/', $path)) {
            yield $path;
        }
    }
};

foreach ($dirs as $dir) {
    foreach ($iterator($dir) as $file) {
        $conteudo = (string) file_get_contents($file);
        if (! preg_match_all($re, $conteudo, $m, PREG_OFFSET_CAPTURE)) {
            continue;
        }
        foreach ($m[1] as [$raw]) {
            $hex = strtolower($raw);
            if (strlen($hex) === 3) {
                $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
            }
            if (strlen($hex) === 8) {
                $hex = substr($hex, 0, 6); // ignora alpha
            }
            if (! isset($permitidos[$hex])) {
                $violacoes[] = basename($file).": #{$raw}";
            }
        }
    }
}

$violacoes = array_values(array_unique($violacoes));
if ($violacoes !== []) {
    fwrite(STDERR, "Cores fora da paleta:\n- ".implode("\n- ", $violacoes)."\n");
    exit(1);
}

echo "Paleta OK — nenhum hex fora da lista oficial/derivados.\n";
