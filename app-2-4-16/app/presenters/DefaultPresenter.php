<?php

namespace App\Presenters;


final class DefaultPresenter extends BasePresenter
{
	public function renderDefault()
	{
        $this->template->pageTitle = '„Region Beskydy“ volejbalová liga';
        $this->template->pageHeading = '„Region Beskydy“ volejbalová liga';
        $this->template->pageDesc = '„Region Beskydy“ volejbalová liga je dlouhodobá soutěž hrající se ve Frýdku-Místku.';
        $this->template->scripts = array('lightbox');

        $this->template->text = '';
        // $stranky = new Stranky;
        // $stranka = $stranky->findByNazev('uvod')->fetch();
        // if($stranka) $this->template->text = $stranka->text;

        // $idLigaA = 42;
        // $idLigaB = 43;
        // $tabulky = new Tabulky;
        // $this->template->tabulkyA = $tabulky->getTabulky($idLigaA)->fetchAll();
        // $this->template->skupinaA = '';
        // $this->template->tabulkyB = $tabulky->getTabulky($idLigaB)->fetchAll();
        // $this->template->skupinaB = '';
        // $terminy = new Terminy;
        // $this->template->aktualniKoloA = $terminy->aktualniKolo($idLigaA)->fetch();
        // $this->template->pristiKoloA = $terminy->pristiKolo($idLigaA)->fetch();
        // $this->template->aktualniKoloB = $terminy->aktualniKolo($idLigaB)->fetch();
        // $this->template->pristiKoloB = $terminy->pristiKolo($idLigaB)->fetch();

        // $aktuality = new Aktuality;
        // $this->template->aktuality = $aktuality->findAll(array('vlozeno' => 'desc'), 3);
	}
}
