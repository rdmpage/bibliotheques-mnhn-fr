<?php

$ids=array(
'BMBOT_S004_1979_T001_N001',
'BMBOT_S004_1979_T001_N002',
'BMBOT_S004_1979_T001_N003',
'BMBOT_S004_1979_T001_N004',
'BMBOT_S004_1980_T002_N001',
'BMBOT_S004_1980_T002_N002',
'BMBOT_S004_1980_T002_N003',
'BMBOT_S004_1980_T002_N004',
);

$basedir = 'html-BMBOT';


$count = 1;




foreach ($ids as $id)
{
	$url = 'https://bibliotheques.mnhn.fr/EXPLOITATION/infodoc/digitalCollections/viewerpopup.aspx?seid=' . $id;

	$html_filename = $basedir . '/' . $id . '.html';
	
	if (!file_exists($html_filename))
	{
		$command = "wget --timeout=20 --tries=4 '" . $url . "' -O '$html_filename'";
		echo $command . "\n";
		system ($command);
			
		// Give server a break every 10 items
		if (($count++ % 1) == 0)
		{
			$rand = rand(1000000, 10000000);
			echo "\n-- ...sleeping for " . round(($rand / 1000000),2) . ' seconds' . "\n\n";
			usleep($rand);
		}
	
	}

}