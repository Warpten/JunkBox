<form method="POST">
<textarea name="txt" rows="7" cols="120"><?php if (isset($_POST['txt'])) { echo $_POST['txt']; } ?></textarea><input type="submit" value="oK" />
</form>
<?php

// Sample
/*struct AreaGroupEntry
{
    uint32  AreaGroupId;                                    // 0
    uint32  AreaId[MAX_GROUP_AREA_IDS];                     // 1-6
    uint32  nextGroup;                                      // 7 index of next group
};*/
$regex = "#^(/){0,2}(uint32|float|int32|char\*)([ ]+)([A-Za-z0-9_]+)(((\[)([0-9A-Z_]+)(\]))?)(.+)#";
$topreg = "#^struct ([A-Za-z0-9_]+)#";
$empty = "#^// ([0-9]+)-([0-9]+)(.)+#";
$convertCorresp = array('float' => 'DBC_FLOAT',
						'uint32' => 'DBC_UINT',
						'int32' => 'DBC_INT',
						'char*' => 'DBC_STRING_LOC');
if (!isset($_POST['txt']))
	exit;
	
$lines = array_map("trim", explode(chr(10), $_POST['txt']));
if (!preg_match($topreg, $lines[0]))
	die("This is not a DBCStruct");
	
echo "Converting to DBC Structure<br />";
$namef = preg_replace($topreg, '$1', $lines[0]);
echo 'DBCStruct for '.$namef.'<br /><br />';

$unkcount = 0;
foreach ($lines as $k => $v)
{
	$file = fopen('./maps/' . $namef . '.ini', 'a+'); //Ouvre en lecture et écriture ; place le pointeur de fichier à la fin du fichier. Si le fichier n'existe pas, on tente de le créer. 
	if (substr($v, 0, 2) == chr(47).chr(47))
	{
		if (preg_match($empty, $v))
		{
			$begin = preg_replace($empty, '$1', $v);
			$end = preg_replace($empty, '$2', $v);
			for ($i = $begin; $i <= $end; $i++)
			{
				$line = 'unk'.$unkcount.' = DBC_UINT';
				echo $line . '<br />';
				fwrite($file, $line . chr(10));
				$unkcount++;
			}
		}
		else if (preg_match('#^// ([0-9]+)(.+)#', $v))
		{
			$line = 'unknown'.$unkcount.' = DBC_UINT';
			echo $line . '<br />';
			fwrite($file, $line . chr(10));
			$unkcount++;
		}
	}	
	if (preg_match($regex, $v))
	{
		$didWork = false;
		if (preg_replace($regex, '$5', $v) <> '')
		{
			$didWork = true;
			$nb = preg_replace($regex, '$8', $v);
			$isStr = preg_replace($regex, '$2', $v);
			if ($isStr == 'char*')
				$didWork = false;
				
			if ($didWork)
				for ($i = 0; $i < $nb; $i++)
				{
					$line = preg_replace($regex, '${4}' . ($i+$unkcount) . ' = $2', $v);
					$line = str_replace(array_keys($convertCorresp), array_values($convertCorresp), $line);
					echo $line . "<br />";
					fwrite($file, $line . chr(10));
				}
		}

		if (!$didWork)
		{
			$line = preg_replace($regex, '$4 = $2', $v);
			$line = str_replace(array_keys($convertCorresp), array_values($convertCorresp), $line);
			echo $line . '<br />';
			fwrite($file, $line);
		}
		fwrite($file, chr(10));
	}
	fclose($file);
}
echo "<br /><br />Structure had been saved to .ini file.";
?>