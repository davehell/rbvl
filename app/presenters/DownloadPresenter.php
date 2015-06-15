<?php

require_once dirname(__FILE__) . '/BasePresenter.php';

class DownloadPresenter extends BasePresenter
{
    public function renderDefault()
    {
        $this->template->pageTitle = '„RB“VL - Ke stažení';
        $this->template->pageDesc = '„Region Beskydy“ volejbalová liga - Dokumenty ke stažení';
        $this->template->pageHeading = 'Ke stažení';
    }
}
