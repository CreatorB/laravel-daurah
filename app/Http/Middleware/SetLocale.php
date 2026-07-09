<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $available = (array) config('questions.locales', ['ar', 'id']);
        $default = 'id';

        $candidate = $request->query('locale')
            ?? $request->session()->get('locale')
            ?? $this->parseAcceptLanguage($request->header('Accept-Language'))
            ?? $default;

        $candidate = is_array($candidate) ? ($candidate[0] ?? $default) : (string) $candidate;
        $candidate = strtolower(trim($candidate));
        $short = substr($candidate, 0, 2);

        if (! in_array($candidate, $available, true) && ! in_array($short, $available, true)) {
            $candidate = $default;
        } else {
            $candidate = in_array($candidate, $available, true) ? $candidate : $short;
        }

        app()->setLocale($candidate);
        $request->session()->put('locale', $candidate);

        return $next($request);
    }

    private function parseAcceptLanguage(?string $header): ?string
    {
        if (! $header) {
            return null;
        }

        $parts = explode(',', $header);
        foreach ($parts as $part) {
            $tag = trim(explode(';', $part)[0]);
            if ($tag !== '') {
                return $tag;
            }
        }

        return null;
    }
}