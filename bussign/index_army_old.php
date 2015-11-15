<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<link rel="stylesheet" type="text/css" href="army.css"/>
	<script type='text/javascript' src='jquery-2.1.3.min.js'></script>
	<script type='text/javascript' src='underscore.min.js'></script>
	<title>MARTA Army | TimelyTrip</title>
</head>
<body>

<?php

date_default_timezone_set('America/New_York');

$default_effective_date = "08AUG2015";
$default_expiry_date = "11DEC2015";

$stopId1 = $_REQUEST['agency'] . "_" . $_REQUEST['sid'];
$stopId2 = "";
if (isset($_REQUEST['sid2']) && $_REQUEST['sid2'] != "") {
	$stopId2 = $_REQUEST['agency2'] . "_" . $_REQUEST['sid2'];
}

$stopIds = array(
	array($stopId1, $stopId2)
	//array('MARTA', '901441')
);

// Syntax:
// <Origin> to <Destination> via <place1>, <place2>, <place3>...
// In the signs, " to " will be replaced by an arrow, and anything after 'via ' will be shown in light characters.

// Rd. and Dr. are optional.
// Do not use dots after abbreviations like Rd, Dr, Blvd if you use these.
// Use dots after Hosp., Shop., N.
// Use Station in the " to " only, and if space is available. Spell out Station.
// Do not use 'and', 'station' in the 'via' section except Atlantic Station


