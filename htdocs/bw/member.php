<?php
/*

Copyright (c) 2007 BeVolunteer

This file is part of BW Rox.

BW Rox is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

BW Rox is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, see <http://www.gnu.org/licenses/> or 
write to the Free Software Foundation, Inc., 59 Temple Place - Suite 330, 
Boston, MA  02111-1307, USA.

*/
require_once "lib/init.php";
require_once "layout/error.php";
require_once "lib/prepare_profile_header.php";

// Find parameters
$IdMember = IdMember(GetParam("cid", ""));

if (empty($IdMember)) {
	if (IsLoggedIn()) {
	     $IdMember=$_SESSION["IdMember"]; // for case where there is no CID provide like when caming back from forum
	}
	else {
		 $errcode = "ErrorWithParameters";
		 DisplayError(ww("ErrorWithParameters", "\$IdMember is not defined"));
		 exit (0);
	}
}
// If user is not logged test if the profile is publib, if not force to log
if ((!IsLoggedIn()) and (!IsPublic($IdMember))) {
	MustLogIn();
} 


$photorank=GetParam("photorank",0);
switch (GetParam("action")) {
	case "previouspicture" :
		$photorank--;
		if ($photorank < 0) {
	  	    $rr=LoadRow("select SQL_CACHE * from membersphotos where IdMember=" . $IdMember . " order by SortOrder desc limit 1");
			if (isset($rr->SortOrder)) $photorank = $rr->SortOrder;
			else $photorank=0;
		}
		break;
	case "nextpicture" :
		$photorank++;
		break;
	case "logout" :
		Logout("main.php");
		exit (0);
}

$m = prepareProfileHeader($IdMember,null,$photorank);

// Try to see how many language this members has used 
$m->CountTrad=0;
$m->Trad = array ();
$str="SELECT DISTINCT (memberstrads.IdLanguage),languages.ShortCode FROM memberstrads,languages WHERE memberstrads.IdLanguage=languages.id and IdOwner=".$IdMember; 
$qry = mysql_query($str);
while ($rr = mysql_fetch_object($qry)) {
	array_push($m->Trad, $rr);
	$m->CountTrad++;
}

// Try to load specialrelations and caracteristics belong to
$Relations = array ();
$str = "select SQL_CACHE specialrelations.*,members.Username as Username,members.Gender as Gender,members.HideGender as HideGender,members.id as IdMember from specialrelations,members where IdOwner=".$IdMember." and specialrelations.Confirmed='Yes' and members.id=specialrelations.IdRelation and members.Status='Active'";
$qry = mysql_query($str);
while ($rr = mysql_fetch_object($qry)) {
	if ((!IsLoggedIn()) and (!IsPublic($rr->IdMember))) continue; // Skip non public profile is is not logged

	$rr->Comment=FindTrad($rr->Comment,true);
   $photo=LoadRow("select SQL_CACHE * from membersphotos where IdMember=" . $rr->IdRelation . " and SortOrder=0");
	if (isset($photo->FilePath)) $rr->photo=$photo->FilePath; 
	array_push($Relations, $rr);
}
$m->Relations=$Relations;

// Try to load groups and caracteristics where the member belong to
$str = "select SQL_CACHE membersgroups.Comment as Comment,groups.Name as Name,groups.id as IdGroup from groups,membersgroups where membersgroups.IdGroup=groups.id and membersgroups.Status='In' and membersgroups.IdMember=" . $m->id;
$qry = mysql_query($str);
$TGroups = array ();
while ($rr = mysql_fetch_object($qry)) {
	array_push($TGroups, $rr);
}

// Load phone
if ($m->HomePhoneNumber > 0) {
	$m->DisplayHomePhoneNumber = PublicReadCrypted($m->HomePhoneNumber, ww("Hidden"));
}
if ($m->CellPhoneNumber > 0) {
	$m->DisplayCellPhoneNumber = PublicReadCrypted($m->CellPhoneNumber, ww("Hidden"));
}
if ($m->WorkPhoneNumber > 0) {
	$m->DisplayWorkPhoneNumber = PublicReadCrypted($m->WorkPhoneNumber, ww("Hidden"));
}

