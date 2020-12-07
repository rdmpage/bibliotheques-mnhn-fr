<?php

// extract all text files

$basedir = 'pdf-NOTUL';

$basedir = 'pdf-BMBOT';

$basedir = 'pdf-BMBAD';


$files = scandir(dirname(__FILE__) . '/' . $basedir);

//$files = array('BMBOT_S004_1979_T001_N003.pdf');

//$files = array('NOTUL_S000_1909_T001_N001.pdf');

//$files = array('NOTUL_S000_1920_T004_N001.pdf');

//$files = array('BMBAD_S004_1996_T018_N003.pdf');


foreach ($files as $filename)
{
	if (preg_match('/.pdf$/', $filename))
	{	
		$pdf_filename  = $basedir . '/' . $filename;
		$text_filename = str_replace('.pdf', '.txt', $pdf_filename);
		
		$scan_name = str_replace('.pdf', '', $filename);
		
		if (!file_exists($text_filename))
		{	
			$command = "pdftotext -enc UTF-8 -layout '" . $pdf_filename . "'";		
			echo "-- $command\n";		
			system ($command);
		}
				
		// process
	
		//$tsv_filename =  str_replace('.pdf', '.tsv', $pdf_filename);
		
		$text = file_get_contents($text_filename);

		$pages = explode("\f", $text);
		
		//print_r($pages);
		
		//$tsv_rows = array();

		
		$scan_info = new stdclass;
		$scan_info->pages = array();
		$scan_info->page_map = array();
		
		$count = 0;

		foreach ($pages as $page)
		{
			$page_number = '';
			
			$end_page 	= '';

			$lines = explode("\n", $page);
			
			//print_r($lines);

			// -- 265 --

			if (preg_match('/[-]+\s*(?<page>\d+)\s*[-]+/', $lines[0], $m))
			{
				$page_number = $m['page'];
			}
			
			if (preg_match('/[-]+\s*(?<page>I+\d+)\s*[-]+/i', $lines[0], $m))
			{
				$page_number = $m['page'];
			}
			
			// — 360 —
			if (preg_match('/—\s*(?<page>\d+)\s*—/u', $lines[0], $m))
			{
				$page_number = $m['page'];
			}

			/*
			if (preg_match('/(?<page>' . $pattern . ')\s*\.?$/', $lines[0], $m))
			{
				$page_number = $m['page'];
			}	
			*/

			$page_number = str_replace('I', '1', $page_number);
			$page_number = str_replace('i', '1', $page_number);
			
			// second line has artcle page range
			// section B, n∞ 3 : 131-169.		
			if (preg_match('/section B, n°\s+\d+\s+:\s+(?<spage>\d+)(-(?<epage>\d+))?/u', $lines[1], $m))
			{
				$page_number = $m['spage'];
				if ($m['epage'] != '')
				{
					$end_page = $m['epage'];
				}
			}
			
			// section B, Adansonia
			//section B, Adansonia, n o s 3-4 : 239-274.
			if (preg_match('/section B,\s+Adansonia,\s+(n°|n\s*[o|"]\s*s)\s+(\d+(-\d+)?)\s+:\s+(?<spage>\d+)(-(?<epage>\d+))?/u', $lines[1], $m))
			{
				$page_number = $m['spage'];
				if ($m['epage'] != '')
				{
					$end_page = $m['epage'];
				}
			}
			


			$scanned_page = new stdclass;
			$scanned_page->index = $count;
			$scanned_page->line = $lines[0];

			if ($page_number != '')
			{
				$scanned_page->page_number = $page_number;
				
				if (!isset($scan_info->page_map[$page_number]))
				{
					$scan_info->page_map[$page_number] = array();
				}
				$scan_info->page_map[$page_number][] = $scanned_page->index;
				
				if ($end_page != '')
				{
					$scanned_page->end_page = $end_page;	
				}			
				
			}
			
			$scan_info->pages[] = $scanned_page;

			$count++;

		}
		
		//print_r($scan_info);
		
		// try and update database...
		
		$n = count($scan_info->pages);
		
		for ($i = 0; $i < $n; $i++)
		{
			$page_number = -1;
			
			$end_page_number = -1;
			
			if (isset($scan_info->pages[$i]->page_number))
			{
				$page_number = $scan_info->pages[$i]->page_number;			
			}
			else
			{
				// backtrack
				$back = $i - 1;
				$stop = max(0, $i - 4);
				while ($page_number == -1 && $back > $stop)
				{
					if (isset($scan_info->pages[$back]->page_number))
					{
						$page_number = $scan_info->pages[$back]->page_number + ($i - $back);			
					}			
					$back--;
				}
			
			}
			
			
			
			if ($page_number > -1)
			{
				echo 'UPDATE publications_mnhn SET spage="'. $page_number . '" WHERE scan="' . $scan_name . '" AND scan_page="' . $i . '";' . "\n";
			
			}
			
			if (isset($scan_info->pages[$i]->end_page))
			{
				echo 'UPDATE publications_mnhn SET epage="'. $scan_info->pages[$i]->end_page . '" WHERE scan="' . $scan_name . '" AND scan_page="' . $i . '";' . "\n";
			}
			
			
			
		
		
		}
		
	}
}

?>