$directionData = array(
'Rainbow Dr./Snapfinger Rd' => 'xxx',
'Route - 110 Lenox Station' => 'Five Points to Lenox Station via Peachtree Center, Fox Theatre, Arts Center, AMTRAK, Piedmont Hosp., Buckhead',
'Route 1- Coronet Way' => 'North Ave to Moores Mill Shop. Ctr via Centennial Park, Westside Provisions, Chattahoochee Ave/Marietta Blvd',
'Route 1- Five Points Station' => 'Moores Mill Shop. Ctr to North Ave Station via Chattahoochee Ave/Marietta Blvd, Westside Provisions, Centennial Park',
'Route 102- North Avenue Station' => 'Candler Park to North Ave Station via Caroline St, Little Five Pts, Freedom Park, Ponce City Mkt',
'Route 102-Edgewood/Candler Park Station' => 'North Ave to Candler Park Station via Ponce City Mkt, Little Five Pts, Caroline St',
'Route 103 - Chamblee Station' => 'Winters Chapel to Chamblee Station via Peeler Rd, N. Shallowford, Peachtree Indus.',
'Route 103 - Wniter Chapel Rd' => 'Chamblee to Winters Chapel via Peachtree Indus., N. Shallowford, Peeler Rd',
'Route 104 - Doraville Station' => 'Winters Chapel to Doraville Station via New Peachtree',
'Route 104 - Tilly Mill Road' => 'Doraville Station to Winters Chapel via New Peachtree, Woodwin Rd',
'Route 107 - Inman Park Station' => 'Kensington to Inman Park Station via Covington Hwy, Glenwood Ave, East Atlanta Village, Moreland Ave',
'Route 107 - Kensington Station' => 'Inman Park to Kensington Station via Moreland Ave, East Atlanta Village, Glenwood Ave, Covington Hwy',
'Route 110 - Five Points' => 'Lenox to Arts Center/Five Points via Buckhead, Piedmont Hosp., AMTRAK, Fox Theatre, Peachtree Center',
'Route 110 - Lenox Station' => 'Five Points to Lenox Station via Peachtree Center, Fox Theatre, Arts Center, AMTRAK, Piedmont Hosp., Buckhead',
'Route 110-Arts Center Station' => 'Lenox to Arts Center/Five Points via Peachtree, Buckhead, Piedmont Hosp., AMTRAK',
'Route 111 - Indian Creek Station' => 'Stonecrest Mall to Indian Creek via Hillandale Rd, Snapfinger Woods, Wesley Chapel, S. Indian Creek',
'Route 111 - Stone Crest Mall' => 'Indian Creek to Stonecrest Mall via S. Indian Creek, Wesley Chapel, Snapfinger Woods, Hillandale Rd',
'Route 111 - Stonecrest Mall' => 'Indian Creek to Stonecrest Mall via S. Indian Creek, Wesley Chapel, Snapfinger Woods, Hillandale Rd',
'Route 114 - Avondale Station' => 'Clifton Spr. Health Ctr to Avondale Station via Columbia High School, Eastgate Shop. Ctr, Columbia Dr',
'Route 114- Clifton Springs Health Center' => 'Avondale to Clifton Spr. Health Ctr via Columbia Dr, Eastgate Shop. Ctr, Columbia High School',
'Route 115- Indian Creek Station' => 'Lithonia Plaza to Indian Creek via Covington Hwy, S. Hairston Rd',
'Route 115- Main Street/Swift Street' => 'Indian Creek to Lithonia Plaza via S. Hairston Rd, Covington Hwy',
'Route 116 - Indian Creek Station' => 'Stonecrest Mall to Indian Creek via Lithonia Plaza, Redan Rd',
'Route 116 - Stonecrest Mall' => 'Indian Creek to Stonecrest Mall via Redan Rd, Lithonia Plaza',
'Route 117 - GRTA Park & Ride' => 'Kensington to Panola P/R via Rockbridge Rd, Panola Rd, Fairington Pkwy',
'Route 117 - Kensington Station' => 'Panola P/R to Kensington Station via Panola Rd, Rockbridge Rd',
'Route 119- Kensington Station' => 'Memorial Dr P/R to Kensington Station via N./S. Hairston Rd, Redan Rd, Indian Creek Station, Kensington Rd',
'Route 119- Memorial Drive Park -Ride' => 'Kensington to Memorial Dr P/R via Kensington Rd, Indian Creek Station, Redan Rd, S./N. Hairston Rd',
'Route 12 - Cumberland Mall' => 'Midtown to Cumberland Mall via 10th St, Howell Mill, Northside Pkwy',
'Howell Mill Rd/Cumberland' => 'Cumberland Mall to Midtown Station via Northside Pkwy, Howell Mill, 10th St',
'Route 12 - Midtown Station' => 'Cumberland Mall to Midtown Station via Northside Pkwy, Howell Mill, 10th St',
'Route 120 - Avondale Station' => 'Tucker to Avondale Station via Mountain Indus., E. Ponce, DeKalb Farmers Mkt',
'Route 120 - Greer Circle/Mountian Industrial Boulevard' => 'Avondale to Tucker via DeKalb Farmers Mkt, E. Ponce, Mountain Indus.',
'Route 121 - Kensington Station' => 'Stone Mountain to Kensington Station via N. Hairston Rd, Memorial Dr',
'Route 121- Memorial Drive Park-Ride' => 'Kensington to Memorial Dr P/R via Memorial Dr, N. Hairston Rd, Downtown Stone Mountain',
'Route 123 - Decatur Station' => 'N. DeKalb Mall - Belvedere via Church St, Decatur Station, McDonough St, Midway Rd',
'Route 124 - Doraville Station' => 'Tucker to Doraville via Chamblee-Tucker, Tucker-Norcross Rd, Pleasantdale, Oakcliff Rd',
'Route 124 Lawrenceville HIghway' => 'Doraville to Tucker via Oakcliff Rd, Pleasantdale, Tucker-Norcross Rd, Chamblee-Tucker Rd',
'Route 125 - Avondale Station' => 'N. Lake to Avondale Station via Montreal Rd, GPC Clarkston, N. Decatur',
'Route 125 - Northlake Mall' => 'Avondale to N. Lake Mall via N. Decatur, GPC Clarkston, Montreal Rd',
'Route 126 - Chamblee Station' => 'N. Lake to Chamblee Station via Henderson Mill, Mercer Univ., Chamblee-Tucker',
'Route 126 - Northlake Mall' => 'Chamblee to N. Lake Mall via Chamblee-Tucker, Mercer Univ., Henderson Mill',
'Route 126 - Parklake Dr' => 'Chamblee to N. Lake Mall via Chamblee-Tucker, Mercer Univ., Henderson Mill',
'Route 13 - Five Points Station' => 'West Lake to Five Points via Mozley Park, Fair St, Atlanta Univ Ctr, Castleberry Hill',
'Route 13 - West Lake Station' => 'Five Points to West Lake via Castleberry Hill, Atlanta Univ Ctr, Fair St, Mozley Park',
'Route 132 - Chamblee Station' => 'Dunwoody Club to Chamblee Station via Tilly Mill, GPC Dunwoody, N. Peachtree, Chamblee City Hall',
'Route 132 - Dunwoody Village' => 'Chamblee to Dunwoody Club via Chamblee City Hall, N. Peachtree, Tilly Mill, GPC Dunwoody',
'Route 140 - North Springs Station' => 'Windward/Old Milton to N. Springs via North Point Mall, Mansell P/R',
'Route 140 - Windward Park n Ride' => 'N. Springs to Windward OR Alpharetta City Hall via Mansell P/R, North Point Mall, [North Pt Pkwy or Haynes Br]',
'Route 140- Marrietta Street/Norcross Street' => 'N. Springs to Alpharetta City Hall via Mansell P/R, North Point Mall, Haynes Bridge, Old Milton',
'North Point/Mansell P/R' => 'Old Milton to N. Springs via Alpharetta City Hall, Haynes Br, North Pt Mall, Mansell P/R',
'Route 143 - North Springs Station' => 'Windward/Old Milton to N. Springs',
'Route 143 North Springs Station' => 'Windward to N. Springs',
'Route 143 Windward Park-Ride' => 'N. Springs to Windward P/R',
'Route 143 Windward Park-Ride Deerfield Parkway' => 'N. Springs to Windward P/R',
'Windward Park / Ride' => 'Windward to N. Springs', // Southbound afternoon trips on the 143.
'Route 148 Sandy Springs Station' => 'Riveredge to Sandy Springs Station via Mt Vernon',
'Route 148/ Riveredge Parkway' => 'Sandy Springs to Riveredge Pkwy via Mt Vernon',
'Route 15 - Decatur Station / Decatur Square' => 'Anvil Block/River Rd to Decatur Station via S. DeKalb Mall, Candler Rd, Agnes Scott',
'Route 15- Anvil Block Road' => 'Decatur to Anvil Block OR Linecrest Rd via Agnes Scott, Candler Rd, S. DeKalb Mall, [River Rd or Panthersville Rd]',
'Route 15- Linecrest Road' => 'Decatur to Anvil Block OR Linecrest Rd via Agnes Scott, Candler Rd, S. DeKalb Mall, [River Rd or Panthersville Rd]',
'Route 150 - Dunwoody Station' => 'Dunwoody Village to Dunwoody Station via Mt Vernon, Ashford-Dunwoody, Perimeter Center Pkwy',
'Route 150 - Dunwoody Village' => 'Dunwoody Station to Dunwoody Village via Perimeter Center Pkwy, Ashford-Dunwoody, [Mt Vernon]',
'Route 150 - Mount Vernon Rd' => 'Dunwoody Station to Dunwoody Village via Perimeter Center Pkwy, Ashford-Dunwoody, [Mt Vernon]',
'Route 153 - Browntown Road' => 'HE Holmes to Browntown Rd via F. Douglass High School, Holmes Dr, J. Jackson Pkwy',
'Route 153 - Hamilton E. Holmes Station' => 'Browntown Rd to HE Holmes Station via J. Jackson Pkwy, Holmes Dr, F. Douglass High School',
'Route 155 - Georgia State Station' => 'Polar Rock/Swallow Cir to GA State Station via Windsor St, Forsyth St, Five Points',
'Route 155 - Polar Rock Road' => 'GA State to Polar Rock OR Swallow Cir via Five Points, Forsyth St, Windsor St, [Lakewood Ave OR Brownsmill Rd] , ',
'Route 155 - Swallow Circle' => 'GA State to Polar Rock OR Swallow Cir via Five Points, Forsyth St, Windsor St, [Lakewood Ave OR Brownsmill Rd',
'Route 16 - Executive Park Drive' => 'Five Points to Executive Park via Ralph McGill, Carter Ctr, VA Highland, Briarcliff',
'Route 16 - Five Points Station / Underground Atlanta' => 'Executive Park to Five Points via Briarcliff, VA Highland, Carter Ctr, Ralph McGill',
'Route 162 - North Camp Creek Parkway' => 'Oakland City to N. Camp Creek Pkwy via Campbellton, Stanton, Alison Ct, Headland Dr, Greenbriar Mall',
'Route 162 - Oakland City Station' => 'N. Camp Creek Pkwy to Oakland City Station via Greenbriar Mall, Headland Dr, Alison Ct, Stanton, Campbellton Rd',
'Route 165 - Barge Road Park-Ride' => 'HE Holmes to Barge Rd P/R via Fairburn Rd',
'Route 165 - Hamilton E. Holmes Station' => 'Barge Rd P/R to HE Holmes Station via Fairburn Rd',
'Route 170 - Hamiliton E. Holmes Station' => 'Brownlee Rd to HE Holmes Station via MLK Dr, Peyton Rd',
'Route 170 - Lynhurst Drive/Benjamin E. Mays Drive' => 'HE Holmes to Brownlee Rd via MLK Dr, Peyton Rd, Lynhusrt Dr',
'Route 172 - College Park Station' => 'Oakland City to College Park Station via Sylvan Rd, Hapeville, Viginia Ave',
'Route 172 - Oakland Station  / Campbellton Road' => 'College Park to Oakland City Station via Virginia Ave, Hapeville, Sylvan Rd',
'Route 178 - Hamilton Boulevard' => 'Lakewood to Southside Indus. via Macon Dr, Browns Mill Rd',
'Route 178 - Lakewood Station' => 'Southside Indus. to Lakewood Station via Hapeville Rd, Macon Dr',
'Route 180 - College Park Station' => 'Palmetto to College Park via Roosevelt Hwy, Fairburn, Union City, Washington Rd, Campcreek Pkwy',
'Route 180 - Palmetto' => 'College Park to Palmetto via Campcreek Pkwy, Washington Rd, Roosevelt Hwy, Union City, Fairburn',
'Route 181 - College Park Station' => 'Fairburn to College Park via Roosevelt Hwy, Beverly Engram, Buffington Rd, GA Intl Conv. Ctr',
'Route 181 - Smith St' => 'College Park to Fairburn via GA Intl Conv. Ctr, Buffington Rd, Beverly Engram, Roosevelt Hwy',
'Route 183 - Barge Road Park N Ride' => 'Lakewood to Barge Rd P/R via Greenbriar Mall, Campbellton Rd, County Line',
'Route 183 - County Line Road' => 'Lakewood to Barge Rd P/R via Greenbriar Mall, Campbellton Rd, County Line',
'Route 183 - Lakewood Station / Fort McPherson' => 'Barge Rd P/R to Lakewood Station via Greenbriar Mall',
'Route 185 - North Springs Station' => 'Windward to N. Springs via Main St/Alpharetta City Hall, Holcomb Bridge',
'Route 185 - Windward Park / Ride' => 'N. Springs to Windward P/R via Holcomb Bridge, Main St/Alpharetta City Hall',
'Route 186 - Cone Street/Marietta Street' => 'Wesley Chapel to Five Points via Snapfinger Woods, Rainbow Dr, S. DeKalb Mall, GA State Station',
'Route 186 - Pleasant Wood Drive' => 'Five Points to Wesley Chapel via GA State Station, S. DeKalb Mall, Rainbow Dr, Snapfinger Woods',
'Route 186-Snapfinger Woods Dive' => 'Five Points to Wesley Chapel via GA State Station, S. DeKalb Mall, Rainbow Dr, Snapfinger Woods',
'Route 189 - College Park Station' => 'S. Fulton P/R to College Park via Flat Shoals Rd, Scofield Rd, Old National Hwy, GA Intl Conv. Ctr',
'Route 189- South Fulton Park/Ride' => 'College Park to S. Fulton P/R via GA Intl Conv. Ctr, Old National Hwy, Scofield Rd, Flat Shoals Rd',
'Route 19 - Decatur Station' => 'Chamblee to Decatur Station via Clairmont Rd, PDK Airport, Plaza Fiesta, Toco Hills, VA Hospital',
'Route 19- Chamblee Station' => 'Decatur to Chamblee Station via Clairmont Rd, VA Hospital, Toco Hills, Plaza Fiesta, PDK Airport',
'Route 191- Hartsfield-Jackson Atlanta International Airport' => 'Airport (Intl Term.) to Clayton Justice Ctr via GA-85, Riverdale P/R, Flint River/GA-138',
'Route 191-Clayton County Justice Center' => 'Clayton Justice Ctr to Airport (Intl Term.) via Flint River/SR138, Riverdale P/R, GA-85',
'Route 193 - East Point Station' => 'Clayton Justice Ctr to East Point Station via Jonesboro Rd, Clayton State Univ., Forest Park, Hapeville',
'Route 193- Clayton County Justice Center' => 'East Point to Clayton Justice Ctr via Hapeville, Forest Park, Clayton State Univ., Jonesboro Rd',
'Route 195- College Park Station' => 'Anvil Block Rd to College Park Station via Forest Pkwy, Forest Park, Roosevelt Hwy, GA Intl Conv. Ctr',
'Route 195/ Anvil Block Road' => 'College Park to Anvil Block Rd via GA Intl Conv. Ctr, Roosevelt Hwy, Forest Park, Forest Pkwy',
'Route 196- College Park Station' => 'Southlake Mall to College Park Station via Mt Zion, Upper Riverdale, Riverdale P/R, GA-85',
'Route 196- Southlake Mall' => 'College Park to Southlake Mall via GA-85, Riverdale P/R, Upper Riverdale, Mt Zion',
'Route 2 - Decatur Station' => 'North Ave to Decatur Station via North Ave, Ponce City Market, Fernbank Museum',
'Route 2 - North Avenue Station' => 'Decatur to North Ave Station via W. Ponce, Fernbank Museum, Ponce City Market, North Ave',
'Route 201 - H. E. Holmes Station / CC Transit' => 'Six Flags to HE Holmes (Seasonal)',
'Route 201 - Six Flags Over Georgia' => 'HE Holmes to Six Flags (Seasonal)',
'Route 21 - Kensington Station' => 'GA State to Kensington Station via Memorial Dr, Oakland Cemetery, East Lake Golf Club, Belvedere Plaza',
'Route 21- Georgia State Station' => 'Kensington to GA State Station via Memorial Dr, Belvedere Plaza, East Lake Golf Club, Oakland Cemetery, King Memorial Station',
'Route 221- Kensington Station' => 'Memorial Dr P/R to Kensington Station via Memorial Dr (Limited stops)',
'Route 221- Memorial Drive Park Ride' => 'Kensington to Memorial Dr P/R via Memorial Dr, N. Hairston Rd (Limited stops)',
'Route 24 - East Lake Station' => 'Candler Park to East Lake Station via Hosea Williams Dr, Kirkwood, Sisson Ave',
'Route 24- Edgewood/ Candler Park' => 'East Lake to Candler Park Station via Oakhurst Park, 2nd Ave, East Lake Golf Club, Hosea Williams Dr',
'Route 25 - Doraville Station' => 'Lenox to Doraville OR Medical Ctr via Brookhaven, Oglethorpe Univ., [Peachtree Indus. or Johnson Ferry]',
'Route 25 - Lenox Station' => 'Doraville/Medical Ctr to Lenox Station via Oglethorpe Univ., Brookhaven',
'Route 25- Medical Center Station' => 'Lenox to Doraville OR Medical Ctr via Brookhaven, Oglethorpe Univ., [Peachtree Indus. or Johnson Ferry]',
'Route 26 - Perry Heights' => 'North Ave to Perry Blvd via North Ave, Bankhead Station',
'Route 26 North Avenue Station' => 'Perry Blvd to North Ave Station via Bankhead Station, North Ave',
'Route 27 - Lindbergh Station / Marta Headquarters' => 'Midtown to Lindbergh Station via Botanical Gardens,<br/>Ansley Mall, Cheshire Bridge',
'Route 27- Midtown Station' => 'Lindbergh to Midtown Station via Cheshire Bridge,<br/>Ansley Mall, Botanical Gardens',
'Route 3 - Candler Park Station' => 'HE Holmes to Candler Park Station via Fair St, Mozley Park, Atlanta Univ. Ctr, Five Pts, [Highland or Irwin St]',
'Route 3 - H. E. Holmes Station' => 'Candler Park to HE Holmes Station via [Highland or Irwin St], Five Pts, Atlanta Univ. Ctr, Fair St, Mozley Park',
'Route 3 - Pryor St / Wall St' => 'HE Holmes to Candler Park Station via Fair St, Mozley Park, Atlanta Univ. Ctr, Five Pts, [Highland or Irwin St]',
'Route 30 - Lindbergh Station' => 'N. Lake to Lindbergh Station via LaVista, Lakeside, Toco Hills',
'Route 30- Northlake Mall' => 'Lindbergh to N. Lake Mall via LaVista, Toco Hills, Lakeside',
'Route 32 - Metro Transitional Center' => 'Five Pts to Transitional Ctr via Capitol, Zoo Atlanta, Confederate Ave, Bouldercrest',
'Route 32-Five Points Station' => 'Transitional Ctr to Five Pts via Bouldercrest, Confederate Ave, Zoo Atlanta, Capitol',
'Route 33 - Chamblee Station' => 'Lenox to Chamblee Station via Executive Park, Briarcliff, I-85 Access Rd, Chamblee-Tucker',
'Route 33- Lenox Station' => 'Chamblee to Lenox Station via Chamblee-Tucker, I-85 Access Rd, Briarcliff, Executive Park',
'Route 34 - Inman Park Station' => 'GPC Decatur to Inman Park Station via Gresham Rd, Brannen Rd, Stockswood, East Atlanta Village',
'Route 34- Clifton Springs Health Center' => 'Inman Park to GPC Decatur via East Atlanta Vlg, Stockswood, Brannen Rd, Gresham Rd, Clifton Spr Health Ctr',
'Route 34- Panthersville Road' => 'Inman Park to GPC Decatur via East Atlanta Vlg, Stockswood, Brannen Rd, Gresham Rd, GPC Decatur, Clifton Spr Health Ctr',
'Route 36 - Avondale Station' => 'Midtown to Avondale Station via Piedmont Park, VA Highland, Emory, N. Decatur, DeKalb Farmers Mkt',
'Route 36 Midtown Station' => 'Avondale to Midtown Station via DeKalb Farmers Mkt, N. Decatur, Emory, VA Highland, Piedmont Park',
'Route 37 Moores Mill Rd' => 'Arts Center to Moores Mill Shop. Ctr via Atlantic Station, Bellemeade Ave, Defoors Ferry',
'Route 37- Art Center Station' => 'Moores Mill Shop. Ctr to Arts Center Station via Defoors Ferry, Bellemeade Ave, Atlantic Station',
'Route 39 Doraville Station' => 'Lindbergh to Doraville Station via Sydney Marcus Blvd, Buford Hwy, Corporate Square, Plaza Fiesta',
'Route 39 Lindbergh Station' => 'Doraville to Lindbergh Station via Buford Hwy, Plaza Fiesta, Corporate Square, Sydney Marcus Blvd',
'Route 4 - Iman Park Station' => 'Thomasville to Inman Park via Forest Park Rd, Kipling St, Moreland Ave',
'Route 4- Rebel Forest Drive' => 'Inman Park to Thomasville via Moreland Ave, Valley View, Rebel Forest Dr, Forest Park Rd',
'Route 42 - Lakewood Station' => 'Five Points to Lakewood Station via Mc Daniel St, Pryor Rd, Lakewood Amphitheater',
'Route 42- Five Points' => 'Lakewood to Five Points Station via Lakewood Amphitheater, Pryor Rd, Mc Daniel St',
'Route 47 - Chamblee Station' => 'Brookhaven to Chamblee via Briarwood, I-85 Access Rd, Shallowford',
'Route 47- Broohaven Station' => 'Chamblee to Brookhaven via Shallowford, I-85 Access Rd, Briarwood',
'Route 49- Five Points Station' => 'Moreland/Woodland to Five Points via Custer Ave, Hill St, Pollard Blvd, Central Ave',
'Route 49- Moreland Drive/ Woodland Ave' => 'Five Points to Moreland/Woodland Ave via Pryor St, Pollard Ave, Mc Donough Blvd',
'Route 5 - Dunwoody Station' => 'Lindbergh to Dunwoody Station via Piedmont Rd, Buckhead, Roswell Rd, Hammond Dr',
'Route 5 - Lindbergh Station' => 'Dunwoody to Lindbergh Station via Hammond Dr, Roswell Rd, Buckhead, Piedmont Rd',
'Route 50 - Bankhead Station' => 'Carroll Heights to Bankhead Station via Bolton Rd, Donald E. Hollowell Pkwy',
'Route 50- Croft Place/ Bolton Road' => 'Bankhead to Carroll Heights via Donald E. Hollowell Pkwy, Bolton Rd, Old Gordon Rd',
'Route 50-Old Gordon Road' => 'Bankhead to Carroll Heights via Donald E. Hollowell Pkwy, Bolton Rd, Old Gordon Rd',
'Route 51- Westlake Station' => 'Five Pts to New Jersey Ave via CNN, Aquarium, Joseph E. Boone Blvd',
'Route 51-Five Points' => 'Joseph E. Boone to Five Points via Aquarium, CNN',
'Route 51-Joseph E. Boone Bvld' => 'Five Pts to New Jersey Ave via CNN, Aquarium, Joseph E. Boone Blvd',
'Route 53- Skipper Dr./ Harwell Rd' => 'West Lake to Skipper Dr via West Lake Ave, Baker Rd, Allegro Dr',
'Route 53- West Lake Station' => 'Skipper Dr to West Lake Station via Allegro Dr, Baker Rd, West Lake Ave',
'Route 55 - Five Points Station' => 'Forest Park to Five Points via Jonesboro Rd, Mc Donough Blvd, Turner Field',
'Route 55- Old Conley Road/Jonesboro Road' => 'Five Points to Forest Park via Turner Field, Mc Donough Blvd, Jonesboro Rd',
'Route 56 - Hamilton E. Holmes Station' => 'xxx',
'Route 56 - Plainville Circle' => 'HE Holmes to Adamsville via Burton Rd, Collier Rd, Bakers Ferry, [Wilson Mill or Boulder Park]',
'Route 56- Dollar Mill Road' => 'Adamsville to HE Holmes via Wilson Mill, Bakers Ferry, Collier Rd, Burton Rd',
'Route 58 - Bankhead Station' => 'Atlanta Industrial to Bankhead Station via Northwest Dr, Hollywood Dr, Donald E. Hollowell Pkwy',
'Route 58- Atlanta Industrial Pkwy' => 'Bankhead to Atlanta Industrial via Donald E. Hollowell Pkwy, Hollywood Rd, Northwest Dr',
'Route 6 - Inman Park Station' => 'Lindbergh to Inman Park Station via LaVista, Emory, Briarcliff, Moreland',
'Route 6 - Lindbergh Station' => 'Inman Park to Lindbergh Station via Moreland, Briarcliff, Emory, LaVista',
'Route 60 - Hamilton E. Holmes Station' => 'Moores Mill Shop. Ctr to HE Holmes Station via Hollywood Rd, F. Douglass High School, Holmes Dr',
'Route 60 - Moores Mill Shopping Center' => 'HE Holmes to Moores Mill Shop. Ctr via Holmes Dr, F. Douglass High School, Hollywood Rd',
'Route 66 - Barge Rd. Park/Ride' => 'HE Holmes to Barge Rd P/R via Lynhurst Dr, Harbin Rd, Therell High School, Greenbriar Mall',
'Route 66 - Hamilton E. Holmes Station' => 'Barge Rd P/R via Greenbriar Mall, Therell High School, Harbin Rd, Lynhurst Dr',
'Route 66 - Hamilton E.Holmes Station' => 'Barge Rd P/R via Greenbriar Mall, Therell High School, Harbin Rd, Lynhurst Dr',
'Route 67 - West End Station' => 'Dixie Hills to West End Station via West Lake Station, Westview Cemetery, Lucile St',
'Route 67 - West Lake Station' => 'West End to Dixie Hills via Lucile St, Westview Cemetery, West Lake Station',
'Route 68 - Asby  Station' => 'Lorraine/Granada to Ashby Station via Donnelly Ave, West End Station, Atlanta Univ. Ctr',
'Route 68 - Ashby St. Station' => 'Lorraine/Granada to Ashby Station via Donnelly Ave, West End Station, Atlanta Univ. Ctr',
'Route 68 - Benjamin E. Mays Drive' => 'Ashby to Lorraine/Granada via Atlanta Univ. Ctr, West End Station, Beecher St',
'Route 68 South Gordon Street' => 'Ashby to Lorraine/Granada via Atlanta Univ. Ctr, West End Station, Beecher St',
'Route 71 - Cascade / Ashley Courts' => 'West End to [Ashley Ct OR Country Sq] via Abernathy Blvd, Cascade Rd, Cascade Springs Preserve, Cascade Crossing',
'Route 71 - Country Squire Apartments / Ashley Court Apartments' => 'West End to [Ashley Ct OR Country Sq] via Abernathy Blvd, Cascade Rd, Cascade Springs Preserve, Cascade Crossing',
'Route 71 - West End Station' => 'Ashley Ct/Country Sq to West End via Cascade Rd, Cascade Crossing, Cascade Springs Preserve, Abernathy Blvd',
'Route 73 - Boat Rock Parkway' => 'HE Holmes to Boat Rock Blvd via Adamsville, Fulton Co Airport, Fulton Industrial',
'Route 73 - Hamilton E. Holmes Station' => 'Boat Rock to HE Holmes Station via Fulton Industrial, Fulton Co Airport, Adamsville',
'Route 74 - Battle Forrest Drive' => 'GA State to S. DeKalb Mall via East Atlanta Vlg, Flat Schoals, [Whites Mill or Battle Forest]',
'Route 74 - Five Points Station' => 'S. DeKalb Mall to GA State via [Whites Mill or Battle Forest], Flat Schoals, East Atlanta Vlg',
'Route 74- Whites Mill Road' => 'GA State to S. DeKalb Mall via East Atlanta Vlg, Flat Schoals, [Whites Mill or Battle Forest]',
'Route 75 - Avondale Station' => 'Tucker to Avondale Station via Tucker High School, Lawrenceville Hwy, N. DeKalb Mall, DeKalb Indus.',
'Route 75 - North Royal Atlanta Drive' => 'Avondale to Tucker via DeKalb Indus., N. DeKalb Mall, Lawrenceville Hwy, Tucker High School',
'Route 78 - East Point Station' => 'Browns Mill to East Point Station via Cleveland Ave',
'Route 78 - Jonesboro Road/Cleveland Avenue' => 'East Point to Browns Mill Park via Cleveland Ave, Jonesboro Rd',
'Route 79- East Point Station' => 'Oakland City to East Point Station via Sylvan Rd, Springdale Rd, Jefferson Ave, Cleveland Ave',
'Route 79- Oakland City Station' => 'East Point to Oakland City Station via Cleveland Ave, Jefferson Ave, Springdale Rd, Sylvan Rd',
'Route 8 - Avondale Station' => 'Brookhaven to Avondale Station via Executive Park, Toco Hills, N. DeKalb Mall, Historic Avondale',
'Route 8 - Brookhaven Station' => 'Avondale to Brookhaven Station via Historic Avondale, N. DeKalb Mall, Toco Hills, Executive Park',
'Route 81 - Campbellton Road' => 'West End to Adams Park via Oglethorpe Ave, Lawton St, Westmont Rd, Venetian Hills',
'Route 81 - West End Station' => 'Adams Park to West End Station via Venetian Hills, Westmont Rd, Lawton St, Oglethorpe Ave',
'Route 82 - College Park Station' => 'Union City to College Park via Roosevelt Ave, Welcome All Rd, Camp Creek Pkwy/Marketplace',
'Route 82 - Union City' => 'College Park to Union City via Camp Creek Pkwy/Marketplace, Welcome All Rd, Roosevelt Hwy',
'Route 82 Camp Creek Market Place' => 'College Park to Union City via Camp Creek Pkwy/Marketplace, Welcome All, Roosevelt Hwy',
'Route 83 - Barge Road Park n Ride' => 'Oakland City to Barge Rd P/R via Campbellton Ave, Adams Park, Greenbriar Mall',
'Route 83 - Oakland City Station' => 'Barge Rd P/R to Oakland City Station via Greenbriar Mall, Campbellton Rd, Adams Park',
'Route 84 - East Point Station' => 'Deerwood to East Point Station via Fairburn Rd, Camp Creek Marketplace, Mt Olive, Washington Rd',
'Route 84- N.Camp Creek Parkway' => 'East Point to Deerwood via Washington Rd, Mt Olive, Camp Creek Marketplace, Fairburn Rd',
'Route 84- Social Security Administration Office' => 'East Point to Deerwood via Washington Rd, Mt Olive, Camp Creek Marketplace, Fairburn Rd',
'Route 85 - Mansell Park & Ride/Mansell Road' => 'N. Springs to Mansell P/R via Dunwoody Place, Historic Roswell, Mansell Rd',
'Route 85 - North Springs Station' => 'Mansell P/R to N. Springs via Mansell Rd, Historic Roswell, Dunwoody Place',
'Route 86 - East Lake Station' => 'Stonecrest Mall to East Lake Station via Hillandale Rd, Snapfinger Woods, McAfee, 2nd Ave',
'Route 86- Stonecrest Mall' => 'East Lake to Stonecrest Mall via 2nd Ave, McAfee, Snapfinger Woods, Hillandale Rd',
'Route 87 - Dunwoody Station' => 'Dunwoody Place to Dunwoody Station via Roswell Rd, Hammond Dr',
'Route 87 - North Springs Station' => 'Dunwoody Station to N. Springs via Hammond Dr, Roswell Rd, Dunwoody Place',
'Route 87 Roswell Road/ Dunwoody Place' => 'Dunwoody Station to N. Springs via Hammond Dr, Roswell Rd, Dunwoody Place',
'Roswell Rd./Morgan Falls' => 'Dunwoody Station to Dunwoody Place via Hammond Dr, Roswell Rd',
'Route 89 -  College Park Station' => 'Shannon Square to College Park via Jonesboro Rd, Old National Hwy, Sullivan Rd, Best Rd',
'Route 89- Union Station' => 'College Park to Shannon Square via Best Rd, Sullivan Rd, Old National Hwy, Jonesboro Rd',
'Route 9 Kensington Station' => 'Brannen Rd to Kensington Station via Mark Trail Park, Toney Valley, Shamrock Rd, Peachcrest Rd',
'Route 9- Flatshoals Rd' => 'Kensington to Brannen Rd via Peachcrest Rd, Shamrock Rd, Toney Valley, Mark Trail Park',
'Route 95 - King Arnold Street' => 'West End to Hapeville via Metropolitan Pkwy, Atlanta Tech College, King Arnold St',
'Route 95 - West End Station' => 'Hapeville to West End Station via King Arnold St, Metropolitan Pkwy, Atlanta Tech College',
'Route 95 Atlanta Tech /Atlanta Metro College' => 'West End to Hapeville via Metropolitan Pkwy, Atlanta Tech College, King Arnold St',
'Route 99 - Georgia State Station' => 'North Ave/Midtown to GA State Station via Boulevard, MLK Center, Sweet Auburn',
'Route 99 - Midtown Station' => 'GA State to Midtown OR North Ave via Sweet Auburn, MLK Center, Boulevard, [Piedmont Park or North Ave]',
'Route 99 - North Avenue Station' => 'GA State to North Ave via King Memorial, Boulevard, [Piedmont Park or North Ave]',

);

