<?php
/**
 * External Authentication via HTTP_AUTH_USER header
 * (c) Ural Khassanov, 2018
 */     
class dataface_modules_preauth {

  function authenticate() {
    $auth =& Dataface_AuthenticationTool::getInstance();
    if ($this->checkCredentials()) {
      $creds = $this->getCredentials();
      Dataface_Application::getInstance()->startSession();
      $_SESSION['UserName'] = $creds['UserName'];
        return true;
      } else {
	return PEAR::raiseError('No credentials were supplied by external authentication system.', DATAFACE_E_REQUEST_NOT_HANDLED);
      }
  }

  function getCredentials() {
    $username = (isset($_SERVER['HTTP_AUTH_USER']) ? $_SERVER['HTTP_AUTH_USER'] : null);
    if ( !@$username) return null;
    #$username = 'av';
    return array('UserName'=>$username, 'Password'=>null);
  }

  function checkCredentials() {
    return isset($_SERVER['HTTP_AUTH_USER']);
  }

  /**
   * Returns the username of the user who is currently logged in.  If no user
   * is logged in it returns null.
   */
  function getLoggedInUsername() {
    if ( !@$_SESSION['UserName'] ) return null;
    return @$_SESSION['UserName'];	
  }

}
