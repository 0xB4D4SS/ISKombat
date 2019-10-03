async function sendMoveRequest() {
    const response = await fetch("api/?method=move");
    const result = await response.json();
    return result;
}

window.onload = function() {
    const moveButton = this.document.getElementById("move");
    moveButton.addEventListener("click", 
                                async function() {
                                    console.log(await sendMoveRequest());
                                }
    );
}