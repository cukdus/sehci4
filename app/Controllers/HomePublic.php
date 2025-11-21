<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class HomePublic extends Controller
{
    public function index()
    {
        $content = view('home/pages/landing');
        return view('home/layout', [
            'content' => $content,
            'bodyClass' => 'index-page',
            'bodyAttrs' => 'data-aos-easing="ease-in-out" data-aos-duration="600" data-aos-delay="0"',
            'headerClass' => 'fixed-top',
        ]);
    }

    public function page(string $slug)
    {
        $viewPath = 'home/pages/' . $slug;
        if (!is_file(APPPATH . 'Views/' . $viewPath . '.php')) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        $content = view($viewPath);
        $bodyClass = $slug === 'register' ? 'blog-details-page' : 'index-page';
        $bodyAttrs = $slug === 'register' ? '' : 'data-aos-easing="ease-in-out" data-aos-duration="600" data-aos-delay="0"';
        $headerClass = $slug === 'register' ? 'sticky-top' : 'fixed-top';
        return view('home/layout', [
            'content' => $content,
            'bodyClass' => $bodyClass,
            'bodyAttrs' => $bodyAttrs,
            'headerClass' => $headerClass,
        ]);
    }
}
