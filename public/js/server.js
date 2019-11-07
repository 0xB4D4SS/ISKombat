class Server {

    token = null;

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

    async auth(login, pass) {
        const result = await this.sendRequest("login", {login, pass});
        if (result && result.token) {
            this.token = result.token;
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
        return this.sendRequest("logout");
    }

    getAllUsers() {
        return this.sendRequest("getAllUsers");
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