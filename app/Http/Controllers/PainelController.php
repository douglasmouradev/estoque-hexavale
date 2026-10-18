<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use RuntimeException;

/**
 * Serve o protótipo ERP canônico pós-login (HTML exatamente como enviado).
 * Não passa pelo Blade — Vue usa {{ }} no arquivo.
 */
final class PainelController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $path = resource_path('prototipo/hexavale_erp_unificado.html');
        if (! is_readable($path)) {
            throw new RuntimeException('Protótipo ERP ausente em resources/prototipo/.');
        }

        $html = (string) file_get_contents($path);
        $user = $request->user();
        $nome = $user !== null ? (string) $user->name : 'Usuário';
        $iniciais = $this->iniciais($nome);
        $csrf = csrf_token();
        $sairAction = e(route('sair'));
        $nomeEsc = e($nome);
        $iniciaisEsc = e($iniciais);

        $perfil = <<<HTML
            <div class="border-t border-brand-900 p-4 bg-brand-900/20">
                <div class="flex items-center gap-2">
                    <div class="w-9 h-9 rounded-full bg-brand-700 flex justify-center items-center text-white font-bold border border-brand-500 text-sm">{$iniciaisEsc}</div>
                    <div class="ml-1 flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{$nomeEsc}</p>
                        <p class="text-[11px] text-brand-400">Sessão autenticada</p>
                    </div>
                    <button type="button" id="hx-btn-sair" class="text-[11px] text-brand-200 hover:text-white px-2 py-1 border border-brand-600 rounded shrink-0">Sair</button>
                </div>
            </div>
HTML;

        $html = (string) preg_replace(
            '#<!-- User -->.*?</aside>#s',
            "<!-- User -->\n{$perfil}\n        </aside>",
            $html,
            1,
        );

        $inject = <<<HTML
<form id="hx-form-sair" method="post" action="{$sairAction}" style="display:none">
<input type="hidden" name="_token" value="{$csrf}">
</form>
<script>
document.getElementById('hx-btn-sair')?.addEventListener('click', function () {
  document.getElementById('hx-form-sair')?.submit();
});
</script>
HTML;

        // Só o </body> final — o ot-src embute outro </body> e um replace global quebra o Vue.
        $pos = strrpos($html, '</body>');
        if ($pos === false) {
            throw new RuntimeException('Protótipo ERP sem </body>.');
        }
        $html = substr($html, 0, $pos).$inject."\n".substr($html, $pos);

        return response($html, 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
            'Cache-Control' => 'no-store',
        ]);
    }

    private function iniciais(string $nome): string
    {
        $partes = preg_split('/\s+/u', trim($nome));
        if ($partes === false) {
            $partes = [];
        }
        $letras = '';
        foreach ($partes as $p) {
            if ($p === '') {
                continue;
            }
            $letras .= mb_strtoupper(mb_substr($p, 0, 1));
            if (mb_strlen($letras) >= 2) {
                break;
            }
        }

        return $letras !== '' ? $letras : 'HV';
    }
}
