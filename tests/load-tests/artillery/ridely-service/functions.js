const axios = require("axios");
require("dotenv").config();

let cachedToken = null;
let tokenExpiresAt = null;

module.exports = {
    authenticate: (context, events, done) => {
        const now = Date.now();

        // Se temos token e ele ainda é válido, reutilize
        if (cachedToken && tokenExpiresAt && now < tokenExpiresAt) {
            // console.log('Usando token cacheado');
            context.vars.token = cachedToken;
            return done();
        }

        const url = `${process.env.KEYCLOAK_URL}/realms/${process.env.KEYCLOAK_REALM}/protocol/openid-connect/token`;
        // console.log(`url: ${url}`);
        // console.log(`
        //     client_id: ${process.env.KEYCLOAK_CLIENT_ID},
        //     client_secret: process.env.KEYCLOAK_CLIENT_SECRET,
        //     username: ${process.env.KEYCLOAK_USERNAME},
        //     password: process.env.KEYCLOAK_PASSWORD
        // `);

        axios.post(url,
            new URLSearchParams({
                grant_type: "password",
                client_id: process.env.KEYCLOAK_CLIENT_ID,
                client_secret: process.env.KEYCLOAK_CLIENT_SECRET,
                username: process.env.KEYCLOAK_USERNAME,
                password: process.env.KEYCLOAK_PASSWORD
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
            done();
        }).catch((err) => {
            console.error("Erro na autenticação:", err.response?.data || err.message);
            done();
        });
    }
};
