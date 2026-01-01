<?php
/**
*  This file is part of the WCL project
*
*  Copyright (c) 2004-2007 Jose Leon Serna <support@qadram.com>
*
*  This library is free software; you can redistribute it and/or
*  modify it under the terms of the GNU Lesser General Public
*  License as published by the Free Software Foundation; either
*  version 2.1 of the License, or (at your option) any later version.
*
*  This library is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
*  Lesser General Public License for more details.
*
*  You should have received a copy of the GNU Lesser General Public
*  License along with this library; if not, write to the Free Software
*  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307
*  USA
*
*/
   require_once("vcl/vcl.inc.php");
   use_unit("designide.inc.php");

   setPackageTitle("Extra Components - JAB");
   setIconPath("./icons");

   registerComponents("Samples",array("RawOutput"),"rawoutput.inc.php");
   registerComponents("Samples",array("UserLogin"),"userlogin.inc.php");
   registerComponents("Samples",array("RawInclude"),"rawinclude.inc.php");

   registerPropertyValues("UserLogin","Database",array('Database'));

?>
