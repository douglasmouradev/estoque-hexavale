<?php

declare(strict_types=1);

namespace App\Providers;

use HexaVale\Auditoria\Aplicacao\PortaRegistrarAuditoria;
use HexaVale\Auditoria\Dominio\CadeiaHash;
use HexaVale\Auditoria\Infraestrutura\EloquentRegistrarAuditoria;
use HexaVale\Cadastros\Aplicacao\ExcluirProduto;
use HexaVale\Cadastros\Aplicacao\Portas\PortaProdutoEscrita;
use HexaVale\Cadastros\Infraestrutura\EloquentProdutoEscrita;
use HexaVale\Estoque\Aplicacao\Portas\PortaLedgerEstoque;
use HexaVale\Estoque\Aplicacao\RegistrarEntrada;
use HexaVale\Estoque\Aplicacao\RegistrarSaida;
use HexaVale\Estoque\Infraestrutura\EloquentLedgerEstoque;
use HexaVale\Identidade\Aplicacao\Entrar;
use HexaVale\Identidade\Aplicacao\Portas\PortaAutenticacao;
use HexaVale\Identidade\Aplicacao\Portas\PortaTotp;
use HexaVale\Identidade\Aplicacao\Portas\PortaUsuarioConsulta;
use HexaVale\Identidade\Aplicacao\VerificarTotp;
use HexaVale\Identidade\Infraestrutura\EloquentUsuarioConsulta;
use HexaVale\Identidade\Infraestrutura\LaravelAutenticacao;
use HexaVale\Identidade\Infraestrutura\OtphpTotp;
use HexaVale\Privacidade\Dominio\CifradorCampo;
use HexaVale\Privacidade\Dominio\MascaradorPii;
use HexaVale\Privacidade\Infraestrutura\SodiumCifradorCampo;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CifradorCampo::class, SodiumCifradorCampo::class);
        $this->app->singleton(MascaradorPii::class);
        $this->app->singleton(CadeiaHash::class);
        $this->app->singleton(PortaRegistrarAuditoria::class, EloquentRegistrarAuditoria::class);
        $this->app->singleton(PortaAutenticacao::class, LaravelAutenticacao::class);
        $this->app->singleton(PortaUsuarioConsulta::class, EloquentUsuarioConsulta::class);
        $this->app->singleton(PortaTotp::class, OtphpTotp::class);
        $this->app->singleton(Entrar::class);
        $this->app->singleton(VerificarTotp::class);
        $this->app->singleton(PortaLedgerEstoque::class, EloquentLedgerEstoque::class);
        $this->app->singleton(PortaProdutoEscrita::class, EloquentProdutoEscrita::class);
        $this->app->singleton(RegistrarEntrada::class);
        $this->app->singleton(RegistrarSaida::class);
        $this->app->singleton(ExcluirProduto::class);
    }

    public function boot(): void
    {
        //
    }
}
