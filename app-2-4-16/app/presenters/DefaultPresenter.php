<?php

namespace App\Presenters;

use App;

final class DefaultPresenter extends BasePresenter
{
    /** @var App\Model\Stranky */
    private $stranky;

    /** @var App\Model\Aktuality */
    private $aktuality;
    // private $tabulky;
    // private $terminy;

    public function __construct(
        // App\Model\Tabulky $tabulky,
        // App\Model\Terminy $terminy,
        App\Model\Aktuality $aktuality,
        App\Model\Stranky $stranky
    )
    {
        // $this->tabulky = $tabulky;
        // $this->terminy = $terminy;
        $this->aktuality = $aktuality;
        $this->stranky = $stranky;
    }

	public function renderDefault()
	{
        $this->template->pageTitle = '„Region Beskydy“ volejbalová liga';
        $this->template->pageHeading = '„Region Beskydy“ volejbalová liga';
        $this->template->pageDesc = '„Region Beskydy“ volejbalová liga je dlouhodobá soutěž hrající se ve Frýdku-Místku.';
        $this->template->scripts = array('lightbox');

        $this->template->text = '';
        $stranka = $this->stranky->getByNazev("uvod");
        if($stranka) $this->template->text = $stranka->text;

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

        $this->template->aktuality = $this->aktuality->findAllDateSorted()->limit(3);
	}
}