$neighbourhoodsData = array(
	'CCT_481' => array(
	    array('522', 'Town Center Park/Ride')
	),

	'CCT_102' => array(
	    array('522', 'Acworth Park/Ride')
	),

	'MARTA_16' => array(
	    array('905146', 'Johnson Rd, Executive Park'),
	    array('904480', 'Old Fourth Ward, Civic Center, Downtown'),
	    array('905024', 'Old Fourth Ward, Civic Center, Downtown'),
	    array('904273', 'Virginia Highland, Johnson Rd, Executive Park'),
	    array('901037', 'Civic Center, Downtown'),
	    array('900947', 'Old Fourth Ward, Virginia Highland, Executive Park')
	),

	'MARTA_1' => array(
	    array('901441', 'Marietta St; Luckie St, Georgia Tech, North Ave. Station'),
	    array('902051', 'Marietta Blvd, Coronet Way'),
	    array('210554', 'Five Points Station')
	),

	'MARTA_12' => array(
	    array('901441', '10th Street, Georgia Tech, Midtown Station'),
	    array('902051', 'Howell Mill, Northside Pkwy, Cumberland'),
	    array('904287', 'Midtown Station'),
	    array('904285', 'Howell Mill, Northside Pkwy, Cumberland'),
	    array('904668', 'Midtown Station'),
	    array('904327', 'Howell Mill, Northside Pkwy, Cumberland')
	),

	'99' => array(
	    array('901229', 'Midtown Station'),
	    array('901230', 'Boulevard, Sweet Auburn, Georgia State'),
	    array('903404', 'Sweet Auburn, Georgia State'),
	    array('903226', 'Divide in two: (1) Piedmont Park, Midtown Station; (2) North Avenue Station'),
	    array('900974', 'Georgia State Station'),
	    array('103060', 'Divide in two: (1) Piedmont Park, Midtown Station; (2) North Avenue Station')
	),

	'MARTA_110' => array(
	    array('904552', 'Fox Theatre, Peachtree Center, Five Points'),
	    array('904551', 'Arts Center, Peachtree Rd, Buckhead'),
	    array('902393', 'Fox Theatre, Peachtree Center, Five Points'),
	    array('900021', 'Arts Center, Peachtree Rd, Buckhead')
	),

	'MARTA_27' => array(
	    array('904551', 'Botanical Gardens, Ansley Mall, Cheshire Bridge, Lindbergh Center'),
	    array('901811', 'Cheshire Bridge, Lindbergh Center'),
	    array('902010', 'Ansley Mall, Midtown'),
	    array('900611', 'Cheshire Bridge, Ansley Mall, Midtown'),
	    array('900612', 'Lindbergh Center'),
	    array('211955', 'Botanical Gardens, Ansley Mall, Cheshire Bridge, Lindbergh Center'),
	    array('211956', 'Midtown Station')
	),

	'MARTA_6' => array(
	    array('902218', 'Poncey Highland, Little Five Points, Inman Park'),
	    array('901796', 'Lindbergh Center'),
	    array('900611', 'Emory, Little Five Points, Inman Park'),
	    array('900612', 'Lindbergh Center'),
	    array('900052', 'Inman Park Station'),
	    array('900055', 'Poncey Highland, Emory, Lindbergh Center'),
	    array('900056', 'Emory, Lindbergh Center'),
	    array('900050', 'Inman Park Station')
	),

	'MARTA_2' => array(
	    array('211524', 'Ponce City Market, North Ave Station (via North Ave)'),
	    array('904530', 'Fernbank, Decatur'),
	    array('904527', 'North Ave Station (via North Ave)'),
	    array('904409', 'Poncey Highland, Fernbank, Decatur')
	),

	'MARTA_102' => array(
	    array('211524', 'Ponce City Market, North Ave Station (via Ponce)'),
	    array('904530', 'Little Five Points, Edgewood, Candler Park'),
	    array('904527', 'North Ave Station (via Ponce)'),
	    array('904409', 'Poncey Highland, Little Five Points, Candler Park'),
	    array('900052', 'Candler Park Station (via Edgewood District)'),
	    array('900055', 'Poncey Highland, Ponce City Market, North Ave Station'),
	    array('900056', 'Ponce City Market, North Ave Station'),
	    array('900050', 'Candler Park Station (via Edgewood District)')
	),

	'MARTA_30' => array(
	    array('900611', 'LaVista, Toco Hills, North Lake'),
	    array('900612', 'Lindbergh Center')
	),

	'CCT_477' => array(
	    array('490', 'Powder Springs, Hiram')
	),

	'MARTA_51' => array(
	    array('901371', 'Five Points Station (via Mitchell St)'),
	    array('904422', 'J. Boone Blvd')
	),

	'MARTA_3' => array(
	    array('900052', 'Candler Park Station (via DeKalb Ave)'),
	    array('901134', 'Candler Park Station (via Mc Lendon)'),
	    array('900055', 'Sweet Auburn (via Highland Ave), Downtown, Castleberry Hill, MLK Jr Dr, HE Holmes '),
	    array('901049', 'Sweet Auburn (via Irwin St), Downtown, Castleberry Hill, MLK Jr Dr, HE Holmes '),
	    array('900056', 'Sweet Auburn (via Highland Ave), Downtown, Castleberry Hill, MLK Jr Dr, HE Holmes '),
	    array('900050', 'Candler Park Station (via DeKalb Ave)'),
	    array('101126', 'Five Points (via Forsyth St), Sweet Auburn, Little Five Points, Candler Park'),
	    array('101010', 'MLK Jr Dr, Mozley Park, HE Holmes'),
	    array('101030', 'MLK Jr Dr, Mozley Park, HE Holmes'),
	    array('101031', 'Five Points (via Forsyth St), Sweet Auburn, Little Five Points, Candler Park'),
	    array('101014', 'MLK Jr Dr, Mozley Park, HE Holmes'),
	    array('211668', 'Five Points (via Forsyth St), Sweet Auburn, Little Five Points, Candler Park'),
	    array('101166', 'MLK Jr Dr, Mozley Park, HE Holmes')
	),

	'MARTA_13' => array(
	    array('101126', 'Five Points (via Peachtree St)'),
	    array('101010', 'Fair St, Mozley Park, West Lake'),
	    array('101030', 'Fair St, Mozley Park, West Lake'),
	    array('902902', 'Five Points (via Peachtree St)')
	),

	'MARTA_36' => array(
	    array('901229', 'Midtown Station'),
	    array('901230', 'Virginia Highland, Emory, North Decatur, Avondale'),
	    array('904480', 'Piedmont Park, Midtown'),
	    array('904772', 'Emory, North Decatur, Avondale'),
	    array('902990', 'Piedmont Park, Midtown'),
	    array('902218', 'Virginia Highland, Piedmont Park, Midtown'),
	    array('901796', 'North Decatur, Avondale')
	),

	'GRN' => array(
	    array('10thhemp', 'GTRI Conference Center')
	)

);

