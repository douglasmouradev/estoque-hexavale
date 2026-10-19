<?php

declare(strict_types=1);

namespace HexaVale\Compartilhado\Dominio;

use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use InvalidArgumentException;

/** RN-04 — conversão só na mesma grandeza (massa ou volume). */
final class ConversorUnidade
{
    /** @var array<string, array{grandeza: string, para_base: string}> */
    private const TABELA = [
        'kg' => ['grandeza' => 'massa', 'para_base' => '1'],
        'g' => ['grandeza' => 'massa', 'para_base' => '0.001'],
        'L' => ['grandeza' => 'volume', 'para_base' => '1'],
        'mL' => ['grandeza' => 'volume', 'para_base' => '0.001'],
    ];

    public static function converter(Quantidade $quantidade, string $de, string $para): Quantidade
    {
        $de = self::normalizar($de);
        $para = self::normalizar($para);
        if ($de === $para) {
            return $quantidade;
        }
        $a = self::TABELA[$de] ?? null;
        $b = self::TABELA[$para] ?? null;
        if ($a === null || $b === null) {
            throw new InvalidArgumentException("Unidade não suportada: {$de} → {$para}.");
        }
        if ($a['grandeza'] !== $b['grandeza']) {
            throw new InvalidArgumentException('Conversão só é permitida na mesma grandeza (RN-04).');
        }

        $fatorDe = BigDecimal::of($a['para_base']);
        $fatorPara = BigDecimal::of($b['para_base']);
        $emBase = BigDecimal::of($quantidade->paraString())->multipliedBy($fatorDe);
        $resultado = $emBase->dividedBy($fatorPara, 6, RoundingMode::HalfUp);

        return Quantidade::de($resultado->__toString());
    }

    public static function mesmaGrandeza(string $a, string $b): bool
    {
        $a = self::normalizar($a);
        $b = self::normalizar($b);
        $ta = self::TABELA[$a] ?? null;
        $tb = self::TABELA[$b] ?? null;

        return $ta !== null && $tb !== null && $ta['grandeza'] === $tb['grandeza'];
    }

    private static function normalizar(string $u): string
    {
        $u = trim($u);
        if ($u === 'l') {
            return 'L';
        }
        if ($u === 'ml') {
            return 'mL';
        }

        return $u;
    }
}
