<?php

declare(strict_types=1);

namespace HexaVale\Privacidade\Infraestrutura;

use HexaVale\Privacidade\Dominio\CifradorCampo;
use RuntimeException;

final class SodiumCifradorCampo implements CifradorCampo
{
    private string $chaveCifra;

    private string $chaveIndice;

    public function __construct(?string $chaveCifraB64 = null, ?string $chaveIndiceB64 = null)
    {
        $this->chaveCifra = $this->decodificarChave(
            $chaveCifraB64 ?? $this->lerChaveConfig('hexavale.field_encryption_key', 'FIELD_ENCRYPTION_KEY'),
            'FIELD_ENCRYPTION_KEY',
        );
        $this->chaveIndice = $this->decodificarChave(
            $chaveIndiceB64 ?? $this->lerChaveConfig('hexavale.field_blind_index_key', 'FIELD_BLIND_INDEX_KEY'),
            'FIELD_BLIND_INDEX_KEY',
        );
    }

    public function cifrar(string $textoClaro): string
    {
        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $cipher = sodium_crypto_secretbox($textoClaro, $nonce, $this->chaveCifra);

        return base64_encode($nonce.$cipher);
    }

    public function decifrar(string $textoCifrado): string
    {
        $raw = base64_decode($textoCifrado, true);
        if ($raw === false || strlen($raw) < SODIUM_CRYPTO_SECRETBOX_NONCEBYTES) {
            throw new RuntimeException('Payload cifrado inválido.');
        }
        $nonce = substr($raw, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $cipher = substr($raw, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $claro = sodium_crypto_secretbox_open($cipher, $nonce, $this->chaveCifra);
        if ($claro === false) {
            throw new RuntimeException('Falha ao decifrar campo.');
        }

        return $claro;
    }

    public function indiceCego(string $textoClaro): string
    {
        return hash_hmac('sha256', mb_strtolower(trim($textoClaro)), $this->chaveIndice);
    }

    private function lerChaveConfig(string $chave, string $nome): string
    {
        $v = config($chave);
        if (! is_string($v) || $v === '') {
            throw new RuntimeException("{$nome} ausente ou inválida.");
        }

        return $v;
    }

    private function decodificarChave(string $b64, string $nome): string
    {
        $raw = base64_decode($b64, true);
        if ($raw === false || strlen($raw) !== SODIUM_CRYPTO_SECRETBOX_KEYBYTES) {
            throw new RuntimeException("Chave {$nome} inválida: esperado 32 bytes em base64.");
        }

        return $raw;
    }
}
