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

        $this->template->text = '';
        $stranky = new Stranky;
        $stranka = $stranky->findByNazev('uvod')->fetch();
        if($stranka) $this->template->text = $stranka->text;

        $tabulky = new Tabulky;
        $this->template->tabulkyA = $tabulky->getTabulky(19)->fetchAll();
        $this->template->skupinaA = '';
        $this->template->tabulkyB = $tabulky->getTabulky(22)->fetchAll();
        $this->template->skupinaB = '';
    }
}
