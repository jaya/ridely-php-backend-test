const axios = require("axios");
require("dotenv").config();

let cachedTokens = [];

module.exports = {
    authenticate: (context, events, done) => {
        const now = Date.now();
        const username = context.vars.username || process.env.KEYCLOAK_USERNAME;
        const password = context.vars.password || process.env.KEYCLOAK_PASSWORD;

        // Se temos token e ele ainda é válido, reutilize
        if (cachedTokens[username]) {
            let tokenData = cachedTokens[username];
            let cachedToken = tokenData.token;
            let tokenExpiresAt = tokenData.expiresAt;
            if (tokenExpiresAt && now < tokenExpiresAt) {
                // console.log('Usando token cacheado para o usuario: ', username);
                context.vars.token = cachedToken;
                return done();
            }

        }

        const url = `${process.env.KEYCLOAK_URL}/realms/${process.env.KEYCLOAK_REALM}/protocol/openid-connect/token`;
        // console.log(`url: ${url}`);
        // console.log(`
        //     client_id: ${process.env.KEYCLOAK_CLIENT_ID},
        //     client_secret: process.env.KEYCLOAK_CLIENT_SECRET,
        //     username: ${username},
        //     password: ${password}
        // `);

        axios.post(url,
            new URLSearchParams({
                grant_type: "password",
                client_id: process.env.KEYCLOAK_CLIENT_ID,
                client_secret: process.env.KEYCLOAK_CLIENT_SECRET,
                username: username,
                password: password
            }),
            {
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                }
            }
        ).then((res) => {
            const accessToken = res.data.access_token;
            const expiresIn = res.data.expires_in || 60;

            cachedToken = accessToken;
            tokenExpiresAt = now + expiresIn * 1000 - 5000; // expira 5s antes para segurança

            context.vars.token = cachedToken;
            cachedTokens[username] = { token: cachedToken, expiresAt: tokenExpiresAt };
            done();
        }).catch((err) => {
            console.error("Erro na autenticação:", err.response?.data || err.message);
            console.error("User and password:", username, password)
            done();
        });
    }
};
