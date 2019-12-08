
window.onload = function () {

const server = new Server(callChallengeCB, isAcceptChallengeCB, renderCB);
const graph = new Graph();
const image = new this.Image();
image.src = "../public/img/Sprite_N.png";
//этот метод должен вызываться внутри updateBattle
const FIGHTER_PICS = {
    STANDING: {sx: 0, sy: 0, sWidth: 705, sHeight: 2013},
    MOVING: {sx: 705, sy: 0, sWidth: 872, sHeight: 2013},
    //TODO: cut all fighter pics, depending on state
}

function render(data) {
    console.log(data);
    graph.clear();
    graph.spriteFighter(image, FIGHTER_PICS.STANDING, data.fighters[0].x, data.fighters[0].y);
}
//этот метод должен вызываться внутри updateBattle
function renderCB(result) {
    render(result);
    
    //graph.sprite(image, 100, 200);
}

function callChallengeCB() {
    document.getElementById('challenge').style.display = "block";
    document.getElementById('accept').onclick = async function () {
        const result = await server.acceptChallenge('yes');
        if (result) {
            document.getElementById('challenge').style.display = "none";
            showPage("gamePage");
            server.sendUpdateBattle = true;
            server.updateBattle();
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
    server.sendUpdateBattle = true;
    server.updateBattle();
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

function initUsernameHeader() {
    const login = document.getElementById("login").value;
    const userLogin = document.createElement('h6');
    userLogin.innerHTML = "You are logged in as " + login;
    document.getElementById("lobbyHeader").appendChild(userLogin);
}

    //authorization
    document.getElementById("loginButton").addEventListener("click", async function() {
        const login = document.getElementById("login").value;
        const pass = document.getElementById("pass").value;
        if (login && pass) {
            const result = await server.auth(login, pass);
            if (result) {
                showPage("lobbyPage");
                initUsernameHeader();
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
                server.stopUpdateBattle();
                showPage("authPage");
            }
    });
    //game methods
    
    document.getElementById('move_right').addEventListener('click', async function () {
        console.log(await server.move("right"));
    });
    
    document.getElementById('move_left').addEventListener('click', async function () {
        console.log(await server.move("left"));
    });
    /*
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
            server.stopUpdateBattle();
            showPage("lobbyPage");
        }
    });

    showPage("authPage");
};
