<?php

declare(strict_types=1);

namespace Tests\Architecture;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

/**
 * Smoke: garante que o Deptrac está configurado e o domínio não puxa Illuminate.
 */
final class DependencyRuleTest extends TestCase
{
    #[Test]
    public function dominio_nao_referencia_illuminate(): void
    {
        $root = dirname(__DIR__, 2).'/src';
        /** @var RecursiveIteratorIterator<RecursiveDirectoryIterator> $iterator */
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($root, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        /** @var SplFileInfo $file */
        foreach ($iterator as $file) {
            if (! $file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            $path = $file->getPathname();
            if (! str_contains($path, DIRECTORY_SEPARATOR.'Dominio'.DIRECTORY_SEPARATOR)) {
                continue;
            }

            $contents = (string) file_get_contents($path);
            self::assertStringNotContainsString(
                'Illuminate\\',
                $contents,
                "Domínio não pode importar Illuminate: {$path}"
            );
            self::assertStringNotContainsString(
                'Laravel\\',
                $contents,
                "Domínio não pode importar Laravel: {$path}"
            );
        }
    }

    #[Test]
    public function deptrac_config_existe(): void
    {
        self::assertFileExists(dirname(__DIR__, 2).'/deptrac.yaml');
    }
}
