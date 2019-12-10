class Graph {
    constructor() {
        this.canvas = document.getElementById("canvas");
        this.canvas.width = 1280;
        this.canvas.height = 720;
        this.context = this.canvas.getContext("2d");
    }

    clear() {
        this.context.fillStyle = 'black'; // #FF0000
        this.context.fillRect(0, 0, this.canvas.width, this.canvas.height);
        
        //this.context.clearRect();
    }

    sprite(img, x, y) {
        this.context.drawImage(img, x, y);
    }

   

    spriteFighter(img, coords, x, y) {
        const { sx, sy, sWidth, sHeight} = coords;
        this.context.drawImage(img, sx, sy, sWidth, sHeight, x, y, 200, 400);
        
    }

}