foreach($stopIds as $sid) {
	printPageForStop($sid);
}

function printPageForStop($stop) {
	$agency = $stop[0];
	$sid = $stop[1];
	
	$stopId1 = $stop[0];

	$result = getJson("http://atlanta.onebusaway.org/api/api/where/schedule-for-stop/" . $stopId1 . ".json?key=TEST&date=2015-09-08"); // weekday

	$stopName = $result['data']['references']['stops'][0]['name'];
	if (isset($_REQUEST['stopNameOverride']) && $_REQUEST['stopNameOverride'] != "") {
		$stopName = $_REQUEST['stopNameOverride'];
	}

	$routes = $result['data']['references']['routes'];

	$stopSchedules = array();
	if (count($routes) == 1) array_push($stopSchedules, null);
	
/*	foreach($routes as $r) {
		$route = array();

		$route['name'] = $r['shortName'];
		$route['agency'] = $r['agencyId'];		
		$route['direction'] = 'unknown';
		//$route['neighbourhoods'] = getNeighbourhoods($agency, $r['shortName'], $sid);
		$route['wkday'] = array();
		$route['sat'] = array();
		$route['sun'] = array();

		$routeId = $r['id'];
		$stopSchedules[$routeId] = $route;
	}
*/
//	if (count($routes) == 1) array_push($stopSchedules, null);
	

	// FIRST bus stop
	// get weekday schedules
	createSchedules($result, $stopSchedules, 'wkday');

	// get saturday schedules
	$result = getJson("http://atlanta.onebusaway.org/api/api/where/schedule-for-stop/" . $stopId1 . ".json?key=TEST&date=2015-04-04"); // saturday
	createSchedules($result, $stopSchedules, 'sat');
	
	// get sunday schedules
	$result = getJson("http://atlanta.onebusaway.org/api/api/where/schedule-for-stop/" . $stopId1 . ".json?key=TEST&date=2015-04-05"); // sunday
	createSchedules($result, $stopSchedules, 'sun');

	if (count($routes) == 1) array_push($stopSchedules, null);

	// SECOND BUS STOP if available
	$stopId2 = $stop[1];
	if ($stopId2 != "") {
		// get weekday schedules
		$result = getJson("http://atlanta.onebusaway.org/api/api/where/schedule-for-stop/" . $stopId2 . ".json?key=TEST&date=2015-09-08"); // weekday
		createSchedules($result, $stopSchedules, 'wkday');

		// get saturday schedules
		$result = getJson("http://atlanta.onebusaway.org/api/api/where/schedule-for-stop/" . $stopId2 . ".json?key=TEST&date=2015-04-04"); // saturday
		createSchedules($result, $stopSchedules, 'sat');
		
		// get sunday schedules
		$result = getJson("http://atlanta.onebusaway.org/api/api/where/schedule-for-stop/" . $stopId2 . ".json?key=TEST&date=2015-04-05"); // sunday
		createSchedules($result, $stopSchedules, 'sun');
	}

	// var_dump($stopSchedules);

	// print
	echo "<div class='page'>";
	echo "<div id='top-hole'>+</div>";
	printPageHeader($stopName);

	echo "<div class='buses'>";
	foreach($stopSchedules as $ss) {
		printRouteInfo($ss);
	}
	echo "</div>";
	printPageFooter($sid);
	echo "</div>";
	echo "<div style='clear:both; page-break-after: always;'><" . "/div>";
}

