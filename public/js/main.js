async function sendMoveRightRequest() {
    const response = await fetch('api/?method=move&id=0&direction=right');
    const result = await response.json();
    return result;
}

async function sendMoveLeftRequest() {
    const response = await fetch('api/?method=move&id=0&direction=left');
    const result = await response.json();
    return result;
}

async function sendHandHitRequest() {
    const response = await fetch('api/?method=hit&id=0&hitType=HANDKICK');
    const result = await response.json();
    return result;
}

async function sendLegHitRequest() {
    const response = await fetch('api/?method=hit&id=0&hitType=LEGKICK');
    const result = await response.json();
    return result;
}

async function sendStandRequest() {
    const response = await fetch('api/?method=setState&id=0&state=STANDING');
    const result = await response.json();
    return result;
}

async function sendCrouchRequest() {
    const response = await fetch('api/?method=setState&id=0&state=CROUCHING');
    const result = await response.json();
    return result;
}

async function sendJumpRequest() {
    const response = await fetch('api/?method=setState&id=0&state=JUMP');
    const result = await response.json();
    return result;
}
// change of same method's parameters should be inside one function prob :thinking:

window.onload = function () {
    const moveRightButton = document.getElementById('move_right');
    moveRightButton.addEventListener('click', async function () {
        console.log(await sendMoveRightRequest());
    });

    const moveLeftButton = document.getElementById('move_left');
    moveLeftButton.addEventListener('click', async function () {
        console.log(await sendMoveLeftRequest());
    });

    const handHitButton = document.getElementById('hit_hand');
    handHitButton.addEventListener('click', async function () {
        console.log(await sendHandHitRequest());
    });

    const legHitButton = document.getElementById('hit_leg');
    legHitButton.addEventListener('click', async function () {
        console.log(await sendLegHitRequest());
    });

    const standButton = document.getElementById('stand');
    standButton.addEventListener('click', async function () {
        console.log(await sendStandRequest());
    });

    const crouchButton = document.getElementById('crouch');
    crouchButton.addEventListener('click', async function () {
        console.log(await sendCrouchRequest());
    });

    const jumpButton = document.getElementById('jump');
    jumpButton.addEventListener('click', async function () {
        console.log(await sendJumpRequest());
    });
};
