<?php

// Parse downloaded files

error_reporting(E_ALL ^ E_DEPRECATED);

require_once(dirname(__FILE__) . '/simplehtmldom_1_5/simple_html_dom.php');


//----------------------------------------------------------------------------------------
function get_preceding_page($pages, $from = -1)
{
	if ($from == -1)
	{
		$from = count($pages) - 1;
	}
	
	//echo $from;
	
	$done = false;
	while (($from > 0) && !$done)
	{
		//echo $pages[$from]->PageNumber . "\n";
		
		if ($pages[$from]->PageNumber != '')
		{
	
			if (!preg_match('/\[/', $pages[$from]->PageNumber))
			{
				if ($pages[$from]->PageTypeName != 'Blank' &&!preg_match('/^pl/', $pages[$from]->PageNumber))
				{			
					$done = true;
				}
			}
		}
				
		if (!$done)
		{
			$from--;
		}
	
	}
	
	$page = $pages[$from]->PageNumber;
	
	$page = str_replace('p.%', '', $page);
	
	return $page;
}

//----------------------------------------------------------------------------------------
function authors_from_string($authorstring, $split_on_commas = false)
{
	$authors = array();
	
	$authorstring = preg_replace("/\s+&amp;\s+/u", "|", $authorstring);
	 
	
	// Strip out suffix
	$authorstring = preg_replace("/,\s*Jr./u", "", trim($authorstring));
	$authorstring = preg_replace("/,\s*jr./u", "", trim($authorstring));
	
	$authorstring = preg_replace("/\.\-/Uu", "-", $authorstring);	
	
	/*
	//echo $authorstring . "\n";
	if (preg_match('/^(?<name>\w+((\s+\w+)+)?),/u', $authorstring, $m))
	{
		//print_r($m);
		$authorstring = preg_replace("/,/u", "|", $authorstring);
		//$authorstring = preg_replace("/^" . $m['name'] . "\|/u", $m['name'] . ",", $authorstring);
		//echo $authorstring . "\n";
	}
	else
	{
		$authorstring = preg_replace("/,/u", "|", trim($authorstring));
	}
    //echo $authorstring . "\n";
    */
    
    if ($split_on_commas)
    {
    	$authorstring = preg_replace("/,\s*/u", "|", trim($authorstring));
    }


	$authorstring = preg_replace("/,$/u", "", trim($authorstring));
	$authorstring = preg_replace("/&/u", "|", $authorstring);
	$authorstring = preg_replace("/;/u", "|", $authorstring);
	$authorstring = preg_replace("/ and /iu", "|", $authorstring);
	$authorstring = preg_replace("/\.,/Uu", "|", $authorstring);
						
	$authorstring = preg_replace("/\|\s*\|/Uu", "|", $authorstring);				
	$authorstring = preg_replace("/\|\s*/Uu", "|", $authorstring);				
	$authors = explode("|", $authorstring);
	
	//echo $authorstring . "\n";
	
	for ($i = 0; $i < count($authors); $i++)
	{
		$authors[$i] = preg_replace('/\.([A-Z])/u', ". $1", $authors[$i]);
		$authors[$i] = preg_replace('/^\s+/u', "", $authors[$i]);
		$authors[$i] = mb_convert_case($authors[$i], MB_CASE_TITLE, 'UTF-8');
		
		$authors[$i] =str_replace('.', '', $authors[$i]);
	}
	
	// try and catch obvious errors
	$j = 0;
	$a = array();
	for ($i = 0; $i < count($authors); $i++)
	{
		if ($split_on_commas)
		{
			if (preg_match('/^(?<lastname>\\p{L}+(-\\p{L}+)?)\s+(?<firstname>[A-Z]\.(\s*[A-Z]\.)?)$/u', $authors[$i], $m))
			{
				$a[$j] = $m['lastname'] . ', ' . $m['firstname'];
				$j++;
			}	
			else
			{
				$a[$j] = $authors[$i];
				$j++;
			}	
		}
		else
		{	
			if (preg_match('/^([A-Z]\.?((\s+[A-Z]\.?)+)?)$/', $authors[$i]))
			{
				$a[$j-1] = $authors[$i] . ' ' . $a[$j-1];
			}
			else
			{
				$a[$j] = $authors[$i];
				$j++;
			}
		}
	}
	$authors = $a;
	
	//print_r($a);
	

	return $authors;
}

//----------------------------------------------------------------------------------------