function getNeighbourhoods($agency, $route, $sid) {
	global $neighbourhoodsData;
	
	$route_index = $agency.'_'.$route;
	$routeNbhds = (array_key_exists($route_index, $neighbourhoodsData) ? $neighbourhoodsData[$route_index] : null);

	if ($routeNbhds != null) {
		for($i = 0; $i < count($routeNbhds); $i++) {
			if($routeNbhds[$i][0] == $sid) {
				$nbds = $routeNbhds[$i][1];
				return explode(',', $nbds);
			}
		}
	}
	return null;
}

function getJson($url) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_URL,$url);
	$result=curl_exec($ch);
	curl_close($ch);
	return json_decode($result, true);
}

function createSchedules($result, &$stopSchedules, $day) {
	global $directionData;

	$rawSchedules = $result['data']['entry']['stopRouteSchedules'];

/*	foreach($rawSchedules as $rawSch) {
		$routeId = $rawSch['routeId'];
		
		$rawHeadsign = $rawSch['stopRouteDirectionSchedules'][0]['tripHeadsign'];
		$stopSchedules[$routeId]['direction'] = $rawHeadsign; // todo stopRouteDirectionSchedules should only have one item in it		
		$stopSchedules[$routeId]['direction2'] = array_key_exists($rawHeadsign, $directionData) ? $directionData[$rawHeadsign] : $rawHeadsign;

		$schedule = array();

		$arrTimes = $rawSch['stopRouteDirectionSchedules'][0]['scheduleStopTimes']; 
		foreach($arrTimes as $at) {
			array_push($schedule, $at['departureTime']); // todo check for arrivalEnabled and departureEnabled
		}
		$stopSchedules[$routeId][$day] = $schedule;
	}
*/
	$routes = $result['data']['references']['routes'];
	
	foreach($rawSchedules as $rawSch) {
		$routeId = $rawSch['routeId'];		
		
		foreach($rawSch['stopRouteDirectionSchedules'] as $rawSchDirn) {
		
			$rawHeadsign = $rawSchDirn['tripHeadsign'];
			$scheduleId = $routeId . "_" . $rawHeadsign;
			
			$stopSch = (array_key_exists($scheduleId, $stopSchedules) ? $stopSchedules[$scheduleId] : null);
			if ($stopSch == null) {
				foreach($routes as $r) {
					if ($r['id'] == $routeId) {
						$route = array();

						$route['name'] = $r['shortName'];
						$route['agency'] = $r['agencyId'];		
						$route['direction'] = 'unknown';
						//$route['neighbourhoods'] = getNeighbourhoods($agency, $r['shortName'], $sid);
						$route['wkday'] = array();
						$route['sat'] = array();
						$route['sun'] = array();

						
						$stopSchedules[$scheduleId] = $route;				
						break;
					}
				}
			}
			
			$stopSchedules[$scheduleId]['direction'] = $rawHeadsign; // todo stopRouteDirectionSchedules should only have one item in it		
			$stopSchedules[$scheduleId]['direction2'] = (array_key_exists($rawHeadsign, $directionData) ? $directionData[$rawHeadsign] : $rawHeadsign);

			$schedule = array();

			$arrTimes = $rawSchDirn['scheduleStopTimes']; 
			foreach($arrTimes as $at) {
				array_push($schedule, $at['departureTime']); // todo check for arrivalEnabled and departureEnabled
			}
			$stopSchedules[$scheduleId][$day] = $schedule;
		}
	}
	
	

}

