<?php
/*
This file is part of the MyBBClanSuite.

    The MyBBClanSuite is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    The MyBBClanSuite is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with The MyBBClanSuite.  If not, see <http://www.gnu.org/licenses/>.
	*/

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

function roster_meta()
{
	// get access to everything we want
	global $page, $lang, $plugins, $db;

	if($db->table_exists("rosterteams"))
	{	// plugin installed, so show this module's link
		// add_menu_item(title, name, link, display order, submenus)
		return true;
	}
	// I assume returning false means "don't do anything"
	// no adverse effects so far.
	return false;
}

function roster_action_handler($action)
{
	global $db, $page, $lang, $plugins;
	
	
	//Check number of rows
	$teams_query 	= $db->query("SELECT * FROM " . TABLE_PREFIX . "rosterteams");
	$teams_num		= $db->num_rows($teams_query);
	
	// our module's name
	$page->active_module = "roster";
	
	// the available actions and their pages
	$actions = array(
		'addteam' => array('active' => 'addteam', 'file' => 'addteam.php'),
		'manage' => array('active' => 'manage', 'file' => 'manage.php'),
		'addplayer' => array('active' => 'addplayer', 'file' => 'addplayer.php'),
	);
	
	if(!isset($actions[$action]))
	{
		if($teams_num > 0)
		{
			$page->active_action	=	"manage";
		}
		else
		{
			$page->active_action	=	"addteam";
		}
	}
	else
	{
		$page->active_action = $actions[$action]['active'];
	}
	// more custom plugin hooks!
	//$plugins->run_hooks_by_ref("admin_roster_action_handler", $actions);
	
	if($page->active_action == "manage" || $page->active_action == "addteam" || $page->active_action == "addplayer")
	{
	// this is a list of sub menus
	$sub_menu = array();
	$sub_menu['10'] = array("id" => "addteam", "title" => "Create Team", "link" => "index.php?module=roster/addteam");
	$sub_menu['20'] = array("id" => "manage", "title" => "Manage Teams", "link" => "index.php?module=roster/manage");
	$sub_menu['30'] = array("id" => "addplayer", "title" => "Add Player", "link" => "index.php?module=roster/addplayer");
	
	$sidebar = new SidebarItem("Roster Manager");
	$sidebar->add_menu_items($sub_menu, $page->active_action);

	$page->sidebar .= $sidebar->get_markup();
	}
	
	if(isset($actions[$action]))
	{	// set the action and return the page
		$page->active_action = $actions[$action]['active'];
		return $actions[$action]['file'];
	}
	else
	{
		// return the default page
		if ($teams_num > 0)
		{
			$page->active_action = "manage";
			return "manage.php";
		}
		else
		{
			$page->active_action = "addteam";
			return "addteam.php";
		}
		
	}
}