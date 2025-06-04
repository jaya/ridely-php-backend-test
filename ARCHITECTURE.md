# 🏗️ Proposta de Arquitetura (System Design)

## Descrição

Esse documento possui uma proposta de arquitetura para a solução do sistema

## Diagrama

- [Diagrama](https://excalidraw.com/#json=8g-chxXUe3tlX55SM2tvU,R78emKTSyaTkr4Vp1Uu5bQ)

## Estrutura de arquivos

A estrutura de arquivos desse projeto poderá se manter a mesma, já que estamos validando o MVP. À medida que fizer sentido investir em tech, pensaremos DDD para subdividir entre:
Passageiro e Motorista

## Estratégia de comunicação entre serviços

- Todas as requisições enviadas para o back-end deverão ser enviadas via API REST, podendo ser (GET, POST, PUT, DELETE ou PATCH)
- Uma vez que o back-end receba uma requisição, esta deverá chamar o evento responsável por armazenar os processos em fila (Redis, Kafka, RabbitMQ)
- Quando o processo da fila por lido, cada processamento deverá ser executado de forma independente.

## Estratégia de escalabilidade (horizontal, filas, workers, etc.)

- Para garantir esacabilidade, deveremos utilizar o supervisord para podermos ler mais de um processo PHP em paralelo.
- O supervisor por auscultar o evento dos works pré-estabelecidos, que por sua vez deverão interpretar cada processo de seus respectivos eventos.

## Estratégia de autenticação e segurança (JWT, OAuth, etc.)

- Para a camada de autenticação, deveremos utilizar JWT, contendo uma chave secreta do lado da aplicação.
- Uma vez que cada login for realizado, a chave secreta será utilizada para fazer o encode/decode desta senha, tornando a comunicação RESTA segura.
- As rotas preotegidas deverão ter um middleware no app, que farão parte da validação da sessão de usuário e garantirão a segurança do acesso aos dados.

## Ferramentas de observabilidade e rastreamento sugeridas

- Para que possamos ter uma melhor observabilidade, poderemos utilizar o grafana para inspecionar as máquinas e datadog para observar os erros a nivel de aplicação

## Considerações sobre deploy, containers e boas práticas

- A aplicação deverá ter 3 arquivos `docker-compose.yaml` um para dev, outro para staging e um para prod. Dssa forma os ambientes deverão ser uniformes
- Para que possamos manter o mesmo padrão de codificação entre o time, poderemos utilizar o PHP-CS-Fixer na pipe line.
- A pipe em si deverão rodar o PHP-CS-FIXER além de todos os testes
- Cada aplicação (App, servidor, cache, supervisord) deverá rodar em um container isolado para facilitar manutenção e escalabilidade
