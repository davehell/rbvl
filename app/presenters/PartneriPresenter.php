<?php

require_once dirname(__FILE__) . '/BasePresenter.php';

class PartneriPresenter extends BasePresenter
{
    public function actionDefault()
    {
        $this->template->pageTitle = '„RB“VL - Partneři';
        $this->template->pageDesc = 'Partneři „Region Beskydy“ volejbalové ligy';
        $this->template->pageHeading = 'Partneři';
    }
}
