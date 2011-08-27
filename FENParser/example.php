<?php
include('fenparser.class.php');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
    <link rel="stylesheet" type="text/css" href="./fenparser.css" />
</head>

<body>
<?php
$parser = new FENParser("N2r1k2/pp1q1p1p/2nb2p1/2p5/8/1Q2P3/P4PBP/2R2RK1 w - - 3 24");
$parser->FEN2Diagram();
echo $parser->printDiagram();
?>   
</body>
</html>