function printPageHeader($stopName) {
	echo <<<EOT
	<div class='header'>
		<img class='logo' src='ttrip.png' />
		<h1>$stopName</h1>		
		<div class='float-right'>
			<h2 style='text-align:left;'>08-AUG-2015<br/>11-DEC-2015</h2>
		</div>
		<div class='float-right' style='border-left: 1px solid #D0D0D0; padding-left: 15px;'>
			<h2>Valid/<span class='alt-lang'>V&aacute;lido</span>:
			<br />Exp.:</h2>
			<!--br />Exp. &#x25BA;</h2-->
		</div>
		<div class='float-right'>
			<h2>Check your bus departure times* from this stop.
			<br/><span class="alt-lang">Verifica el horario* de su autobus en esta parada.</span>
			</h2>
		</div>
	</div>
EOT;
}

function printRouteInfo($routeInfo) {
	$name = "";
	$agency = "MARTA";
	
	if ($routeInfo != null) {
		$name = $routeInfo['name'];
		$agency = $routeInfo['agency'];
	}

	// todo add colour this up in a separate version 

	echo <<<EOT
	<div class='bus'>
		<div class='title $agency'>
			<h3>$name</h3>
			<ul class='places'>
EOT;

	if ($routeInfo != null) {
	//if ($routeInfo['neighbourhoods'] != null) {
	//	foreach($routeInfo['neighbourhoods'] as $nbd) {
	//		echo "<li>" . trim($nbd) . "</li>";
	//	}
	//}
	//else {
		$destArray = explode(" via ", $routeInfo['direction2']);
		echo "<li class='origin-destination'>" . str_replace(". " , ".&nbsp;", str_replace(" to ", "&nbsp;&#x27A4;&nbsp;", $destArray[0])) . "</li>";
		//echo "<li><b>" . $destArray[0] . "</b></li>";
		if (count($destArray) > 1) echo "<li class='waypoints'><i>via</i> " . $destArray[1] . "</li>";		
	//}
	}

	echo "</ul></div>";

	echo <<<EOT
		<table class='schedule'>
EOT;
	if ($routeInfo != null) {
		echo <<<EOT
				<thead><tr><th>Weekdays<br/><span class="alt-lang">En semana<span></th><th>Saturday<br/><span class="alt-lang">S&aacute;bado</span></th><th>Sunday<br/><span class="alt-lang">Domingo</span></th></tr></thead>
				<tbody>
				<tr>
					<td><ul>
EOT;
		
		printTimeTable($routeInfo['wkday']);
		echo "</ul></td><td><ul>";
		printTimeTable($routeInfo['sat']);
		echo "</ul></td><td><ul>";
		printTimeTable($routeInfo['sun']);
		echo "</ul></td></tr></tbody>";
	}
	else {
		echo "<thead></thead><tbody></tbody>";
	}
	echo "</table></div>";
}

