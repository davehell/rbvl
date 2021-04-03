<?php
namespace App\Model;

use Nette;

class AuthorizatorFactory
{
    public static function create(): Nette\Security\Permission
    {
        $acl = new Nette\Security\Permission;
        $acl->addRole('guest');
        $acl->addRole('member');
        $acl->addRole('admin');

        $acl->addResource('Default');
        $acl->addResource('Diskuze');
        $acl->addResource('Pravidla');
        $acl->addResource('Propozice');
        $acl->addResource('Download');
        $acl->addResource('Uzivatele');
        $acl->addResource('Aktuality');
        $acl->addResource('Akce');
        $acl->addResource('Druzstva');
        $acl->addResource('Kontakty');
        $acl->addResource('Partneri');
        $acl->addResource('Dary');
        $acl->addResource('Rozlosovani');
        $acl->addResource('Error');
        $acl->addResource('Vysledky');
        $acl->addResource('Foto');
        $acl->addResource('Alba');
        $acl->addResource('Hraci');
        $acl->addResource('Stranky');
        $acl->addResource('Gdpr');
        $acl->addResource('Auth');

        $acl->allow('admin'); // admin muze vse

        $acl->allow('member');
        $acl->deny('member', 'Uzivatele', array('add', 'edit', 'delete'));

        $acl->allow('guest', 'Auth');
        $acl->allow('guest', 'Error');
        $acl->allow('guest', $acl::ALL, 'default');
        $acl->allow('guest', 'Diskuze', 'add');
        $acl->allow('guest', 'Akce', 'add');
        $acl->allow('guest', 'Vysledky', 'termin');
        $acl->allow('guest', 'Vysledky', 'tabulky');
        $acl->allow('guest', 'Foto', 'album');
        $acl->allow('guest', 'Foto', 'download');
        $acl->allow('guest', 'Druzstva', 'soupiska');

        $acl->deny('guest', 'Uzivatele', $acl::ALL);
        $acl->deny('guest', 'Hraci', $acl::ALL);
        $acl->deny('guest', 'Stranky', $acl::ALL);

        return $acl;
    }
}
