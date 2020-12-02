<?php

$ids=array(
'NOTUL_S000_1938_T007_N001',
'NOTUL_S000_1909_T001_N001',
'NOTUL_S000_1911_T002_N001',
'NOTUL_S000_1920_T004_N001',
'NOTUL_S000_1935_T005_N001',
'NOTUL_S000_1937_T006_N001',
'NOTUL_S000_1939_T008_N001',
'NOTUL_S000_1940_T009_N001',
'NOTUL_S000_1941_T010_N001',
'NOTUL_S000_1943_T011_N001',
'NOTUL_S000_1945_T012_N001',
'NOTUL_S000_1947_T013_N001',
'NOTUL_S000_1950_T014_N001',
'NOTUL_S000_1954_T015_N001',
'NOTUL_S000_1960_T016_N001'
);

$count = 1;

$basedir = 'pdf-NOTUL';

foreach ($ids as $id)
{
	$pdf = 'http://bibliotheques.mnhn.fr/EXPLOITATION/infodoc/ged/viewportalpublished.ashx?eid=IFD_FICJOINT_' . $id . '_1';

	$article_pdf_filename = $basedir . '/' . $id . '.pdf';
	
	if (!file_exists($article_pdf_filename))
	{
		$command = "wget --timeout=20 --tries=4 '" . $pdf . "' -O '$article_pdf_filename'";
		echo $command . "\n";
		system ($command);

		
			
		// Give server a break every 10 items
		if (($count++ % 1) == 0)
		{
			$rand = rand(30000000, 80000000);
			echo "\n-- ...sleeping for " . round(($rand / 1000000),2) . ' seconds' . "\n\n";
			usleep($rand);
		}
		
	
	}
	

	


}