function printTimeTable($tt) {
	$prevH = -1;
	$prevAMPM = 0;
	$first = true;

	foreach($tt as $t) {
		$t /= 1000;
		$h = intval(date('G', $t));
		$m = date('i', $t);
		$am = true;
		if($h >= 12) { $am = false; }
		if($h > 12) { $h -= 12; }
		if($h == 0) {
			$h = 12;
			$am = true;
		}
		$hs = ($h < 10) ? "&nbsp;&nbsp;" . $h : $h;

		if($h != $prevH) {
			$prevH = $h;
			//if($first) { $first = false; echo "<li class=\"hour\">"; }
			//else { echo "<span class='clearer'></li><li class=\"hour\">"; }

			if($first) $first = false; 
			//else echo "<span class='clearer'></li>";
			else echo "</li>";
			
			if ($am) echo "<li class=\"hour\">"; 
			else echo "<li class=\"pm hour\">"; 
			
			
			$thisAMPM = $am ? 1 : 2; 
			//if ($thisAMPM != $prevAMPM) {
			//	if($am) { echo "<span>AM&nbsp;$hs:$m</span>"; }
			//	else { echo "<span class='pm'>PM&nbsp;$hs:$m</span>"; }			
			//}
			//else {
			//	if($am) { echo "<span>$hs:$m</span>"; }
			//	else { echo "<span class='pm'>$hs:$m</span>"; }
			//}
			if ($thisAMPM != $prevAMPM) {
				if($am) { echo "<span><small>AM&nbsp;</small>$hs:$m</span>"; }
				else { echo "<span class='pm'><small>PM&nbsp;</small>$hs:$m</span>"; }			
			}
			else {
				if($am) { echo "<span>$hs:$m</span>"; }
				else { echo "<span class='pm'>$hs:$m</span>"; }
			}
			$prevAMPM = $thisAMPM;
			
		} else {
			if($am) { echo "<span>:$m</span>"; }
			else { echo "<span class='pm'>:$m</span>"; }
		}
	}
	//echo "<span class='clearer'></span></li>";
	echo "</li>";
}