$basedir = 'html-NOTUL';
$basedir = 'html-BMBOT';
$basedir = 'html-BMBAD';
$basedir = 'html-REMYC';
$basedir = 'html-BUMHN';
$basedir = 'html-BMAZO';

$basedir = 'html-BMZOO';

//$basedir = 'html-BMNHN';

$basedir = 'html-MNHN_CRYAL';
$basedir = 'html-MNHN_CRYMY';
$basedir = 'html-MNHN_CRYBL';

//$basedir = 'html-MEBBO';


$basedir = 'html-MNHN_REBRY';

$basedir = 'html-BMZOO';
$basedir = 'html-BUMHN';
$basedir = 'html-BMBOT';



$files = scandir($basedir);

/*
$files=array(
'MNHN_REBRY_1982_TABL_N000.html',
'MNHN_REBRY_1979_T045_N000.html',
'MNHN_REBRY_1978_T044_N000.html',
'MNHN_REBRY_1977_T043_N000.html',
'MNHN_REBRY_1976_T042_N000.html',
'MNHN_REBRY_1976_T041_NTAB.html',
'MNHN_REBRY_1975_T041_N000.html',
'MNHN_REBRY_1974_T040_N000.html',
'MNHN_REBRY_1973_T039_N000.html',
//'MNHN_REBRY_1972_TABL_N000.html',
'MNHN_REBRY_1971_T038_N000.html',
'MNHN_REBRY_1970_T037_N000.html',
'MNHN_REBRY_1968_T036_N000.html',
'MNHN_REBRY_1967_T035_N000.html',
'MNHN_REBRY_1966_T034_N000.html',
'MNHN_REBRY_1965_T033_N000.html',
'MNHN_REBRY_1963_T032_N000.html',
'MNHN_REBRY_1962_T031_N000.html',
'MNHN_REBRY_1961_T030_N000.html',
'MNHN_REBRY_1960_T029_N000.html',
'MNHN_REBRY_1959_T028_N000.html',
'MNHN_REBRY_1958_T027_N000.html',
'MNHN_REBRY_1957_T026_N000.html',
'MNHN_REBRY_1956_T025_N000.html',
'MNHN_REBRY_1955_T024_N000.html',
//'MNHN_REBRY_1954_TABL_N000.html',
'MNHN_REBRY_1954_T023_N000.html',
'MNHN_REBRY_1953_T022_N000.html',
'MNHN_REBRY_1952_T021_N000.html',
'MNHN_REBRY_1951_T020_N000.html',
'MNHN_REBRY_1950_T019_N000.html',
//'MNHN_REBRY_1949_T018_N000.html',
);
*/

//$files=array('MNHN_REBRY_1949_T018_N000.html');

// debug

//$files= array('NOTUL_S000_1960_T016_N001 serial display.html');

//$files= array('NOTUL_S000_1920_T004_N001 serial display.html');

//$files= array('NOTUL_S000_1911_T002_N001 serial display.html');

//$files=array('BUMHN_S001_1897_T003_N006.html');

//$files=array('BMAZO_S004_1979_T001_N002.html');

//$files=array('MNHN_CRYBL_1993_T014_N004.html');

// $files=array('BUMHN_S001_1895_T001_N003.html');

$files=array(
'BMBOT_S003_1972_T045_N001.html',
'BMBOT_S003_1972_T046_N002.html',
'BMBOT_S003_1972_T047_N003.html',
'BMBOT_S003_1972_T048_N004.html',
'BMBOT_S003_1972_T071_N005.html',
'BMBOT_S003_1973_T126_N006.html',
'BMBOT_S003_1973_T127_N007.html',
'BMBOT_S003_1973_T128_N008.html',
'BMBOT_S003_1973_T189_N009.html',
'BMBOT_S003_1973_T190_N010.html',
'BMBOT_S003_1973_T191_N011.html',
'BMBOT_S003_1973_T192_N012.html',
'BMBOT_S003_1973_T200_N013.html',
'BMBOT_S003_1973_T201_N014.html',
'BMBOT_S003_1974_T227_N015.html',
'BMBOT_S003_1974_T269_N016.html',
'BMBOT_S003_1974_T270_N017.html',
'BMBOT_S003_1974_T271_N018.html',
'BMBOT_S003_1974_T271_N019.html',
'BMBOT_S003_1975_T276_N020.html',
'BMBOT_S003_1975_T277_N021.html',
'BMBOT_S003_1975_T325_N022.html',
'BMBOT_S003_1975_T326_N023.html',
'BMBOT_S003_1976_T364_N024.html',
'BMBOT_S003_1976_T365_N025.html',
'BMBOT_S003_1976_T383_N026.html',
'BMBOT_S003_1976_T395_N027.html',
'BMBOT_S003_1976_T418_N028.html',
'BMBOT_S003_1977_T442_N029.html',
'BMBOT_S003_1977_T443_N030.html',
'BMBOT_S003_1977_T460_N031.html',
'BMBOT_S003_1977_T461_N032.html',
'BMBOT_S003_1977_T502_N033.html',
'BMBOT_S003_1977_T503_N034.html',
'BMBOT_S003_1978_T521_N035.html',

);


