<?php
/*********************************************************************
 class.nav.php

 Navigation helper classes. Pointless BUT helps keep navigation clean and free from errors.

 Peter Rotich <peter@osticket.com>
 Copyright (c)  2006-2010 osTicket
 http://www.osticket.com

 Released under the GNU General Public License WITHOUT ANY WARRANTY.
 See LICENSE.TXT for details.

 vim: expandtab sw=4 ts=4 sts=4:
 $Id: class.nav.php,v 1.2 2011/10/24 21:27:34 root Exp $
 **********************************************************************/
class StaffNav {
	var $tabs=array();
	var $submenu=array();

	var $activetab;
	var $ptype;

	function StaffNav($pagetype='staff'){
		global $thisuser;

		$this->ptype=$pagetype;
		$tabs=array();
		if($thisuser->isAdmin() && strcasecmp($pagetype,'admin')==0) {
			$desc = translate("LABEL_DASHBOARD");
			$title = translate("LABEL_ADMIN_DASHBOARD");
			$tabs['dashboard']=array('desc'=>$title,'href'=>'admin.php?t=dashboard','title'=>$tile);
			$desc = translate("LABEL_SETTINGS");
			$title = translate("LABEL_SYSTEM_SETTINGS");
			$tabs['settings']=array('desc'=>$desc,'href'=>'admin.php?t=settings','title'=>$title);
			$desc = translate('LABEL_EMAILS');
			$title = translate('LABEL_EMAILS_SETTINGS');
			$tabs['emails']=array('desc'=>$desc,'href'=>'admin.php?t=email','title'=>$title);
			$desc = translate('LABEL_HELP_TOPICS');
			$title = translate('LABEL_HELP_TOPICS');
			$tabs['topics']=array('desc'=>$desc,'href'=>'admin.php?t=topics','title'=>$title);
			$desc = translate('LABEL_STAFF');
			$title = translate('LABEL_STAFF_MEMBERS');
			$tabs['staff']=array('desc'=>$desc,'href'=>'admin.php?t=staff','title'=>$title);
			$desc = translate('LABEL_LAYOUT');
                        $title = translate('LABEL_LAYOUT'); 
                        $tabs['layout']=array('desc'=>$desc,'href'=>'admin.php?t=layout','title'=>$title);
		}else {
		        $desc = translate("LABEL_TICKETS");
			$tabs['tickets']=array('desc'=>$desc,'href'=>'tickets.php','title'=>'Ticket Queue');
			if($thisuser && $thisuser->canManageKb()){
				$desc = translate('LABEL_KNOWLEDGE_BASE');
				$title = translate('LABEL_KNOWLEDGE_BASE_PREMATE');
				$tabs['kbase']=array('desc'=>$desc,'href'=>'kb.php','title'=>$title);
			}
			$desc = translate('LABEL_DIRECTORY');
			$title = translate('LABEL_DIRECTORY_STAFF');
			$tabs['directory']=array('desc'=>$desc,'href'=>'directory.php','title'=>$title);
			$labelRules = translate("TEXT_RULES");
			$tabs['rules']=array('desc'=>$labelRules,'href'=>'rules.php','title'=>'Rules');
			$desc = translate('LABEL_MY_ACCOUNT');
			$title = translate('LABEL_MY_ACCOUNT');
			$tabs['profile']=array('desc'=>$desc,'href'=>'profile.php','title'=>$title);
		}
		$this->tabs=$tabs;
	}


	function setTabActive($tab){

		if($this->tabs[$tab]){
			$this->tabs[$tab]['active']=true;
			if($this->activetab && $this->activetab!=$tab && $this->tabs[$this->activetab])
			$this->tabs[$this->activetab]['active']=false;
			$this->activetab=$tab;
			return true;
		}
		return false;
	}

	function addSubMenu($item,$tab=null) {

		$tab=$tab?$tab:$this->activetab;
		$this->submenu[$tab][]=$item;
	}



	function getActiveTab(){
		return $this->activetab;
	}

	function getTabs(){
		return $this->tabs;
	}

	function getSubMenu($tab=null){

		$tab=$tab?$tab:$this->activetab;
		return $this->submenu[$tab];
	}

}
?>
