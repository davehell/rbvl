<?php

namespace App\Presenters;

use App;

final class DaryPresenter extends BasePresenter
{
    /** @var App\Model\Stranky */
    private $stranky;

    public function __construct(App\Model\Stranky $stranky)
    {
        $this->stranky = $stranky;
    }

    public function renderDefault()
    {
        $this->template->pageTitle = '„RB“VL - Dotace a dary';
        $this->template->pageDesc = 'Dotace a dary pro „Region Beskydy“ volejbalovou ligu';
        $this->template->pageHeading = 'Dotace a dary';

        $this->template->text = '';
        $stranka = $this->stranky->getByNazev("dary");
        if($stranka) $this->template->text = $stranka->text;
    }
}
