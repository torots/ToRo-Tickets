<?php
/*********************************************************************
    secure.inc.php

    File included on every client's "secure" pages

    Original File by Peter Rotich <r@toroTS.com>
    Copyright (c)  2006-2011 toroTS
    http://www.torots.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
    $Id: secure.inc.php,v 1.2 2011/08/20 14:17:08 ntozier Exp $
**********************************************************************/
if(!strcasecmp(basename($_SERVER['SCRIPT_NAME']),basename(__FILE__))) die('Kwaheri rafiki!');
if(!file_exists('client.inc.php')) die('Fatal Error.');
require_once('client.inc.php');
//User must be logged in!
if(!$thisclient || !$thisclient->getId() || !$thisclient->isValid()){
    require('./login.php');
    exit;
}
$thisclient->refreshSession();
?>
