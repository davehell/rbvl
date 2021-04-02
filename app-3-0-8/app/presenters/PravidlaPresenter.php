<?php
namespace App\Presenters;

final class PravidlaPresenter extends BasePresenter
{
    public function renderDefault()
    {
        $this->template->pageTitle = '„RB“VL - Pravidla';
        $this->template->pageDesc = '„Region Beskydy“ volejbalová liga - Pravidla soutěže';
        $this->template->pageHeading = 'Pravidla';
    }
}
