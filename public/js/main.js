async function sendMoveRequest() {
    const response = await fetch('api/?method=move&id=1&direction=left');
    const result = await response.json();
    return result;
}

window.onload = function () {
    const moveButton = document.getElementById('move');
    moveButton.addEventListener('click', async function () {
        console.log(await sendMoveRequest());
    });
};
