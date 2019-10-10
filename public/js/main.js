window.onload = function () {
    const server = new Server();
    //authorization
    document.getElementById("loginButton").addEventListener("click", async function() {
        const login = document.getElementById("login").value;
        const pass = document.getElementById("pass").value;
        if (login && pass) {
            console.log(await server.auth(login, pass));
        }else alert("no login or pass");
    });
    //game methods
    document.getElementById('move_right').addEventListener('click', async function () {
        console.log(await server.move(0, "right"));
    });

    document.getElementById('move_left').addEventListener('click', async function () {
        console.log(await server.move(0, "left"));
    });

    document.getElementById('hit_hand').addEventListener('click', async function () {
        console.log(await server.hit(0, "HANDKICK"));
    });

    document.getElementById('hit_leg').addEventListener('click', async function () {
        console.log(await server.hit(0, "LEGKICK"));
    });

    document.getElementById('stand').addEventListener('click', async function () {
        console.log(await server.setState(0, "STANDING"));
    });

    document.getElementById('crouch').addEventListener('click', async function () {
        console.log(await server.setState(0, "CROUCHING"));
    });

    document.getElementById('jump').addEventListener('click', async function () {
        console.log(await server.setState(0, "JUMP"));
    });
};
