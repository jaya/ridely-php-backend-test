# 🚀 Plano de Escalabilidade

## Descrição

Esse documento apresentara um plano de escalabilidade para a aplicação

## Estratégia de escalabilidade da API

- Utilização de cache para economizar recursos de infra e bancos de dados
- Utilização de rate limiting para aumentar segurança nas apis
- Uso de tags de cache para invalidação granular

## Otimizações no banco de dados (índices, particionamento, etc.)

- Utilização em indexes nas colunas usadas em consultas que contenham `WHERE`, `JOIN`, `HAVING` e `ORDER BY`
- Aplicação de indiexes compostos para filtros combinados
- Utilizar particionamento de tabelas
- Replicação de banco de dados (read only & write only)

##  Processamento Assíncrono / Concorrente 

- Uso do supervisord + Redis Queue
- Conforme escalar muito, pode ser escolhido RabbitMQ ou Kafka
- Exemplates de serviços que deverão rodar nas chamadas assincronas (disparo de e-mails, pagamentos, notificação de mensagem, cotação de preços, etc)
- Utilização de recursos como `retry` e `queue:failed` para tentar rodar processos e novamente e rodar itens que falharamm respectivamente.

##  Indexação Geoespacial 

- Utilização do PostGIS para realizar buscas com mais precisão
- Redis Geo para uma leitura de um dado volátil de forma mais rápida e eficiente

## Estratégia para escalabilidade horizontal

- Utilização de kubernetes para escalonar os containers sob demanda
- nginx com load balancing ou ALB (AWS) para distribuir o tráfego
- CDN caching
- Separação de responsabilidae de serviços