function printPageFooter($stopId) {
	$sid_loc = $_REQUEST['sid'];
	$agency_loc = $_REQUEST['agency'];
	$sid_full = $agency_loc . "_" . $sid_loc;

	echo <<<EOT
	<div class='disclaimer'>
		*Trip times are approximate, may change without notice, and may vary with road conditions, events, and holidays. Data provided by MARTA and OneBusAway.
		<br /><span class='alt-lang'>*Los horarios son indicativos, pueden cambiar sin aviso previo y cambiar en funci&oacute;n de las condiciones de circulaci&oacute;n, eventos, y d&iacute;as festivos.</span> 
	</div>
		<div class='footer'>
			<div class='ack1'>This stop has been adopted by</div>
			<div class='ack'>
EOT;
	if (isset($_REQUEST['adopter']) && $_REQUEST['adopter'] != "") {					
		$adopter = $_REQUEST['adopter'];

		echo "<div class='logo soldierName' style='max-width:600px;'>";
		echo "<p class='adopter'>$adopter</p>";
		if (isset($_REQUEST['rank']) && $_REQUEST['rank'] != "") {
			$rank = $_REQUEST['rank'];
			echo "<p class='rank'>$rank</p>";
		}
		echo "</div>";
	}
	if (isset($_REQUEST['weblogo']) && $_REQUEST['weblogo'] != "") {
		$logoUrl = $_REQUEST['weblogo'];
		echo "<img class='logo' src='$logoUrl' style='max-width:600px;' />";		
	}
	echo <<<EOT
			</div>
			<div class='QR'>
				<img src='qr.php?p=http://atlanta.onebusaway.org/where/standard/stop.action?id=$sid_full'/>
				
				<p style="margin:0.3em"><span class='big'>&#x25BA;</span></p>
				<p style="margin:0.3em"><span class='big'>SCAN HERE</span><br />to get live bus predictions<br />on your mobile device.
				<br/>($sid_loc)

				<div class='adoptPitch'>
						<p><b>YOU CAN ADOPT A STOP TOO!</b>
						<br />MARTAArmy.org/<b>join</b>an<b>army</b></p>
				</div>
				
				</p> <!-- TODO mention OBA close to this code -->
			</div>
			<div id="bottom-hole">+</div>
			<!-- todo add a disclaimer from MARTA on this -->
		</div></div><div style='clear:both; page-break-after: always;'></div>		
		</div><div style='clear:both; page-break-after: always;'></div>
EOT;
}

?>

<script type='text/javascript'>
	//$('.bus table.schedule td ul li').each(function(el) { 
	//	var $e = $(this); 
	//	var len = $e.find('span').length;
		
		//$e.width(55 + 25*(len-2)); 
		//$e.width(94 + 45*(len-1)); 

	//});
	var cmnHeight = 0;
	var busWidths = []; 
	var totalWidth = 0;
	var containerWidth = 1700;
	var i = 0;
	var nSkinnyCols = 0;
	
	$('div.bus').each(function() {
		var $b = $(this);
		var $t = $b.find('div.title');
		var $s = $b.find('table.schedule');
		var w = $s.width();		
		$b.width(w); //$t.width(w);		
		busWidths[i] = $b.width();
		i++;

		if (w < 5) nSkinnyCols++;
		else totalWidth += w;
		
		var h = $s.height();
		if (h > cmnHeight) cmnHeight = h;
	});
	
	var idealBusWidth = containerWidth;
	if (busWidths.length > 1) idealBusWidth = containerWidth / busWidths.length;

	var skinnyColWidth = 0;
	if (nSkinnyCols != 0) {
		if (totalWidth < idealBusWidth) skinnyColWidth = totalWidth / 2;
		else skinnyColWidth = (containerWidth - totalWidth) / nSkinnyCols / 1.4;
		
		$('div.bus').each(function() {
			var $b = $(this);
			//var $t = $b.find('div.title');
			//var $s = $b.find('table.schedule');
			var w = $b.width();
			if (w < 5) {
				w = skinnyColWidth;
				$b.width(w); //$t.width(w);		
				totalWidth += w;
			}
		});
	}
	if (totalWidth < containerWidth) {
		// if all bus widths are less than 1/n container width then set width to 1/n container width
		// else scale up.
		
		var allBusWidthsAreLessThanIdeal = true;
		for (var j = 0; j < busWidths.length; j++) {
			if (busWidths[j] > idealBusWidth) allBusWidthsAreLessThanIdeal = false;
		}
		
		var stretchRatio = containerWidth / totalWidth;
		$('div.bus').each(function() {
			var $b = $(this);
			var $t = $b.find('div.title');
			var $s = $b.find('table.schedule');
			var w = $b.width();

			if (!allBusWidthsAreLessThanIdeal || nSkinnyCols != 0) {
				$b.width(w * stretchRatio);// * 0.999);			
			}
			else {
				$b.width(idealBusWidth);// * 0.999);
			}
			//$s.width("100%");

		});	
	}
	
	$('div.bus').each(function() {
		var $b = $(this);
		var $s = $b.find('table.schedule');
		$s.height(cmnHeight); 
	});

</script>


</body>
</html>
