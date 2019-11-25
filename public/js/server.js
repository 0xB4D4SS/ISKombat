class Server {

    constructor(callChallengeCB, isAcceptChallengeCB) {
        this.token = null;
        this.sendIsChallenge = false;
        this.sendIsChallengeAccepted = false;
        this.callChallengeCB = callChallengeCB;
        this.isAcceptChallengeCB = isAcceptChallengeCB;
    }

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
            if (result) {
                this.stopCallChallenge();
                this.callChallengeCB();
                return;
            }
            this.startCallChallenge();
        }
    }

    stopCallChallenge() {
        this.sendIsChallenge = false;
    }

    async startCallIsChallengeAccepted() {
        if (this.sendIsChallengeAccepted) {
            const result = await this.sendRequest("isChallengeAccepted");
            if (result) {                      // why this data isn't enough to complete if?`
                this.stopCallIsChallengeAccepted();
                this.isAcceptChallengeCB();
            }
            this.startCallIsChallengeAccepted();
            return result;
        }
    }

    stopCallIsChallengeAccepted() {
        this.sendIsChallengeAccepted = false;
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
        this.stopCallIsChallengeAccepted();
        return this.sendRequest("logout");
    }

    getAllUsers() {
        return this.sendRequest("getAllUsers");
    }

    isUserChallenged(id) {
        return this.sendRequest("isUserChallenged", { id });
    }

    newChallenge(id) {
        this.sendIsChallengeAccepted = true;
        return this.sendRequest("newChallenge", { id });
    }

    acceptChallenge(answer) {
        return this.sendRequest("acceptChallenge", { answer });
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

    deleteFighter() {
        this.sendIsChallenge = true;
        this.startCallChallenge();
        return this.sendRequest("deleteFighter");
    }
}
