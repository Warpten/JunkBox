<?php
/* FEN string parser
 * Author Kapoeira / Cron
 */
 
class FENParser
{
    private $fen; // Contains the FEN string
    private $errorMessage;
    private $diagram;

    /*
     * Constructor
     * Arguments: string $string : the FEN string to be parsed
     * Returns: boolean
     */
    public function __construct($string)
    {
        // Example FEN string : rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1
        // Check for the validity of the passed FEN string.
        if (!$this->isValidFEN($string))
        {
            $this->setErrorMessage("The string you passed is not a valid FEN string.");
            return false;
        }
        
        // No need to give any other test, juste save the FEN string
        $this->fen = $string;
        return true;
    }
    
    /*
     * Reads the FEN to prepare printing a diagram
     */
    public function FEN2Diagram()
    {
        $fen = $this->fen;
        $A = explode(' ', $fen);
        $B = explode('/', $A[0]);
        
        foreach ($B as $lineIndex => $C)
        {
            for ($i = 0; $i < strlen($C); $i++)
            {
                if (ctype_digit($C[$i])) // Empty squares
                {
                    for ($j = 0; $j < $C[$i]; $j++)
                        $this->diagram[$lineIndex][] = "";
                }
                else // A piece
                {
                    $this->diagram[$lineIndex][] = $C[$i];
                }
            }
        }
    }
    
    public function printDiagram()
    {
        $diagram = $this->diagram;
        
        $html = '<div id="chess-diagram-wrapper"><div id="chess-diagram-background">';
        foreach ($diagram as $row => $places)
            foreach ($places as $column => $square)
                if ($square != "")
                    $html .= '<div class="' . ((strtoupper($square) == $square) ? 'w' : 'b') . strtolower($square) . '" style="position:absolute; top:' . ($row * 45) . 'px; left: ' . ($column * 45) . 'px"></div>';
        $html .= '</div></div>';
        
        return $html;
    }
    
    /* *************** */
    /*     HELPERS     */
    /* *************** */
    public function isValidFEN($string)
    {
        return preg_match("#^(([a-z0-8]{1,8})/){7}([a-z0-8]{1,8}) (w|b) ((k|q){4}|-) - ([0-9]+) ([0-9]+)$#i", $string);
    }
    
    public function setErrorMessage($string)
    {
        $this->errorMessage = $string;
    }
    
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }
}