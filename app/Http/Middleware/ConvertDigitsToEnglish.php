<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ConvertDigitsToEnglish
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $input = $request->all();
        $convertedInput = $this->convertDigitsToEnglish($input);
        $request->replace($convertedInput);
        return $next($request);
    }

    private function convertDigitsToEnglish($data)
    {
        return array_map(function ($value) {
            if (is_array($value)) {
                return $this->convertDigitsToEnglish($value);
            }
            return convertDigits($value);
        }, $data);
    }
}
