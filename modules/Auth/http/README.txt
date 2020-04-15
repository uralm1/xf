Dataface Web Application Framework: CAS Module
Copyright (C) 2005-2006  Steve Hannah (shannah@sfu.ca)

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.


About
-----

This module extends Dataface to allow its applications to use standard HTTP
authentication.  This can be handy when you have machine services accessing
your dataface application and you need a more standard way to handle
authentication.

Installation
-------------

1. Download the HTTP module and extract the contents of the tarball into
    your dataface/modules directory.  You should have a directory path
	somewhat like the following:

    	%DATAFACE_PATH%/modules/Auth/http/...

2. Add the following section to your application's conf.ini file.
   [_auth]
   auth_type = http
   users_table = "%name_of_your_users_table%"
   username_column = "%username_col%"
   password_column = ""

 	If you are already using authentication in your application, then you will
   have only added 1 new line:

   		auth_type : Set this to 'http' to indicate that you want to use the 'http' module.

  Please see the Getting Started with Dataface tutorial's section on permissions
     for more information about the '_auth' section of the conf.ini  file.
     (hhttp://xataface.com/documentation/tutorial/getting_started/permissions)
