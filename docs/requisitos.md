# Requisitos iniciais

## Versões fixadas (Fase 0)

- PHP 8.5.x · Laravel 13.37 · MySQL 9.7.2

## Regras de negócio (RN) — rastreabilidade

| ID | Resumo | Módulo | Teste (alvo) |
|---|---|---|---|
| RN-01 | Liberar reserva; confirmar baixa única; cancelar exige motivo | OperacaoAgricola / Estoque | Fase 5 |
| RN-02 | disponível = físico − reservado | Estoque | Fase 3 |
| RN-03 | Lote vencido bloqueia uso | Estoque | Fase 3 |
| RN-04 | Conversão só mesma grandeza | Compartilhado | Fase 3 |
| RN-05 | Fórmulas pulverização/adubação | OperacaoAgricola | Fase 5 |
| RN-06 | Área tratada ∈ (0, planejada] | OperacaoAgricola | Fase 5 |
| RN-07 | Saída sem saldo negativo | Estoque | Fase 3 |
| RN-08 | Baixa automática uma vez | OperacaoAgricola | Fase 5 |
| RN-09 | Ajuste exige motivo; contagem ≥ reservado | Estoque | Fase 3 |
| RN-10 | Correção com motivo + versão | OperacaoAgricola | Fase 5 |
| RN-11 | Conferência bula / 5% | OperacaoAgricola | Fase 5 |
| RN-12 | Pedido + segregação aprovador | Compras | Fase 4 |
| RN-13 | Cadastro referenciado não exclui | Cadastros | Fase 2 |
| RN-14 | Situação saldo Zerado/Abaixo/OK | Estoque | Fase 3 |
| RN-15 | Custo por talhão e R$/ha | Relatorios | Fase 6 |
| RN-16 | Horímetro não decrescente | Frota | Fase 6 |
| RN-17 | Auditoria em toda gravação | Auditoria | Fase 1 |
| RN-18 | POST movimentações idempotente | Estoque | Fase 3 |
| RN-19 | CSV BOM `;` + impressão A4 | Relatorios | Fase 5–6 |
| RN-20 | Campos da ordem de campo | OperacaoAgricola | Fase 5 |

## Exemplo Gherkin (RN-01)

```gherkin
Funcionalidade: Ciclo de reserva da ordem
  Cenário: Emitir não movimenta
    Dado uma ordem em rascunho com itens
    Quando a ordem é apenas salva
    Então nenhuma movimentação nem reserva é criada
  Cenário: Liberar cria reserva
    Dado disponível suficiente em todos os lotes
    Quando a ordem é liberada
    Então reservas são criadas e o status é liberada
```

## RNF iniciais

| ID | Requisito |
|---|---|
| RNF-01 | Zero CDN em runtime |
| RNF-02 | DECIMAL / brick-math; proibido float em qtd/valor |
| RNF-03 | Multi-fazenda com filtro obrigatório |
| RNF-04 | LGPD por design (matriz em docs/lgpd) |
| RNF-05 | ASVS nível 2 (Fase 10) |
