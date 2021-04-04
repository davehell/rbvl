<?php

namespace App\Presenters;


final class KontaktyPresenter extends BasePresenter
{
    public function renderDefault()
    {
        $this->template->pageTitle = '„RB“VL - Kontakty';
        $this->template->pageDesc = 'Kontakty na vedení „Region Beskydy“ volejbalové ligy';
        $this->template->pageHeading = 'Kontakty';
    }
}
