window.onload = function() {

    const server = new Server(callChallengeCB, isAcceptChallengeCB, renderCB);
    const graph = new Graph();
    const fighter1Img = new Image();
    const fighter2Img = new Image();
    const backgroundImg = new Image();
    fighter1Img.src = "../public/img/Sprite_N.png";
    fighter2Img.src = "../public/img/Sprite_A(mirrored).png";
    backgroundImg.src = "../public/img/UDSU.png"
    //TODO: cut all fighter pics, depending on state
    const FIGHTER_PICS_right = {
        STANDING: { sx: 390, sy: 0, sWidth: 400, sHeight: 1200 },
        HITARM: { sx: 3888, sy: 0, sWidth: 540, sHeight: 1200 },
        HITLEG: { sx: 5125, sy: 0, sWidth: 850, sHeight: 1200 },
        MOVING: { sx: 1493, sy: 0, sWidth: 502, sHeight: 1200 },
        DEAD: { sx: 11992, sy: 0, sWidth: 1207, sHeight: 1200 }
    }

    const FIGHTER_PICS_left = {
        STANDING: { sx: 375, sy: 0, sWidth: 472, sHeight: 1200 },
        HITARM: { sx: 3771, sy: 0, sWidth: 668, sHeight: 1200 },
        HITLEG: { sx: 4881, sy: 0, sWidth: 854, sHeight: 1200 },
        MOVING: { sx: 1579, sy: 0, sWidth: 520, sHeight: 1200 },
        DEAD: { sx: 11996, sy: 0, sWidth: 1203, sHeight: 1200 }
    }

    function render(data) {
        graph.clear();
        //setTimeout(graph.sprite(backgroundImg, 0, 0), 1000);
        directionFighter1 = data.fighters[0].direction;
        directionFighter2 = data.fighters[1].direction;
        graph.spriteFighter(
            fighter1Img,
            FIGHTER_PICS_right[data.fighters[0].state],
            data.fighters[0].x,
            data.fighters[0].y,
            data.fighters[0].state
        );
        graph.spriteFighter(
            fighter2Img, 
            FIGHTER_PICS_left[data.fighters[1].state], 
            data.fighters[1].x, data.fighters[1].y,
            data.fighters[1].state
        );
    }

    function renderCB(result) {
        if (result && result.endBattle) {
            server.stopUpdateBattle();
            alert("END! Winner: "+result.winner+", Loser: "+result.loser+".");
            server.deleteFighter();
            showPage("lobbyPage");
            return;
        }
        render(result);
    }

    function callChallengeCB() {
        document.getElementById('challenge').style.display = "block";
        document.getElementById('accept').onclick = async function() {
            const result = await server.acceptChallenge('yes');
            if (result) {
                document.getElementById('challenge').style.display = "none";
                showPage("gamePage");
                server.sendUpdateBattle = true;
                server.updateBattle();
            }
        };
        document.getElementById('decline').onclick = async function() {
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
            } else {
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
        } else alert("no login or pass");
    });

    document.getElementById("registerButton").addEventListener("click", async function() {
        const login = document.getElementById("login").value;
        const pass = document.getElementById("pass").value;
        if (login && pass) {
            const result = server.register(login, pass);
            if (result) {
                alert("success!");
            }
        } else alert("no login or pass");
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
    document.addEventListener('keydown', function(event) {
        if (event.keyCode == 69) { // KeyE
            server.hit("HITARM");
        }
        if (event.keyCode == 81) { // KeyQ
            server.hit("HITLEG");
        }
        if (event.keyCode == 68) { // KeyD
            server.move("right");
        }
        if (event.keyCode == 65) { // KeyA
            server.move("left");
        }
    });
    /*
    document..addEventListener('click', async function () {
        console.log(await server.hit("HITARM"));
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
        const result = server.deleteFighter();
        if (result) {
            server.stopUpdateBattle();
            showPage("lobbyPage");
        }
    });

    showPage("authPage");
};