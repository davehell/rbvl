<?php

require_once dirname(__FILE__) . '/BasePresenter.php';

class DefaultPresenter extends BasePresenter
{
    public function renderDefault()
    {
        $this->template->pageTitle = '„Region Beskydy“ volejbalová liga';
        $this->template->pageHeading = '„Region Beskydy“ volejbalová liga';
        $this->template->pageDesc = '„Region Beskydy“ volejbalová liga je dlouhodobá soutěž hrající se ve Frýdku-Místku.';
        $this->template->scripts = array('lightbox');
    }
}
