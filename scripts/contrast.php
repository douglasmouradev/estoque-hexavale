<?php

declare(strict_types=1);

/**
 * Relatório de contraste WCAG 2.2 AA (texto normal ≥ 4,5:1).
 * Uso: php scripts/contrast.php
 */
function luminancia(string $hex): float
{
    $hex = ltrim($hex, '#');
    $rgb = [
        hexdec(substr($hex, 0, 2)) / 255,
        hexdec(substr($hex, 2, 2)) / 255,
        hexdec(substr($hex, 4, 2)) / 255,
    ];
    $lin = array_map(static function (float $c): float {
        return $c <= 0.03928 ? $c / 12.92 : (($c + 0.055) / 1.055) ** 2.4;
    }, $rgb);

    return 0.2126 * $lin[0] + 0.7152 * $lin[1] + 0.0722 * $lin[2];
}

function razao(string $fg, string $bg): float
{
    $a = luminancia($fg);
    $b = luminancia($bg);
    $hi = max($a, $b);
    $lo = min($a, $b);

    return ($hi + 0.05) / ($lo + 0.05);
}

$pares = [
    // claro — tokens oficiais
    ['tx', 'bg', '#1c2a1f', '#f4f6f3'],
    ['mu', 'bg', '#667568', '#f4f6f3'],
    ['mu-ink', 'bg', '#5b695d', '#f4f6f3'],
    ['mu', 'card', '#667568', '#ffffff'],
    ['ac', 'card', '#1f6b3a', '#ffffff'],
    ['ac', 'ac2', '#1f6b3a', '#e6f2ea'],
    ['wa', 'wab', '#b26a00', '#fff3dc'],
    ['wa-ink', 'wab', '#975a00', '#fff3dc'],
    ['er', 'erb', '#b3261e', '#fdeceb'],
    ['info', 'info-bg', '#1d4ed8', '#dbeafe'],
    // escuro
    ['tx', 'bg', '#e8efe9', '#121813'],
    ['mu', 'bg', '#9bab9e', '#121813'],
    ['ac', 'card', '#5fbf82', '#1b231d'],
    ['wa', 'wab', '#f0b44c', '#352a12'],
    ['er', 'erb', '#f2877f', '#3a1a18'],
];

$falhas = 0;
echo "Contraste HexaVale (AA texto ≥ 4,50)\n";
echo str_repeat('-', 56)."\n";
foreach ($pares as [$fgName, $bgName, $fg, $bg]) {
    $r = razao($fg, $bg);
    $ok = $r >= 4.5;
    if (! $ok) {
        $falhas++;
    }
    printf("%-8s on %-8s  %5.2f  %s\n", $fgName, $bgName, $r, $ok ? 'OK' : 'FAIL');
}

echo str_repeat('-', 56)."\n";
if ($falhas > 0) {
    echo "Pares oficiais que falham usam *-ink na UI de texto.\n";
    exit(0); // informativo: falhas esperadas nos tokens brutos
}

echo "Todos os pares listados passam AA.\n";
