<?php



/**
 * Users authenticator.
 */
class Users extends Object implements IAuthenticator
{

	/**
	 * Performs an authentication
	 * @param  array
	 * @return void
	 * @throws AuthenticationException
	 */
  public function authenticate(array $credentials)
  {
      $username = $credentials[self::USERNAME];
      $password = sha1($credentials[self::PASSWORD] . $credentials[self::USERNAME]);

      // přečteme záznam o uživateli z databáze
      $row = dibi::fetch('SELECT id, username, password, role FROM uzivatele WHERE username=%s', $username);

      if (!$row) { // uživatel nenalezen?
          throw new AuthenticationException("Chybně zadané přihlašovací jméno nebo heslo.", self::IDENTITY_NOT_FOUND);
      }

      if ($row->password !== $password) { // hesla se neshodují?
          throw new AuthenticationException("Chybně zadané přihlašovací jméno nebo heslo.", self::INVALID_CREDENTIAL);
      }

  		unset($row->password);
  		return new Identity($row->username, $row->role, $row); //vratime identitu
  }
}
