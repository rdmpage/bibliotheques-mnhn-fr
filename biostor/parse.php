<?php

// parse a BioStor TSV dump and add identifiers

//----------------------------------------------------------------------------------------

$filename = '0240-8937.tsv';
$filename = '0181-0626.tsv';

$headings = array();

$row_count = 0;

$file = @fopen($filename, "r") or die("couldn't open $filename");
		
$file_handle = fopen($filename, "r");
while (!feof($file_handle)) 
{
	$row = fgetcsv(
		$file_handle, 
		0, 
		"\t" 
		);
		
	$go = is_array($row);
	
	if ($go)
	{
		if ($row_count == 0)
		{
			$headings = $row;		
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
		
			//print_r($obj);	
			
			if (
				isset($obj->issn)
				&& isset($obj->volume)
				&& isset($obj->spage)
				)
			{
				echo 'UPDATE publications_mnhn SET biostor=' . $obj->reference_id . ' WHERE '
					. 'issn="' . $obj->issn . '" AND volume="' . $obj->volume . '" AND spage="' . $obj->spage . '";' . "\n";
			
				echo 'UPDATE publications_mnhn SET BHL_Page=' . $obj->PageID . ' WHERE '
					. 'issn="' . $obj->issn . '" AND volume="' . $obj->volume . '" AND spage="' . $obj->spage . '";' . "\n";
					
				if (isset($obj->epage))
				{
					echo 'UPDATE publications_mnhn SET epage=' . $obj->epage . ' WHERE '
						. 'issn="' . $obj->issn . '" AND volume="' . $obj->volume . '" AND spage="' . $obj->spage . '";' . "\n";
					
				}
			}
			
			
		}
	}	
	$row_count++;
}
?>

