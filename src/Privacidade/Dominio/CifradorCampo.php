<?php

declare(strict_types=1);

namespace HexaVale\Privacidade\Dominio;

interface CifradorCampo
{
    public function cifrar(string $textoClaro): string;

    public function decifrar(string $textoCifrado): string;

    public function indiceCego(string $textoClaro): string;
}
