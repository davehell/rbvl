<?php

namespace App\Presenters;

use App;

final class DefaultPresenter extends BasePresenter
{
    /** @var App\Model\Stranky */
    private $stranky;

    /** @var App\Model\Aktuality */
    private $aktuality;

    /** @var App\Model\Terminy */
    private $terminy;

    /** @var App\Model\Tabulky */
    private $tabulky;

    public function __construct(
        App\Model\Tabulky $tabulky,
        App\Model\Terminy $terminy,
        App\Model\Aktuality $aktuality,
        App\Model\Stranky $stranky
    )
    {
        $this->tabulky = $tabulky;
        $this->terminy = $terminy;
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

        $idLigaA = 44;
        $idLigaB = 45;

        $this->template->tabulkyA = $this->tabulky->getTabulky($idLigaA);
        $this->template->skupinaA = '';
        $this->template->tabulkyB = $this->tabulky->getTabulky($idLigaB);
        $this->template->skupinaB = '';

        $this->template->aktualniKoloA = $this->terminy->aktualniKolo($idLigaA);
        $this->template->pristiKoloA = $this->terminy->pristiKolo($idLigaA);
        $this->template->aktualniKoloB = $this->terminy->aktualniKolo($idLigaB);
        $this->template->pristiKoloB = $this->terminy->pristiKolo($idLigaB);

        $this->template->aktuality = $this->aktuality->findAllDateSorted()->limit(3);
	}
}
