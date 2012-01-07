<?php
/*********************************************************************
    captcha.php

    Simply returns captcha image.
    
    r h <p@torots.com>
    Copyright (c)  2006-2011 toroTS
    http://www.torots.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
    $Id: captcha.php,v 1.1 2011/08/20 13:53:35 ntozier Exp $
**********************************************************************/
require_once('main.inc.php');
require(INCLUDE_DIR.'class.captcha.php');

$captcha = new Captcha(5,12,ROOT_DIR.'images/captcha/');
echo $captcha->getImage();
?>
