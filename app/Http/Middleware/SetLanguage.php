<?php

namespace App\Http\Middleware;

use App\Models\Language;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLanguage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Récupérer la langue demandée
        $langCode = $request->header('Accept-Language', "en_US");

        // 2. Chercher dans la table languages
        $language = null;
        if ($langCode) {
            $langCode = str_replace("-", "_", $langCode);
            $language = Language::where('code', $langCode)->first();
        }

        // 3. Si trouvé, utiliser son id, sinon fallback en anglais
        if (!$language) {
            $language = Language::find(1);
        }

        // 4. Injecter dans la Request
        $request->merge(['lang' => $language->id]);

        return $next($request);
    }
}
