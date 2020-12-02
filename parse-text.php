<?php

// extract all text files

$basedir = 'pdf-NOTUL';
$files = scandir(dirname(__FILE__) . '/' . $basedir);

//$files = array('NOTUL_S000_1909_T001_N001.pdf');

//$files = array('NOTUL_S000_1920_T004_N001.pdf');


foreach ($files as $filename)
{
	if (preg_match('/.pdf$/', $filename))
	{	
		$pdf_filename  = $basedir . '/' . $filename;
		$text_filename = str_replace('.pdf', '.txt', $pdf_filename);
		
		$scan_name = str_replace('.pdf', '', $filename);
		
		if (!file_exists($text_filename))
		{	
			$command = "pdftotext -layout '" . $pdf_filename . "'";		
			echo $command . "\n";		
			system ($command);
		}
				
		// process
	
		//$tsv_filename =  str_replace('.pdf', '.tsv', $pdf_filename);
		
		$text = file_get_contents($text_filename);

		$pages = explode("\f", $text);
		
		//$tsv_rows = array();

		//print_r($pages);
		
		$scan_info = new stdclass;
		$scan_info->pages = array();
		$scan_info->page_map = array();
		
		$count = 0;

		foreach ($pages as $page)
		{
			$page_number = '';

			$lines = explode("\n", $page);

			// -- 265 --

			if (preg_match('/[-]+\s*(?<page>\d+)\s*[-]+/', $lines[0], $m))
			{
				$page_number = $m['page'];
			}
			
			if (preg_match('/[-]+\s*(?<page>I+\d+)\s*[-]+/i', $lines[0], $m))
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
		
		
		}
		
	}
}

?>

