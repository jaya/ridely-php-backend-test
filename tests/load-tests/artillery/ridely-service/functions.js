const {faker} = require('@faker-js/faker');
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
            cachedTokens[username] = {token: cachedToken, expiresAt: tokenExpiresAt};
            done();
        }).catch((err) => {
            console.error("Erro na autenticação:", err.response?.data || err.message);
            console.error("User and password:", username, password)
            done();
        });
    },
    requestDriverDataGenerator: (context, events, done) => {
        // Gera dados dinâmicos para a corrida
        const data = {
            passenger: {
                name: faker.person.fullName(),
                email: faker.internet.email(),
            },
            pick_up: faker.location.streetAddress(),
            drop_off: faker.location.streetAddress(),
        };

        // console.log('data', data);
        // Armazena os dados no contexto do Artillery
        // O Artillery irá usar isso como a variável `ride_data`
        context.vars.ride_data = data;

        // Chama o próximo passo no cenário
        return done();
    }
};
