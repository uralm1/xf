<?php
/*-------------------------------------------------------------------------------
 * Dataface Web Application Framework
 * Copyright (C) 2005-2006  Steve Hannah (shannah@sfu.ca)
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *-------------------------------------------------------------------------------
 */

/**
 *<p>This module extends Dataface to allow its applications to use Standard
 *	http authenticaiton. </p>
 * 
 *
 * @author Steve Hannah (shannah@sfu.ca)
 * @created March 7, 2007
 * @version 0.1
 */     
class dataface_modules_http {

	function authenticate(){
		$auth =& Dataface_AuthenticationTool::getInstance();
		if ( $auth->checkCredentials()){
			$creds = $this->getCredentials();
			Dataface_Application::getInstance()->startSession();
			$_SESSION['UserName'] = $creds['UserName'];
			return true;
		} else {
			return PEAR::raiseError('No credentials were included in the URL.  Proceed as normal.', DATAFACE_E_REQUEST_NOT_HANDLED);
			
		}
		
	}

	function getCredentials(){
	
		$username = ( isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : null);
		$password = ( isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : null);
		if ( !@$username) return null;
		return array('UserName'=>$username, 'Password'=>$password);
	}

	/**
	 * Overrides the default Login Prompt to use the CAS login.
	 */
	function showLoginPrompt(){
		$auth =& Dataface_AuthenticationTool::getInstance();
		$creds = $auth->getCredentials();
		$app =& Dataface_Application::getInstance();
		if ( !$auth->checkCredentials() ){
			$realm = ( isset($auth->conf['realm']) ? $auth->conf['realm'] : 'Dataface Application');
			header('WWW-Authenticate: Basic realm="'.$realm.'"');
			header('HTTP/1.0 401 Unauthorized');
			echo 'Text to send if user hits Cancel button';
			exit;
		} else {
			Dataface_Application::getInstance()->startSession();
			$_SESSION['UserName'] = $creds['UserName'];
			Dataface_Application::getInstance()->display();
			exit;
			//header('Location: '.$app->url(array()));
			//exit;
		}
	}
	
	/**
	 * Returns the username of the user who is currently logged in.  If no user
	 * is logged in it returns null.
	 */
	function getLoggedInUsername(){
		if ( !@$_SESSION['UserName'] ) return null;
		return @$_SESSION['UserName'];	
	}
	

}