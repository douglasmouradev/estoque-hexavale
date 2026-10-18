# Glossário — linguagem ubíqua

Termos de domínio em **português**. Infraestrutura técnica permanece em inglês no código de adapters.

| Termo | Significado | Contexto |
|---|---|---|
| Fazenda | Unidade operacional multi-tenant (`fazenda_id`) | Compartilhado |
| Talhao | Área cultivada (manga/uva), com área em ha | Cadastros |
| Lote | Estoque físico rastreável com validade | Cadastros / Estoque |
| Produto | Insumo com unidade-base | Cadastros |
| Bula | Documento técnico versionado do produto | Cadastros / Conhecimento |
| Carencia | Intervalo mínimo até colheita (dias) | OperacaoAgricola |
| Reentrada | Intervalo até reentrada no talhão (horas) | OperacaoAgricola |
| Movimentacao | Lançamento append-only do ledger | Estoque |
| Reserva | Quantidade comprometida por ordem liberada | Estoque |
| SaldoLote | Projeção: físico, reservado, disponível | Estoque |
| OrdemAgricola | Pulverização ou adubação (ciclo de status) | OperacaoAgricola |
| Dose | Quantidade por ha ou por 100 L de calda | OperacaoAgricola |
| Calda | Volume de aplicação em L/ha | OperacaoAgricola |
| PedidoCompra | Solicitação com aprovação segregada | Compras |
| Pessoa | Titular de PII operacional | Cadastros / Privacidade |
| Auditoria | Evento append-only com hash encadeado | Auditoria |

Atualizar sempre que um novo conceito entrar no domínio.
