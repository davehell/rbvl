<?php

require_once dirname(__FILE__) . '/BasePresenter.php';

class DownloadPresenter extends BasePresenter
{
    public function renderDefault()
    {
        $this->template->pageTitle = '„RB“VL - Ke stažení';
        $this->template->pageDesc = '„Region Beskydy“ volejbalová liga - Dokumenty ke stažení';
        $this->template->pageHeading = 'Ke stažení';

        $this->template->files = array(
          'download/prihlaska_rbvl_1516.doc'  => 'Přihláška pro ročník 2015-2016',
          'download/soupiska_rbvl_1516.doc'   => 'Soupiska pro ročník 2015-2016',
          'download/archiv_rbvl_0910.zip'     => 'Výsledky a tabulky 1. ročníku 2009-2010',
          'download/archiv_rbvl_1011.zip'     => 'Výsledky a tabulky 2. ročníku 2010-2011',
          'download/archiv_rbvl_1112.zip'     => 'Výsledky a tabulky 3. ročníku 2011-2012',
          'download/archiv_rbvl_1213.zip'     => 'Výsledky a tabulky 4. ročníku 2012-2013',
          'download/archiv-rbvl-1314.zip'     => 'Výsledky a tabulky 5. ročníku 2013-2014',
          'download/archiv-rbvl-1415.zip'     => 'Výsledky a tabulky 6. ročníku 2014-2015'
        );
    }
}
