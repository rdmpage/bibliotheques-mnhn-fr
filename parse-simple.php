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



$files = scandir($basedir);

// debug

//$files= array('NOTUL_S000_1960_T016_N001 serial display.html');

//$files= array('NOTUL_S000_1920_T004_N001 serial display.html');

//$files= array('NOTUL_S000_1911_T002_N001 serial display.html');



foreach ($files as $filename)
{
	// echo $filename . "\n";
	
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
			$issn		= '0240–8937';
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
			echo "Bad\n";
			
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
			echo "Bad\n";
			
		}		
		
		
		
	
		$html = file_get_contents($basedir . '/' . $filename);
		
		$dom = str_get_html($html);		
		
		$divs = $dom->find('div[id=TableOfContents1]');

		foreach ($divs as $div)
		{

			//echo $div->plaintext . "\n\n";
			//echo $div->outertext . "\n\n";
	
			
			
			if (0)
			{
				preg_match_all("/\{ fileName: '([^']+)', index: (?<index>\d+) }/", $div->outertext, $m);
				
				print_r($m);
				
				
			
			}
			else
			{
				$count = 0;
				foreach ($div->find('li') as $li)
				{	
					//echo $li->plaintext . "\n";
			
			
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
				
					$references[] = $reference;
				
					$count++;
		
		
				}
			}
			
			// print_r($references);
			
			
			//exit();
	
	
			$count = 0;
		
			foreach ($div->find('script') as $script)
			{
				//echo $script->outertext . "\n";
		
				if (preg_match('/index:\s+(?<index>\d+)/', $script->outertext, $m))
				{
					//print_r($m);
				
					$references[$count]->scan_page = $m['index'];
					
					$references[$count]->guid = $code . '-' . $references[$count]->scan_page;
					
					$references[$count]->pdf = "http://bibliotheques.mnhn.fr/EXPLOITATION/infodoc/ged/viewportalpublished.ashx?eid=IFD_FICJOINT_" . $code . "_1#page=" . ($references[$count]->scan_page + 1);

					$references[$count]->image = "https://aipbvczbup.cloudimg.io/s/height/800/"
						. "http://bibliotheques.mnhn.fr/EXPLOITATION/infodoc/digitalCollections/thumb.ashx?seid=" . $code . "&i=" . $code . '_' . str_pad(($references[$count]->scan_page + 1), 4, '0', STR_PAD_LEFT) . '.JPG&s=large';

					$count++;
				}
				else
				{
					echo "Badness\n";
					exit();
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
