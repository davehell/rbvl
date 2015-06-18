<?php

require_once dirname(__FILE__) . '/BasePresenter.php';

class PravidlaPresenter extends BasePresenter
{
    public function renderDefault()
    {
        $this->template->pageTitle = '„RB“VL - Pravidla';
        $this->template->pageDesc = '„Region Beskydy“ volejbalová liga - Pravidla soutěže';
        $this->template->pageHeading = 'Pravidla';
    }
}
