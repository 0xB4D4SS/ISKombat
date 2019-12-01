
window.onload = function () {

const server = new Server(callChallengeCB, isAcceptChallengeCB);
const graph = new Graph();
const image = new this.Image();
image.src = "";
//этот метод должен вызываться внутри updateBattle
function render() {
    graph.clear();
    graph.sprite(image, 100, 200);
}

function callChallengeCB() {
    document.getElementById('challenge').style.display = "block";
    document.getElementById('accept').onclick = async function () {
        const result = await server.acceptChallenge('yes');
        if (result) {
            document.getElementById('challenge').style.display = "none";
            showPage("gamePage");
            render();
        }
    };
    document.getElementById('decline').onclick = async function () {
        const result = await server.acceptChallenge('no');
        if (result) {
            document.getElementById('challenge').style.display = "none";
            server.sendIsChallenge = true;
            server.startCallChallenge();
        }
    };
}

function isAcceptChallengeCB() {
    server.stopCallIsChallengeAccepted();
    showPage('gamePage');
    render();
}

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
    button.addEventListener('click', async function() {
        const result = await server.isUserChallenged(user.id);
        if (result) {
            server.stopCallIsChallengeAccepted();
            server.startCallChallenge();
            alert(user.login + " already challenged by someone else!");
        }
        else {
            server.newChallenge(user.id);
            server.stopCallChallenge();
            server.startCallIsChallengeAccepted();
        }
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

    document.getElementById("refreshLobby").addEventListener("click", function() {
        initLobbyPage();
    });

    document.getElementById("logoutButton").addEventListener("click", async function() {
            const result = await server.logout();
            if (result) {
                showPage("authPage");
            }
    });
    //game methods
    /*
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
    */
    document.getElementById("exitBattle").addEventListener("click", async function() {
        const result = await server.deleteFighter();
        if (result) {
            showPage("lobbyPage");
        }
    });
};
