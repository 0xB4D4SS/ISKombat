class Server {
    async sendRequest(method, data) {

        const dataArr = [];
        for (let key in data) {
            dataArr.push(`${key}=${data[key]}`);
        }

        const response = await fetch(
            `api/?method=${method}&${dataArr.join("&")}`
        );

        const answer = await response.json();

        if (answer && answer.result === "ok") {
            return answer.data;
        }
        return answer.error;
    }

    auth(login, pass) {
        return this.sendRequest("login", {login, pass});
    }
    
    move(id, direction) {
        return this.sendRequest("move", {id, direction});
    }

    hit(id, hitType) {
        return this.sendRequest("hit", {id,hitType});
    }

    setState(id, state) {
        return this.sendRequest("setState", {id, state});
    }
}