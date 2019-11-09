const server = new Server();

function showPage(name) {
    document.getElementById("authPage").style.display = "none";
    document.getElementById("gamePage").style.display = "none";
    document.getElementById("lobbyPage").style.display = "none";
    document.getElementById(name).style.display = "block";
}

function addUserToLobby(user) {
    const div = document.createElement('div');
        div.innerHTML = user.login;
    const button = document.createElement('button');
        button.innerHTML = 'Challenge user';
    document.addEventListener('click', function() {
        server.newChallenge(user.id);
    });
    document.getElementById('lobbyTable').appendChild(div);
    document.getElementById('lobbyTable').appendChild(button);
}

async function initLobbyPage() {
    const users = await server.getAllUsers();
    document.getElementById("lobbyTable").innerHTML = '';
    if (users && users.length) {
        for (var user of users) {
            addUserToLobby(user);
        }
    }
}

window.onload = function () {
    //authorization
    showPage("authPage");
    document.getElementById("loginButton").addEventListener("click", async function() {
        const login = document.getElementById("login").value;
        const pass = document.getElementById("pass").value;
        if (login && pass) {
            const result = await server.auth(login, pass);
            if (result) {
                showPage("lobbyPage");
                initLobbyPage();
            }
        }else alert("no login or pass");
    });

    document.getElementById("registerButton").addEventListener("click", async function() {
        const login = document.getElementById("login").value;
        const pass = document.getElementById("pass").value;
        if (login && pass) {
            const result = server.register(login, pass);
            if (result) {
                alert("success!");
            }
        }else alert("no login or pass");
    });

    document.getElementById("logoutButton").addEventListener("click", async function() {
            const result = await server.logout();
            if (result) {
                showPage("authPage");
            }
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
