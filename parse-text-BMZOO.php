<?php

// extract all text files

$basedir = 'pdf-NOTUL';

$basedir = 'pdf-BMBOT';

$basedir = 'pdf-BMBAD';

$basedir = 'pdf-BUMHN';

$basedir = 'pdf-BMAZO';

$basedir = '/Volumes/Samsung_T5/bibliotheques-mnhn-fr/pdf-BMNHN';
$basedir = '/Volumes/Samsung_T5/bibliotheques-mnhn-fr/pdf-BMZOO';



//$files = scandir(dirname(__FILE__) . '/' . $basedir);

$files = scandir($basedir);


//$files = array('BMBOT_S004_1979_T001_N003.pdf');

//$files = array('NOTUL_S000_1909_T001_N001.pdf');

//$files = array('NOTUL_S000_1920_T004_N001.pdf');

//$files = array('BMBAD_S004_1996_T018_N003.pdf');

/*
$files=array(
'BMZOO_S003_1971_T001_N001.pdf'
);
*/



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
		
		$n = count($pages);
		
		$last_page = $n - 6;
		
		$lines =  explode("\n", $pages[$last_page]);
		
		//print_r($lines);
		
		$page_line = count($lines)  -  4;
		
		echo "-------\n";
		echo $lines[$page_line] . "\n";
	
		
		
	
		
	}
}

?>

