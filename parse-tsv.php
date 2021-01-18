<?php

error_reporting(E_ALL);

//----------------------------------------------------------------------------------------
function reference_to_ris($reference)
{
	$field_to_ris_key = array(
		'title' 	=> 'TI',
		'alternativetitle' 	=> 'TT',
		'journal' 	=> 'JO',
		'secondary_title' 	=> 'JO',
		'book' 		=> 'T2',
		'issn' 		=> 'SN',
		'volume' 	=> 'VL',
		'issue' 	=> 'IS',
		'spage' 	=> 'SP',
		'epage' 	=> 'EP',
		'year' 		=> 'Y1',
		'date'		=> 'PY',
		'abstract'	=> 'N2',
		'url'		=> 'UR',
		'pdf'		=> 'L1',
		'doi'		=> 'DO',
		'notes'		=> 'N1',
		'oai'		=> 'ID',

		'publisher'	=> 'PB',
		'publoc'	=> 'PP',
		
		'publisher_id' => 'ID',
		
		'xml'		=> 'XM', // I made this up
		
		// correspondence
		
		);
		
	$ris = '';
	
	switch ($reference->genre)
	{
		case 'article':
			$ris .= "TY  - JOUR\n";
			break;

		case 'chapter':
			$ris .= "TY  - CHAP\n";
			break;

		case 'book':
			$ris .= "TY  - BOOK\n";
			break;

		default:
			$ris .= "TY  - GEN\n";
			break;
	}

	//$ris .= "ID  - " . $result->fields['guid'] . "\n";
	
	// Need journal to be output early as some pasring routines that egnerate BibJson
	// assume journal alreday defined by the time we read pages, etc.
	if (isset($reference->journal))
	{
		$ris .= 'JO  - ' . $reference->journal . "\n";
	}

	foreach ($reference as $k => $v)
	{
		switch ($k)
		{
			// eat this
			case 'journal':
				break;
				
			case 'authors':
				foreach ($v as $a)
				{
					if ($a != '')
					{
						$a = str_replace('*', '', $a);
						$a = trim(preg_replace('/\s\s+/u', ' ', $a));						
						$ris .= "AU  - " . $a ."\n";
					}
				}
				break;

			case 'alternativeauthors':
				foreach ($v as $a)
				{
					if ($a != '')
					{
						$a = str_replace('*', '', $a);
						$a = trim(preg_replace('/\s\s+/u', ' ', $a));						
						$ris .= "AT  - " . $a ."\n";
					}
				}
				break;
				
			case 'editors':
				foreach ($v as $a)
				{
					if ($a != '')
					{
						$ris .= "ED  - " . $a ."\n";
					}
				}
				break;				
				
			case 'date':
				//echo "|$v|\n";
				if (preg_match("/^(?<year>[0-9]{4})\-(?<month>[0-9]{2})\-(?<day>[0-9]{2})$/", $v, $matches))
				{
					//print_r($matches);
					$ris .= "PY  - " . $matches['year'] . "/" . $matches['month'] . "/" . $matches['day']  . "/" . "\n";
					$ris .= "Y1  - " . $matches['year'] . "\n";
				}
				else
				{
					$ris .= "Y1  - " . $v . "\n";
				}		
				break;
				
			case 'handle':
				$ris .= 'UR  - https://hdl.handle.net/' . $v . "\n";
				break;
				
			/*
			case 'jstor':
				$ris .= 'UR  - https://hdl.handle.net/' . $v . "\n";
				break;
			*/

			case 'bhl':
				$ris .= 'UR  - https://www.biodiversitylibrary.org/page/' . $v . "\n";
				break;
				
				
			default:
				if ($v != '')
				{
					if (isset($field_to_ris_key[$k]))
					{
						$ris .= $field_to_ris_key[$k] . "  - " . $v . "\n";
					}
				}
				break;
		}
	}
	
	$ris .= "ER  - \n";
	$ris .= "\n";
	
	return $ris;
}

//----------------------------------------------------------------------------------------



$headings = array();

$row_count = 0;

$filename = 'tsv/0181–0634.tsv';

$filename = "edited-sheets/Bulletin du Muséum national d'histoire naturelle. Section B, botanique, biologie et écologie végétales, phytochimie - Sheet1.tsv";


$bhl_pages = array();


$file_handle = fopen($filename, "r");
while (!feof($file_handle)) 
{
	$line = fgets($file_handle);
	
		
	$row = explode("\t",$line);
	
	//print_r($row);
	
	$go = is_array($row) && count($row) > 1;
	
	if ($go)
	{
		if ($row_count == 0)
		{
			$headings = $row;	
			//print_r($headings);
			
			
		}
		else
		{
			$obj = new stdclass;
		
			foreach ($row as $k => $v)
			{
				if ($v != '')
				{
					$obj->{$headings[$k]} = $v;
				}
			}
		
			// print_r($obj);
			
			// clean up
			
			
			$obj->genre = 'article';

			
			if (isset($obj->authors))
			{
				$authorstring = $obj->authors;
				$authorstring = str_replace (' and ', ';', $authorstring);
				$obj->authors = explode(';', $authorstring);				
			}

			if (isset($obj->date))
			{
				/*
				if (strlen($obj->date) == 7)
				{
					$obj->date .= '-00';
				}
				*/
			}
			

			if (isset($obj->{'BHL_Page'}))
			{
				$PageID = $obj->{'BHL_Page'};
				$PageID = str_replace('https?://(www.)?biodiversitylibrary.org/page/', '', $PageID);
				
				$obj->url = $obj->{'BHL_Page'};
				
				if (!isset($bhl_pages[$PageID]))
				{
					$bhl_pages[$PageID] = array();
				}
				$bhl_pages[$PageID][] = $obj; 
				
			}

			echo reference_to_ris($obj);
			
			
		}
	}	
	$row_count++;	
	
}	

//print_r($bhl_pages);

foreach ($bhl_pages as $PageID => $articles)
{
	if (count($articles) > 1)
	{
		echo "Duplicates http://www.biodiversitylibrary.org/page/$PageID\n";
		
		$spages = array();
		
		foreach ($articles as $obj)
		{
			$spages[] = $obj->spage;
		}
		
		$spages = array_unique($spages);
		
		if (count($spages) > 1)
		{
			// problem
			
			foreach ($articles as $obj)
			{
				print_r($obj);
			}
			
		
		}
		
	
		
		echo "----------\n";
	}

}