if ($m->Restrictions == "") {
	$m->TabRestrictions = array ();
} else {
	$m->TabRestrictions = explode(",", $m->Restrictions);
}

if ($m->OtherRestrictions > 0)
	$m->OtherRestrictions = FindTrad($m->OtherRestrictions,true);
else
	$m->OtherRestrictions = "";

if (IsLoggedIn()) {
	// check if the member is in mycontacts
	$rr=LoadRow("select SQL_CACHE * from mycontacts where IdMember=".$_SESSION["IdMember"]." and IdContact=".$IdMember);
	if (isset($rr->id)) {
	   $m->IdContact=$rr->id; // The note id
	}	
	else {
	   $m->IdContact=0; // there is no note
	}	

	// check if wether this profile has a special realtion
	$rr=LoadRow("select SQL_CACHE * from specialrelations where IdOwner=".$_SESSION["IdMember"]." and IdRelation=".$IdMember);
	if (isset($rr->IdRelation)) {
	   $m->IdRelation=$rr->IdRelation; // The note id
	}	
	else {
	   $m->IdRelation=0; // there is no note
	}	
}
	
// Load the language the members nows
$TLanguages = array ();
$str = "SELECT SQL_CACHE memberslanguageslevel.IdLanguage AS IdLanguage,languages.Name AS Name, " .
		"memberslanguageslevel.Level AS Level FROM memberslanguageslevel,languages " .
		"WHERE memberslanguageslevel.IdMember=" . $m->id . 
		" AND memberslanguageslevel.IdLanguage=languages.id AND memberslanguageslevel.Level != 'DontKnow'";

$qry = mysql_query($str);
while ($rr = mysql_fetch_object($qry)) {
	$rr->Level=ww("LanguageLevel_".$rr->Level);   
	array_push($TLanguages, $rr);
}
$m->TLanguages = $TLanguages;

// Make some translation to have blankstring in case records are empty
$m->ILiveWith = FindTrad($m->ILiveWith,true);
$m->MaxLenghtOfStay = FindTrad($m->MaxLenghtOfStay,true);
$m->MotivationForHospitality = FindTrad($m->MotivationForHospitality,true);
$m->Offer = FindTrad($m->Offer,true);
$m->Organizations = FindTrad($m->Organizations,true);
$m->AdditionalAccomodationInfo = FindTrad($m->AdditionalAccomodationInfo,true);
$m->InformationToGuest = FindTrad($m->InformationToGuest,true);
$m->Hobbies = FindTrad($m->Hobbies,true);
$m->Books = FindTrad($m->Books,true);
$m->Music = FindTrad($m->Music,true);
$m->Movies = FindTrad($m->Movies,true);
$m->PleaseBring = FindTrad($m->PleaseBring,true);
$m->OfferGuests = FindTrad($m->OfferGuests,true);
$m->OfferHosts = FindTrad($m->OfferHosts,true);
$m->PublicTransport = FindTrad($m->PublicTransport,true);
$m->PastTrips = FindTrad($m->PastTrips,true);
$m->PlannedTrips = FindTrad($m->PlannedTrips,true);

if (stristr($m->WebSite,"http://") === FALSE &&
	stristr($m->WebSite,"https://") === FALSE &&
	strlen(trim($m->WebSite))>0)
	$m->WebSite = "http://".$m->WebSite;
	
// see if the visit of the profile need to be logged
if (IsLoggedIn() and 
	($IdMember != $_SESSION["IdMember"]) and 
	($_SESSION["Status"] != "ActiveHidden")) { // don't log ActiveHidden visits or visit on self profile

	$str="replace into profilesvisits(IdMember,IdVisitor,updated) values(".$m->id.",".$_SESSION["IdMember"].",now())" ;
	sql_query($str);
}

require_once "layout/member.php";
DisplayMember($m, $m->profilewarning, $TGroups,CanTranslate($IdMember));
?>
