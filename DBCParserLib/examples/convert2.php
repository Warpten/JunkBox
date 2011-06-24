<?php
// Defining Regular Expressions
define('CONSTANT_DEFINITION', '#\#define ([A-Z_]+) ([0-9]+)(.+)#');
define('COMMENT_BLOCK', '#(.*)/\*(.+)\*/(.*)#sU');
define('COMMENTED_FIELD', '#//(char\*|uint32|int32|float|char|uint8)#');
define('HEADER_TO_REMOVE', '#\#(include|pragma|if|else|endif|define)(.*)#');
define('EMPTY_LINE', '#^((' . chr(13) . ')*( )*(' . chr(10) . ')*)$#');
define('FUNCTION_HELPER', '#(bool|uint32|BattlegroundBracketId) ([A-Za-z0-9_]+)\((.*)\) const#');

function dump($resource)
{
	echo "<pre>";
	print_r($resource);
	echo "</pre>";
}

function write($txt)
{
	ob_start();
	echo $txt;
	ob_end_flush();
}

if ($DBC = file_get_contents('DBCStructure.h'))
{
	// Removing comment blocks
	$temps_debut = microtime(true);
	$DBC = preg_replace(COMMENT_BLOCK, '$1 $3', $DBC);
	$temps_fin = microtime(true);
	write("<pre>Removed comment blocks ... (" . round($temps_fin - $temps_debut, 4) * 1000 . "ms)");

	// Removing all commented fields to uncommented
	$temps_debut = microtime(true);
	$DBC = preg_replace(COMMENTED_FIELD, '$1', $DBC);
	$temps_fin = microtime(true);
	write("<br/>Commented fields are now uncommented ... (" . round($temps_fin - $temps_debut, 4) * 1000 . "ms)");

	// Splitting into lines
	$DBC = explode(chr(10), $DBC);
	write("<br/>File content was split following line breaks ...");

	$temps_debut = microtime(true);
	foreach ($DBC as $k => $v)
	{
		// Removing all empty lines
		if (preg_match(EMPTY_LINE, $v))
			unset($DBC[$k]);
			
		// Removing function helpers
		// TODO : make this cleaner, this is disgusting
		if (preg_match(FUNCTION_HELPER, $v))
		{
			// Search for the end of the function
			$j = $k + 1;
			$matchFound = false;
			while ($j <= count($DBC))
			{
				if (isset($DBC[$j]))
					if (substr($DBC[$j], 0, 5) == '    }')
					{
						$matchFound = true;
						break;
					}
				$j++;
			}
			
			if ($matchFound)
				for ($l = $k; $l <= $j; $l++)
					unset($DBC[$l]);
		}
	}
	$temps_fin = microtime(true);
	write("<br />Removed helper functions ... (" . round($temps_fin - $temps_debut, 4) * 1000 . "ms)");
	write("<br />------------------------------------------------------------");

	// This contains all "#define X Y" in the file in the form X => Y
	$filesConstants = array();
	foreach ($DBC as $k => $v)
		if(preg_match(CONSTANT_DEFINITION, $v))
		{
			$key = preg_replace(CONSTANT_DEFINITION, '$1', $v);
			$value = preg_replace(CONSTANT_DEFINITION, '$2', $v);
			$filesConstants[trim($key)] = $value;
			unset($DBC[$k]);
		}
	write("<br />#defines extracted and saved");
	
	// Removing all lines beginning with //, all #include
	foreach ($DBC as $k => $v)
		if (substr($v, 0, 2) == '//' || preg_match(HEADER_TO_REMOVE, $v))
			unset($DBC[$k]);
	write("<br/>Removed commented lines and pre-directions");
	
	dump($DBC);
}
else
	var_dump('Error while looking for DBCStructure.h');