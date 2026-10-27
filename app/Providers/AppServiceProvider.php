<?php

declare(strict_types=1);

namespace App\Providers;

use HexaVale\Auditoria\Aplicacao\PortaRegistrarAuditoria;
use HexaVale\Auditoria\Dominio\CadeiaHash;
use HexaVale\Auditoria\Infraestrutura\EloquentRegistrarAuditoria;
use HexaVale\Cadastros\Aplicacao\ExcluirProduto;
use HexaVale\Cadastros\Aplicacao\Portas\PortaProdutoEscrita;
use HexaVale\Cadastros\Infraestrutura\EloquentProdutoEscrita;
use HexaVale\Compras\Aplicacao\AprovarPedidoCompra;
use HexaVale\Compras\Aplicacao\CriarPedidoCompra;
use HexaVale\Compras\Aplicacao\Portas\PortaPedidoCompra;
use HexaVale\Compras\Aplicacao\ReceberPedidoCompra;
use HexaVale\Compras\Aplicacao\RejeitarPedidoCompra;
use HexaVale\Compras\Aplicacao\SolicitarAprovacaoPedido;
use HexaVale\Compras\Infraestrutura\EloquentPedidoCompra;
use HexaVale\Estoque\Aplicacao\ConsumirReserva;
use HexaVale\Estoque\Aplicacao\LiberarReserva;
use HexaVale\Estoque\Aplicacao\Portas\PortaLedgerEstoque;
use HexaVale\Estoque\Aplicacao\RegistrarAjusteInventario;
use HexaVale\Estoque\Aplicacao\RegistrarEntrada;
use HexaVale\Estoque\Aplicacao\RegistrarSaida;
use HexaVale\Estoque\Aplicacao\ReservarEstoque;
use HexaVale\Estoque\Infraestrutura\EloquentLedgerEstoque;
use HexaVale\Identidade\Aplicacao\Entrar;
use HexaVale\Identidade\Aplicacao\Portas\PortaAutenticacao;
use HexaVale\Identidade\Aplicacao\Portas\PortaTotp;
use HexaVale\Identidade\Aplicacao\Portas\PortaUsuarioConsulta;
use HexaVale\Identidade\Aplicacao\VerificarTotp;
use HexaVale\Identidade\Infraestrutura\EloquentUsuarioConsulta;
use HexaVale\Identidade\Infraestrutura\LaravelAutenticacao;
use HexaVale\Identidade\Infraestrutura\OtphpTotp;
use HexaVale\OperacaoAgricola\Aplicacao\BaixarOrdemAgricola;
use HexaVale\OperacaoAgricola\Aplicacao\CancelarOrdemAgricola;
use HexaVale\OperacaoAgricola\Aplicacao\CriarOrdemAgricola;
use HexaVale\OperacaoAgricola\Aplicacao\LiberarOrdemAgricola;
use HexaVale\OperacaoAgricola\Aplicacao\Portas\PortaOrdemAgricola;
use HexaVale\OperacaoAgricola\Infraestrutura\EloquentOrdemAgricola;
use HexaVale\Privacidade\Dominio\CifradorCampo;
use HexaVale\Privacidade\Dominio\MascaradorPii;
use HexaVale\Privacidade\Infraestrutura\SodiumCifradorCampo;
use Illuminate\Support\Facades\URL;
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
        $this->app->singleton(RegistrarAjusteInventario::class);
        $this->app->singleton(ReservarEstoque::class);
        $this->app->singleton(LiberarReserva::class);
        $this->app->singleton(ExcluirProduto::class);
        $this->app->singleton(PortaPedidoCompra::class, EloquentPedidoCompra::class);
        $this->app->singleton(CriarPedidoCompra::class);
        $this->app->singleton(SolicitarAprovacaoPedido::class);
        $this->app->singleton(AprovarPedidoCompra::class);
        $this->app->singleton(RejeitarPedidoCompra::class);
        $this->app->singleton(ReceberPedidoCompra::class);
        $this->app->singleton(ConsumirReserva::class);
        $this->app->singleton(PortaOrdemAgricola::class, EloquentOrdemAgricola::class);
        $this->app->singleton(CriarOrdemAgricola::class);
        $this->app->singleton(LiberarOrdemAgricola::class);
        $this->app->singleton(BaixarOrdemAgricola::class);
        $this->app->singleton(CancelarOrdemAgricola::class);
    }

    public function boot(): void
    {
        $appUrl = config('app.url');
        if (is_string($appUrl) && str_starts_with($appUrl, 'https://')) {
            URL::forceScheme('https');
        }
    }
}
