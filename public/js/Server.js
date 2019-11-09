class Server {

    token = null;
    sendIsChallenge = false;

    async sendRequest(method, data) {
        const dataArr = [];
        for (let key in data) {
            dataArr.push(`${key}=${data[key]}`);
        }
        if (this.token) {
            dataArr.push(`token=${this.token}`);
        }
        const response = await fetch(
            `api/?method=${method}&${dataArr.join("&")}`
        );
        const answer = await response.json();
        if (answer && answer.result === "ok") {
            return answer.data;
        }
        return false;
    }

    async startCallChallenge() {
        if (this.sendIsChallenge) {
            const result = await this.sendRequest("isChallenge");
            this.startCallChallenge();
            return result;
        }
    }

    stopCallChallenge() {
        this.sendIsChallenge = false;
    }

    async auth(login, pass) {
        const result = await this.sendRequest("login", {login, pass});
        if (result && result.token) {
            this.token = result.token;
            this.sendIsChallenge = true;
            this.startCallChallenge();
        }
        return result;
    }

    register(login, pass) {
        const result = this.sendRequest("register", {login, pass});
        if (result && result.data) {
            this.token = result.token;
        }
        return result;
    }

    logout() {
        this.stopCallChallenge();
        return this.sendRequest("logout");
    }

    getAllUsers() {
        return this.sendRequest("getAllUsers");
    }

    newChallenge(id) {
        return this.sendRequest("newChallenge", { id });
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