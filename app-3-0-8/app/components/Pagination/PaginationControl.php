<?php

namespace App\Components;

use Nette\Application\UI\Control;
use Nette\Http\Request;
use Nette\Http\Urlscript;
use Nette\Utils\Paginator;

/**
 * Komponenta pro správu a vykreslování stránkování.
 * @package App\Components
 */
class PaginationControl extends Control {

    const TEMPLATE = __DIR__ . '/pagination.latte';


    private $radius;


    private $paginator;


    private $url;


    public function __construct(Request $request, int $radius = 5) {
        $this->paginator = new Paginator();
        $this->url = $request->getUrl();
        $this->setRadius($radius);
    }

    public function setRadius($radius) {
        if (is_numeric($radius))
            $this->radius = (int) $radius;
    }

    public function getPaginator() {
        return $this->paginator;
    }

    /** Vykresluje komponentu pro správu a vykreslování stránkování. */
    public function render() {
        $this->template->setFile(self::TEMPLATE); // Nastaví šablonu komponenty.
        // Deklarace pomocných proměných.
        $page = $this->paginator->getPage();
        $pages = $this->paginator->getPageCount();

        // Předává parametry do šablony.
        $this->template->page = $page;
        $this->template->pages = $pages;
        $this->template->left = $page - $this->radius >= 1 ? $page - $this->radius : 1;
        $this->template->right = $page + $this->radius <= $pages ? $page + $this->radius : $pages;
        $this->template->url = $this->url;
        $this->template->render(); // Vykreslí komponentu.
    }
}


interface IPaginationControlFactory {
    public function create(Request $request, int $radius): PaginationControl;
}
