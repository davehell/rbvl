<?php
class ACL extends Permission
{
    function  __construct()  {
        $this->addRoles(); // add roles
        $this->addResources(); // add all resources

        $this->allow('admin'); // admin muze vse

        $this->allow('member');
        $this->deny('member', 'Uzivatele', array('add', 'edit', 'delete'));

        $this->allow('guest', Permission::ALL, 'default');
        $this->allow('guest', 'Diskuze', 'add');
        $this->allow('guest', 'Akce', 'add');
        $this->allow('guest', 'Vysledky', 'termin');
        $this->allow('guest', 'Vysledky', 'tabulky');
        $this->allow('guest', 'Foto', 'album');
        $this->allow('guest', 'Foto', 'download');
        $this->allow('guest', 'Druzstva', 'soupiska');

        $this->deny('guest', 'Uzivatele', Permission::ALL);
        $this->deny('guest', 'Hraci', Permission::ALL);
        $this->deny('guest', 'Stranky', Permission::ALL);
    }

    /**
     * Add all Resources
     */
    function addResources() {
        $this->addResource('Default');
        $this->addResource('Diskuze');
        $this->addResource('Pravidla');
        $this->addResource('Propozice');
        $this->addResource('Download');
        $this->addResource('Uzivatele');
        $this->addResource('Aktuality');
        $this->addResource('Akce');
        $this->addResource('Druzstva');
        $this->addResource('Kontakty');
        $this->addResource('Partneri');
        $this->addResource('Rozlosovani');
        $this->addResource('Error');
        $this->addResource('Vysledky');
        $this->addResource('Foto');
        $this->addResource('Alba');
        $this->addResource('Hraci');
        $this->addResource('Stranky');
    }

    /**
     * Add all roles
     */
    function addRoles()
    {
        $this->addRole('guest');
        $this->addRole('member');
        $this->addRole('admin');
    }
}