foreach ($files as $filename)
{
	echo "-- $filename\n";
	
	$references = array();
	

	if (preg_match('/\.html$/', $filename))
	{	
		$journal 	= '';
		$series     = '';
		$issn 		= '';
		$volume 	= '';
		$issue 		= '';
		$year 		= '';
		
		$code 		= '';
		
		// NOTUL_S000_1920_T004_N001 serial display
		if (preg_match('/(?<code>NOTUL_S000_(?<year>[0-9]{4})_T0*(?<volume>\d+)_N0*(?<issue>\d+))/', $filename, $m))
		{
			$journal 	= 'Notulae Systematicae';
			$issn		= '0374-9223';
			$volume 	= $m['volume'];
			$issue 		= $m['issue'];
			$year 		= $m['year'];
			
			$code 		= $m['code'];
		}
		else
		{
			//echo "Bad\n";
			
		}
		
		// BMBAD_S004_1981_T003_N003
		if (preg_match('/(?<code>BMBAD_S0*(?<series>\d+)_(?<year>[0-9]{4})_T0*(?<volume>\d+)_N0*(?<issue>\d+))/', $filename, $m))
		{
			$journal 	= 'Bulletin du Muséum National d\'Histoire Naturelle Section B,Adansonia, botanique, phytochimie';
			$series 	= $m['series'];
			$issn		= '0240-8937';
			$volume 	= $m['volume'];
			$issue 		= $m['issue'];
			$year 		= $m['year'];
			
			$code 		= $m['code'];
		}
		else
		{
			//echo "Bad\n";
			
		}
		
		if (preg_match('/(?<code>BMBOT_S0*(?<series>\d+)_(?<year>[0-9]{4})_T0*(?<volume>\d+)_N0*(?<issue>\d+))/', $filename, $m))
		{
			$journal 	= 'Bulletin du Muséum national d\'histoire naturelle. Section B, botanique, biologie et écologie végétales, phytochimie';
			$series 	= $m['series'];
			$issn		= '0181-0634';
			$volume 	= $m['volume'];
			$issue 		= $m['issue'];
			$year 		= $m['year'];
			
			$code 		= $m['code'];
		}
		else
		{
			echo "-- Bad\n";
			
		}
		
		// MNHN_REMYC_1938_T003_N001
		if (preg_match('/(?<code>MNHN_REMYC_(?<year>[0-9]{4})_T0*(?<volume>\d+)_N0*(?<issue>\d+))/', $filename, $m))
		{
			$journal 	= 'Revue de mycologie';
			$issn		= '0484-8578';
			$volume 	= $m['volume'];
			$issue 		= $m['issue'];
			$year 		= $m['year'];
			
			$code 		= $m['code'];

		}
		else
		{
			echo "-- Bad\n";
			
		}		
		
		// BUMHN_S001_1901_T007_N008
		if (preg_match('/(?<code>BUMHN_S0*(?<series>\d+)_(?<year>[0-9]{4})_T0*(?<volume>\d+)_N0*(?<issue>\d+))/', $filename, $m))
		{
			$journal 	= 'Bulletin du Musée d\'Histoire Naturelle';
			$series 	= $m['series'];
			$issn		= '0027-4070';
			$volume 	= $m['volume'];
			$issue 		= $m['issue'];
			$year 		= $m['year'];
			
			$code 		= $m['code'];
		}
		else
		{
			echo "-- Bad\n";
			
		}
		
		
		// BMAZO_S004_1979_T001_N002
		if (preg_match('/(?<code>BMAZO_S0*(?<series>\d+)_(?<year>[0-9]{4})_T0*(?<volume>\d+)_N0*(?<issue>\d+))/', $filename, $m))
		{
			$journal 	= 'Bulletin du Muséum national d\'histoire naturelle. Section A: Zoologie, biologie et écologie animales';
			$series 	= $m['series'];
			$issn		= '0181-0626';
			$volume 	= $m['volume'];
			$issue 		= $m['issue'];
			$year 		= $m['year'];
			
			$code 		= $m['code'];
		}
		else
		{
			echo "-- Bad\n";
			
		}
		
		
		// BMNHN_S001_1909_T015_N005
		if (preg_match('/(?<code>BMNHN_S0*(?<series>\d+)_(?<year>[0-9]{4})_T0*(?<volume>\d+)_N0*(?<issue>\d+))/', $filename, $m))
		{
			$journal 	= 'BMNHN';
			$series 	= $m['series'];
			$issn		= '1148-8425';
			$volume 	= $m['volume'];
			$issue 		= $m['issue'];
			$year 		= $m['year'];
			
			$code 		= $m['code'];
		}
		else
		{
			echo "-- Bad\n";
			
		}		

		// BMZOO_S003_1977_T493_N342
		if (preg_match('/(?<code>BMZOO_S0*(?<series>\d+)_(?<year>[0-9]{4})_T0*(?<volume>\d+)_N0*(?<issue>\d+))/', $filename, $m))
		{
			$journal 	= 'Bulletin du Muséum national d\'Histoire naturelle 3ème série - Zoologie';
			$series 	= $m['series'];
			$issn		= '0300–9386';
			$volume 	= $m['volume'];
			$issue 		= $m['issue'];
			$year 		= $m['year'];
			
			$code 		= $m['code'];
		}
		else
		{
			echo "-- Bad\n";
			
		}		
		
		// MNHN_CRYAL
		if (preg_match('/(?<code>MNHN_CRYAL_(?<year>[0-9]{4})_T0*(?<volume>\d+)_N0*(?<issue>\d+))/', $filename, $m))
		{
			$journal 	= 'Cryptogamie. Algologie';
			$issn		= '0181-1568';
			$volume 	= $m['volume'];
			$issue 		= $m['issue'];
			$year 		= $m['year'];
			
			$code 		= $m['code'];
		}
		else
		{
			echo "-- Bad\n";
			
		}		

		// MNHN_CRYMY
		if (preg_match('/(?<code>MNHN_CRYMY_(?<year>[0-9]{4})_T0*(?<volume>\d+)_N0*(?<issue>\d+))/', $filename, $m))
		{
			$journal 	= 'Cryptogamie. Mycologie';
			$issn		= '0181-1584';
			$volume 	= $m['volume'];
			$issue 		= $m['issue'];
			$year 		= $m['year'];
			
			$code 		= $m['code'];
		}
		else
		{
			echo "-- Bad\n";
			
		}		
		
		// MEBBO_S00B
		// MEBBO_S00B_1950_T001_N001
		
		if (preg_match('/(?<code>MEBBO_S00B_(?<year>[0-9]{4})_T0*(?<volume>\d+)_N0*(?<issue>\d+))/', $filename, $m))
		{
			$journal 	= 'Mémoires du Muséum national d\'histoire naturelle. Série B, Botanique';
			$issn		= '0078-9755';
			$volume 	= $m['volume'];
			$issue 		= $m['issue'];
			$year 		= $m['year'];
			
			$code 		= $m['code'];
		}
		else
		{
			echo "-- Bad\n";
			
		}	
		
		// html-MNHN_CRYBL			
		if (preg_match('/(?<code>MNHN_CRYBL_(?<year>[0-9]{4})_T0*(?<volume>\d+)_N0*(?<issue>\d+))/', $filename, $m))
		{
			$journal 	= 'Cryptogamie. Bryologie, lichenologie';
			$issn		= '0181-1576';
			$volume 	= $m['volume'];
			$issue 		= $m['issue'];
			$year 		= $m['year'];
			
			$code 		= $m['code'];
		}
		else
		{
			echo "-- Bad\n";
			
		}		
		
		// html-MNHN_REBRY
		if (preg_match('/(?<code>MNHN_REBRY_(?<year>[0-9]{4})_T0*(?<volume>\d+)_N0*(?<issue>\d+))/', $filename, $m))
		{
			$journal 	= 'Revue bryologique et lichenologique';
			$issn		= '0373-0913';
			$volume 	= $m['volume'];
			$issue 		= $m['issue'];
			$year 		= $m['year'];
			
			$code 		= $m['code'];
		}
		else
		{
			echo "-- Bad\n";
			
		}		
		
		
	
	
	
		$html = file_get_contents($basedir . '/' . $filename);
		
		// clean HTML
		
		// <li><a id="ID0EBAAA">Communications</a></li>
		
		$html = preg_replace('/<a id="ID0[A-Z]+">Communications?<\/a>(<ul>|<\/li>)<li>/i', '', $html);
		
		$dom = str_get_html($html);		
		
		$divs = $dom->find('div[id=TableOfContents1]');

		foreach ($divs as $div)
		{

			//echo  "|" . $div->plaintext . "|\n\n";
			//echo $div->outertext . "\n\n";
	
			
			
			if (0)
			{
				preg_match_all("/\{ fileName: '([^']+)', index: (?<index>\d+) }/", $div->outertext, $mm);
				
				print_r($mm);
				
				
			
			}
			else
			{
				$count = 0;
				foreach ($div->find('li') as $li)
				{	
					echo "-- " . $li->plaintext . "\n";
			
			
					$reference = new stdclass;
					$reference->scan = $code;
				
				
					$reference->type = 'article';
					$reference->journal 	= $journal;
					$reference->issn 		= $issn;
				
					if ($series != '')
					{
						$reference->series = $series;
					}
				
					$reference->volume = $volume;
					$reference->issue = $issue;
					$reference->date = $year;
	
					if (preg_match('/(?<title>.*)\s\/\s+(?<authorstring>.*)/u', $li->plaintext, $m))
					{
						$reference->title = $m['title'];
		
					
						$reference->authorstring = $m['authorstring'];
						$reference->authorstring = preg_replace('/\s+;\s+/u', ';', $reference->authorstring);
						$reference->authors = authors_from_string($reference->authorstring);				
					
					}
					else
					{
						$reference->title = $li->plaintext;
					}
				
					$references[] = $reference;
				
					$count++;
		
		
				}
			}
			
			// print_r($references);
			
			
			//exit();
	
	
			$count = 0;
		
			foreach ($div->find('script') as $script)
			{
				if (0)
				{
					echo $script->outertext . "\n";
				}
		
				if (preg_match('/index:\s+(?<index>\d+)/', $script->outertext, $m))
				{
					if (0)
					{
						print_r($m);
					}
				
					$references[$count]->scan_page = $m['index'];
					
					$references[$count]->guid = $code . '-' . $references[$count]->scan_page;
					
					$references[$count]->pdf = "http://bibliotheques.mnhn.fr/EXPLOITATION/infodoc/ged/viewportalpublished.ashx?eid=IFD_FICJOINT_" . $code . "_1#page=" . ($references[$count]->scan_page + 1);

					$references[$count]->image = "https://aipbvczbup.cloudimg.io/s/height/800/"
						. "http://bibliotheques.mnhn.fr/EXPLOITATION/infodoc/digitalCollections/thumb.ashx?seid=" . $code . "&i=" . $code . '_' . str_pad(($references[$count]->scan_page + 1), 4, '0', STR_PAD_LEFT) . '.JPG&s=large';
						
					if (0)
					{
						print_r($references[$count]);
					}

					$count++;
				}
				else
				{
					echo "-- Badness\n";
					//echo $script->outertext . "\n";
					//exit();
				}
		
			}
		}


		//print_r($references);
		
		// dump to SQL
		foreach ($references as $reference)
		{
			$keys = array();
			$values= array();
			
			if (!isset($reference->guid))
			{
				echo "No guid!\n";
				
				print_r($reference);
				exit();
				
				
				$reference->guid = $code . '-' . uniqid();
			
			}
			
			
			foreach ($reference as $k => $v)
			{
				switch ($k)
				{
				
					// eat
					case 'authorstring':
						break;
						
						
					// array
					case 'authors':
						$keys[] = '`' . $k . '`';
						$values[] = '"' . addcslashes(join(";", $v), '"') . '"';
						break;							
											
					default:
						$keys[] = '`' . $k . '`';
						$values[] = '"' . addcslashes($v, '"') . '"';
						break;					
				}
			
			}

			if (1)
			{
				echo 'REPLACE INTO publications_mnhn(' . join(',', $keys) . ') values('
				. join(',', $values) . ');' . "\n";
			}
	
		
		}
		

		

	}
}


?>
