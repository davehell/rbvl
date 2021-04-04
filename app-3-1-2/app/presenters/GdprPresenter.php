<?php

namespace App\Presenters;

final class GdprPresenter extends BasePresenter
{
    public function renderDefault()
    {
        $this->template->pageTitle = '„RB“VL - GDPR';
        $this->template->pageDesc = '„Region Beskydy“ volejbalová liga - GDPR';
        $this->template->pageHeading = 'GDPR - ochrana osobních údajů';
    }
}
