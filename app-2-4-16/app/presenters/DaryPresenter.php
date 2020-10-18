<?php

namespace App\Presenters;

final class DaryPresenter extends BasePresenter
{
    public function renderDefault()
    {
        $this->template->pageTitle = '„RB“VL - Dotace a dary';
        $this->template->pageDesc = 'Dotace a dary pro „Region Beskydy“ volejbalovou ligu';
        $this->template->pageHeading = 'Dotace a dary';

        $this->template->text = '';
        // $stranky = new Stranky;
        // $stranka = $stranky->findByNazev('dary')->fetch();
        // if($stranka) $this->template->text = $stranka->text;
    }
}
