<?php
namespace App\Controllers;

use App\Models\Archive;
use Core\BaseController;
use Core\Http\Exception\NotFoundException;

class ArchivesController extends BaseController
{
    public function index()
    {
        $data = [
            'pages' => Archive::findMostRecent(99),
        ];

        return $this->view('archives.html', $data);
    }

    public function add()
    {
        // $this->setMetadata([
        //     'title' => $page->meta_title,
        //     'description' => $page->meta_description,
        //     'keywords' => $page->meta_keywords,
        // ]);

        return $this->view('add.html', []);
    }

    public function showPage($request)
    {
        $pageName = $request->getAttribute('pageName');
        $archive = Archive::findByPageName($pageName);
        if (! $archive) {
            throw new NotFoundException('Page not found');
        }

        $this->setMetadata([
            'title' => $archive->title,
            'description' => $archive->description,
            'keywords' => $archive->keywords,
        ]);

        return $this->view('internal-page.html', ['archive' => $archive->toArray()]);
    }

    public function showCategory($request)
    {
        $categoryName = $request->getAttribute('categoryName');
        $data = [
            'category' => $categoryName,
            'pages' => Archive::findActiveByCategory($categoryName),
        ];

        $this->setMetadata([
            'url' => $this->app->getSetting('app.urls.site') . 'category/'.$categoryName,
            'title' => $categoryName.' Archives',
            'description' => 'Article archives for '.$categoryName.' category.',
            'keywords' => $page->meta_keywords.','.$categoryName,
            'og_type' => 'object',
        ]);

        return $this->view('archives.html', $data);
    }
}