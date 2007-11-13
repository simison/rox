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
$words = new MOD_words();



?>

<table class="full">
<tr><td class="info">
<div class="subcolumns main_preposts">
<div class="c25l">
<div class="subc">

<?php
// Display the last created members with a picture
	echo "				<h3>",$words->getFormatted('RecentMember'),"</h3>\n"; 
	echo "				<p class=\"floatbox UserpicFloated\">";
/*	echo LinkWithPicture($m->Username,$m->photo), LinkWithUsername($m->Username),"<br />",$m->countryname ; */
	echo "				</p>\n"; 
	echo "			    </div>\n"; 
	echo "			  </div>\n"; 
	echo "			  <div class=\"c75r\">\n"; 
	echo "			  <h3>",$words->getFormatted('RecentVisitsOfyourProfile'),"</h3>\n"; 
	
	$DivForVisit[0]='c33l' ;
	$DivForVisit[1]='c33l' ;
	$DivForVisit[2]='c33r' ;
// /*###   NEW   To be programmed: show the first visitor, then the second. !! Different div's (c50l, c50r)!  ###
	$T=MOD_visits::get();
	$TVisits=$T->BuildLastVisits() ;
	for ($ii=0;$ii<count($TVisits);$ii++) {
			$m=$TVisits[$ii] ;
			echo "				  <div class=\"",$DivForVisit[$ii],"\">\n"; 
			echo "				    <div class=\"subc\">\n"; 
			echo "					<p class=\"floatbox UserpicFloated\">";
			// FIXME
			// echo LinkWithPicture($m->Username,$m->photo), LinkWithUsername($m->Username),"<br />",$m->countryname ;
			echo "				</p>\n"; 
			echo "					</div>\n"; 
			echo "				  </div>\n"; 
	} // end of for $ii on visits
	/* 
	echo "				  <div class=\"c50r\">\n"; 
	echo "				    <div class=\"subcr\">\n"; 
	echo "					 <p class=\"floatbox\"><img src=\"images/et.gif\" width=\"50\" height=\"50\" border=\"0\" alt=\"\" align=\"top\"  class=\"float_left framed\"><a href=\"#\" class=\"username\">maplefanta</a><br />from Oberschwanbach in USA:<br /> <q>I love BeWelcome</q></p>		\n"; 
	echo "					</div>\n"; 
	echo "				  </div>\n"; 
*/
	echo "			  </div>\n"; 
	echo "		</div>\n";

// OLD DEACTIVATED Display the max last three visits
/*
	for ($ii=0;$ii<count($TVisits);$ii++) {
	$m=$TVisits[$ii] ;
	echo "<td class=\"memberlist\" align=center>";
   echo LinkWithPicture($m->Username,$m->photo);
	echo "<br />" ;
	echo LinkWithUsername($m->Username), "<br />";
	echo $m->countryname, "</td> ";
	  
	} // end of for $ii on visits
*/	

// news	
?>
<div class="subcolumns main_posts">
<div class="subc">
<div id="content">
<h3><?php echo $words->get('News'); ?></h3>
     
<?php
	//Notice: Undefined variable: newscount in /var/www/bewelcome/templates/apps/rox/mainpage.php 
	for ($ii=$newscount;$ii>0;$ii--) {
		echo "<p class=\"news\"><a href=\"#\">",$words->get('NewsTitle_'.$ii),"</a><span class=\"small grey\">&nbsp;&nbsp;  |&nbsp; ",newsdate("NewsTitle_".$ii),"</span></p><p>",$words->get('NewsText_'.$ii),"</p>\n"; 
	}
?>

</div>
</div>
</div>

<?php /* echo "<p><a href=\"#\">",$words->get('MoreNews'),"</a></p>\n";  */ ?>

</td>
</tr>
</table>
