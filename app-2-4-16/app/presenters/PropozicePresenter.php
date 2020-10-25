<?php
namespace App\Presenters;

final class PropozicePresenter extends BasePresenter
{
    public function renderDefault()
    {
        $this->template->pageTitle = '„RB“VL - Propozice';
        $this->template->pageDesc = '„Region Beskydy“ volejbalová liga - Propozice';
        $this->template->pageHeading = 'Propozice';
    }
}
