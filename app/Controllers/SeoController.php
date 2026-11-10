<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;

final class SeoController extends Controller
{
    public function sitemap(Request $request): Response
    {
        $base = rtrim($request->schemeAndHost() . Request::detectedBasePath(), '/');
        $paths = ['/', '/menu', '/menu/pdf', '/deals', '/gallery', '/contact', '/careers'];

        $lines = ['<?xml version="1.0" encoding="UTF-8"?>'];
        $lines[] = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        foreach ($paths as $path) {
            $loc = htmlspecialchars($base . ($path === '/' ? '/' : $path), ENT_XML1 | ENT_QUOTES, 'UTF-8');
            $lines[] = '  <url><loc>' . $loc . '</loc></url>';
        }
        $lines[] = '</urlset>';

        return Response::xml(implode("\n", $lines) . "\n");
    }

    public function robots(Request $request): Response
    {
        $base = rtrim($request->schemeAndHost() . Request::detectedBasePath(), '/');
        $robots = implode("\n", [
            'User-agent: *',
            'Disallow: /admin/',
            'Disallow: /storage/',
            'Sitemap: ' . $base . '/sitemap.xml',
            '',
        ]);

        return Response::text($robots);
    }
}

