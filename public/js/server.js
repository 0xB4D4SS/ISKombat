class Server {

    async sendRequest(method, data) {

        const dataArr = [];
        for (let key in data) {
            dataArr.push(`${key}=${data[key]}`);
        }

        if (this.token) {
            dataArr.push(`${token}=${this.token}`);
        }

        const response = await fetch(
            `api/?method=${method}&${dataArr.join("&")}`
        );

        const answer = await response.json();

        if (answer && answer.result === "ok") {
            return answer.data;
        }else
        return answer.error;
    }

    auth(login, pass) {
        const result = this.sendRequest("login", {login, pass});
        if (result && result.data) {
            this.token = result.data.token;
        }
        return result;
    }

    register(login, pass) {
        const result = this.sendRequest("register", {login, pass});
        if (result && result.data) {
            this.token = result.data.token;
        }
        return result;
    }

    logout() {
        return this.sendRequest("logout", {token: this.token});
    }

    getAllUsers() {
        return this.sendRequest("getAllUsers", {token: this.token});
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