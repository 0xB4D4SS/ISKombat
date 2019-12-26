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
    }

    fillRect(x, y, width, height, color) {
        this.context.fillStyle = color || '#000';
        this.context.fillRect(x, y, width, height);
    }

    lifeBar(health, x, y) {
        this.fillRect(x, y - 20, 100, 20);
        this.fillRect(x, y - 20, health, 20, '#0f0');
    }

    sprite(img, x, y) {
        this.context.drawImage(img, x, y);
    }

    spriteFighter(img, coords, x, y, state) {
        var picWidth;
        var picHeight;
        const { sx, sy, sWidth, sHeight } = coords;
        switch (state) {
            case "STANDING":
            case "HITARM":
                picWidth = 150;
                picHeight = 300;
            break;
            case "HITLEG":
                picWidth = 250;
                picHeight = 300;
            break;
            case "DEAD": 
                picWidth = 400;
                picHeight = 75;
            break;
        }
        this.context.drawImage(img, sx, sy, sWidth, sHeight, x, y, picWidth, picHeight);
    }

    textOut(text, x, y) {
        this.context.fillStyle = '#f00';
        this.context.font = "48px Arial";
        this.context.fillText(text, x, y);
    }
}