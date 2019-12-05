class Graph {
    constructor() {
        this.canvas = document.getElementById("canvas");
        this.canvas.width = 800;
        this.canvas.height = 600;
        this.context = this.canvas.getContext("2d");
    }

    clear() {
        this.context.fillStyle = 'red'; // #FF0000
        this.context.fillRect(0, 0, this.canvas.width, this.canvas.height);
        
        //this.context.clearRect();
    }

    sprite(img, x, y) {
        this.context.drawImage(img, x, y);
    }

    spriteFighter(img, coords, x, y) {
        const { sx, sy, sWidth, sHeight} = coords;
        this.context.drawImage(img, sx, sy, sWidth, sHeight, x, y, sWidth, sHeight);
    }
}