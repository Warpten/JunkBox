var chessCanvas = {
    squareSize: 60, // Default set to 60 but the user may want to enhance or decrease it
    squareColours: new Array("f5f5dc", "708090"), // White square - Dark square
    coloursSets: new Array(new Array("EEEED2", "769656"), // This one is only for reference, not used.
                            new Array("EFEFEF", "ABABAB"),
                            new Array("CDD1D4", "979B9E")),
    dom: 0, // Contains the board's DOM reference, must be a canvas
    
    /*
     * Draws the board, including pieces
     */
    drawBoard: function(domElement)
    {
        var context = domElement.getContext('2d');
        this.dom = domElement;
        var colors = this.squareColours;    
        var max = this.squareSize * 8;
        var size = this.squareSize;
        domElement.setAttribute("width", (size * 8) + "px");
        domElement.setAttribute("height", (size * 8) + "px");
        domElement.style.border = "2px solid black";
        
        for (var i = 0; i < 8; i++)
        {
            for (var j = 0; j < 8; j++)
            {
                context.fillStyle = "#" + colors[i % 2 ? (j % 2 ? 0 : 1 ) : (j % 2 ? 1 : 0 )];
                context.fillRect(i * size, j * size, size, size);
            }
        }
    },
    
    /*
     * Immediately modifies the squares' colours
     */
    setSquareColours: function(light, dark)
    {
        var oldColours = this.squareColours;
        // Convert the old colours to RGB
        var oldLightRComponent = this.hexToR(oldColours[0]);
        var oldLightGComponent = this.hexToG(oldColours[0]);
        var oldLightBComponent = this.hexToB(oldColours[0]);
        var oldDarkRComponent = this.hexToR(oldColours[1]);
        var oldDarkGComponent = this.hexToG(oldColours[1]);
        var oldDarkBComponent = this.hexToB(oldColours[1]);
        // Also convert the new colours
        this.squareColours = new Array(light, dark);
        var newLightRComponent = this.hexToR(light);
        var newLightGComponent = this.hexToG(light);
        var newLightBComponent = this.hexToB(light);
        var newDarkRComponent = this.hexToR(dark);
        var newDarkGComponent = this.hexToG(dark);
        var newDarkBComponent = this.hexToB(dark);
        
        var ctx = this.dom.getContext('2d');
        
        var imageData = ctx.getImageData(0, 0, this.dom.width, this.dom.height);
        var data = imageData.data;
        
        for (var i = 0, n = data.length; i < n; i += 4)
        {
            var red = data[i]; // red
            var green = data[i + 1]; // green
            var blue = data[i + 2]; // blue
            if (red == oldLightRComponent && green == oldLightGComponent && blue == oldLightBComponent)
            {
                data[i] = newLightRComponent;
                data[i + 1] = newLightGComponent;
                data[i + 2] = newLightBComponent;
            }
            else if (red == oldDarkRComponent && green == oldDarkGComponent && blue == oldDarkBComponent)
            {
                data[i] = newDarkRComponent;
                data[i + 1] = newDarkGComponent;
                data[i + 2] = newDarkBComponent;
            }
        }
        
        ctx.putImageData(imageData, 0, 0);
    },
    
    /*
     * Modifies the size of the board. This function does trigger its regeneration, so beware with loaded boards
     */
    modBoardSize: function(i)
    {
        if (this.squareSize < 40)
        {
            this.squareSize = 40;
            return;
        }
        if (this.squareSize > 80)
        {
            this.squareSize = 80;
            return;
        }
        
        this.squareSize += i;
        this.clearBoard();
        this.drawBoard(this.dom);
    },
    
    /*
     * Forces a new value for the board.
     */
    setNewBoardSize: function(i)
    {
        this.squareSize = parseInt(i / 8);
        this.clearBoard();
        this.drawBoard(this.dom);
    },
    
    /*
     * Clears the board
     */
    clearBoard: function()
    {
        this.dom.width = this.dom.width;
    },
    
    /*
     * Helpers
     */
    hexToR: function(h) { return parseInt((this.cutHex(h)).substring(0,2),16) },
    hexToG: function(h) { return parseInt((this.cutHex(h)).substring(2,4),16) },
    hexToB: function(h) { return parseInt((this.cutHex(h)).substring(4,6),16) },
    cutHex: function(h) { return (h.charAt(0)=="#") ? h.substring(1,7):h },
}