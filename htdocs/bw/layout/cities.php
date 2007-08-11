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


require_once ("menus.php");

function DisplayCountries($TList,$where) {
	global $title;
	$title = ww('Cities');
	require_once "header.php";

	Menu1("cities.php", ww('Cities')); // Displays the top menu

	Menu2($_SERVER["PHP_SELF"]);

	DisplayHeaderWithColumns(ww('Cities')); // Display the header

  echo "<div class=\"info\">\n";
  echo "<p class=\"navlink\">\n";
	echo "<a href=\"countries.php\">",ww("countries"),"</a> > ";
	echo "<a href=\"regions.php?IdCountry=",$where->IdCountry,"\">",$where->CountryName,"</a> > ";
	echo "<a href=\"cities.php?IdRegion=",$where->IdRegion,"\">",$where->RegionName,"</a> > ";
  echo "</p>\n";	
	echo "<ul>\n";

	$iiMax = count($TList);
	for ($ii = 0; $ii < $iiMax; $ii++) {
		echo "<li>";
		echo $TList[$ii]->city, " <a href=\"findpeople.php?action=Find&IdCity=",$TList[$ii]->IdCity,"\">" ;
		echo "(",$TList[$ii]->cnt, ")" ;
		echo "</a>";
		echo "</li>\n";
	}
	echo "</ul>\n";
	echo "</div>\n";

	require_once "footer.php";
}
?>
