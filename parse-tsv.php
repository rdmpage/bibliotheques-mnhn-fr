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
// get
function get($url, $accept = '')
{
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); 
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
	
	if ($accept != '')
	{
		curl_setopt($ch, CURLOPT_HTTPHEADER, 
		array(
			"Accept: " . $accept 
			)
		);
	}
	  	
	$response = curl_exec($ch);
	if($response == FALSE) 
	{
		$errorText = curl_error($ch);
		curl_close($ch);
		die($errorText);
	}
	
	$info = curl_getinfo($ch);
	$http_code = $info['http_code'];
	
	curl_close($ch);
	
	return $response;
}
//----------------------------------------------------------------------------------------
function bhl_item_from_pageid($PageID)
{
	$ItemID = 0;
	
	$parameters = array(
		'op' => 'GetPageMetadata',
		'pageid' => $PageID,
		'ocr' => 'false',
		'names' => 'false',
		'format' => 'json',
		'apikey' => '0d4f0303-712e-49e0-92c5-2113a5959159'
	);

	$url = 'https://www.biodiversitylibrary.org/api2/httpquery.ashx?' . http_build_query($parameters);
	
	//echo $url . "\n";
	
	$json = get($url);
	
	//echo $json;

	if ($json != '')
	{
		$obj = json_decode($json);
		
		//print_r($obj);
			
		if ($obj->Status == 'ok')
		{
			$ItemID = $obj->Result->ItemID;
		}
	}	
	
	//echo "ItemID=$ItemID\n";

	return $ItemID;
}


//----------------------------------------------------------------------------------------
// Find ith page in BHL item. BHL URLs number pages by orde rin list of page scans)
function bhl_ith_page_item($ItemID, $page_number)
{
	$PageID = 0;
	
	$parameters = array(
		'op' => 'GetItemMetadata',
		'itemid' => $ItemID,
		'pages' => 'true',
		'ocr' => 'false',
		'parts' => 'true',
		'format' => 'json',
		'apikey' => '0d4f0303-712e-49e0-92c5-2113a5959159'
	);

	$url = 'https://www.biodiversitylibrary.org/api2/httpquery.ashx?' . http_build_query($parameters);

	$json = get($url);

	if ($json != '')
	{
		$obj = json_decode($json);

		//print_r($obj);

		$page = $obj->Result->Pages[$page_number - 1];

		//print_r($page);
		 
		$PageID = $page->PageID;

	}
	
	return $PageID;
}

//----------------------------------------------------------------------------------------
// Get PageID of page pointed to by a BHL URL 
// e.g. http://www.biodiversitylibrary.org/item/96891#page/697/mode/1up
function bhl_page_from_bhl_url($url)
{
	$PageiD = 0;
	
	$ItemID = 0;
	$PageID = 0;
	
	// Link to page URL shown in web browser when visiting BHL
	if (preg_match('/http[s]?:\/\/(www\.)?biodiversitylibrary.org\/item\/(?<item>\d+)#page\/(?<page>\d+)(\/mode\/\d+up)?/', $url, $m))
	{
		//print_r($m);
		$ItemID = $m['item'];
		$page_number = $m['page'];
	}

	// Link to BHL item with page offset
	if (preg_match('/http[s]?:\/\/(www\.)?biodiversitylibrary.org\/item\/(?<item>\d+)#(?<page>\d+)/', $url, $m))
	{
		//print_r($m);
		$ItemID = $m['item'];
		$page_number = $m['page'];
	}

	// Link to BHL page with page offset
	// http://www.biodiversitylibrary.org/page/15733891%23page/417/mode/1up
	if (preg_match('/http[s]?:\/\/(www\.)?biodiversitylibrary.org\/page\/(?<pageid>\d+)#page\/(?<page>\d+)(\/mode\/\d+up)?/', $url, $m))
	{
		//print_r($m);
		$ItemID = bhl_item_from_pageid($m['pageid']);
		
		if ($ItemID != 0)
		{
			// $page_number = $m['page'] + 1;
			$page_number = $m['page'];
		}
	}
	
	
	
	// Link to BHL page using PageID, just extract the PageID
	if (preg_match('/http[s]?:\/\/(www\.)?biodiversitylibrary.org\/page\/(?<page>\d+)$/', $url, $m))
	{
		$PageID = $m['page'];
	}
	
	if ($ItemID != 0)
	{
		$PageID = bhl_ith_page_item($ItemID, $page_number);
	}
	
	return $PageID;
}
	

//----------------------------------------------------------------------------------------



$headings = array();

$row_count = 0;

$filename = 'tsv/0181–0634.tsv';

$filename = "edited-sheets/Bulletin du Muséum national d'histoire naturelle. Section B, botanique, biologie et écologie végétales, phytochimie - Sheet1.tsv";
$filename = "edited-sheets/Cryptogamie. Algologie - Sheet1.tsv";
//$filename = "edited-sheets/1.tsv";

$filename = "edited-sheets/Notulae Systematicae - Sheet1.tsv";


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
				// PageID or Item-based URL
				if (preg_match('/http[s]?:\/\/(www\.)?biodiversitylibrary.org\/item\/(?<item>\d+)#page\/(?<page>\d+)(\/mode\/\d+up)?/', $obj->{'BHL_Page'}, $m))
				{
					//print_r($m);
					$ItemID = $m['item'];
					$page_number = $m['page'];
					
					$PageID = bhl_ith_page_item($ItemID, $page_number);
					
					if ($PageID != 0)
					{					
						$obj->url = 'http://www.biodiversitylibrary.org/page/' . $PageID;
					
						if (!isset($bhl_pages[$PageID]))
						{
							$bhl_pages[$PageID] = array();
						}
						$bhl_pages[$PageID][] = $obj; 										
					}
				}				
				else				
				{
					$PageID = bhl_page_from_bhl_url($obj->{'BHL_Page'});
					$obj->url = 'http://www.biodiversitylibrary.org/page/' . $PageID;
				
					/*
					$PageID = $obj->{'BHL_Page'};
					$PageID = preg_replace('/https?:\/\/(www.)?biodiversitylibrary.org\/page\//', '', $PageID);
					$obj->url = $obj->{'BHL_Page'};
					$obj->url = str_replace('https', 'http', $obj->url);
					*/
				
					if (!isset($bhl_pages[$PageID]))
					{
						$bhl_pages[$PageID] = array();
					}
					$bhl_pages[$PageID][] = $obj; 
				}
			}
			
			echo reference_to_ris($obj);
			
			
		}
	}	
	$row_count++;	
	
}	

//print_r($bhl_pages);


echo "Check for potential duplications\n\n";

foreach ($bhl_pages as $PageID => $articles)
{
	if (count($articles) > 1)
	{
		echo "More than one article at http://www.biodiversitylibrary.org/page/$PageID\n";
		
		$spages = array();
		
		foreach ($articles as $obj)
		{
			$spages[] = $obj->spage;
		}
		
		$spages = array_unique($spages);
		
		if (count($spages) > 1)
		{
			// problem
			
			echo "\n\n*** Articles have different spages - badness ***\n";
			
			foreach ($articles as $obj)
			{
				print_r($obj);
			}
			
		
		}
		
	
		
		echo "----------\n";
	}

}


