## Postmortem: Uso de Redis como Fila Temporária para Cálculo Assíncrono de Corridas

### Contexto
Devido à necessidade de tornar o endpoint de estimativa de preço mais resiliente e não bloquear o cliente enquanto o processamento é feito, considerou-se adotar um modelo assíncrono baseado em filas, para garantir alta disponibilidade e melhor experiência para o usuário.

### Decisão Tomada
Foi adotado o Redis como mecanismo de fila leve, com workers que processam as mensagens de forma desacoplada. 
A escolha foi motivada por simplicidade de integração com Laravel, baixo overhead operacional e disponibilidade local na stack atual.

Também foi considerado extrair o cálculo da estimativa para um serviço separado (`pricing-service`), porém, por questões de tempo e custo de implantação, decidiu-se manter o cálculo dentro do `ridely-service` por ora. A ideia é começar com um serviço mais leve e simples, priorizando entregas rápidas com menor complexidade.

### Alternativas Consideradas
- **RabbitMQ**: Mais robusto, com maior controle de mensagens e confiabilidade, mas exigiria configuração adicional, aumentando a complexidade operacional.
- **SQS (AWS)**: Boa alternativa para ambientes em nuvem, porém não seria adequada para o ambiente local ou para o estágio inicial do projeto.
- **Kafka**: Poderoso e escalável, mas com custo e curva de adoção altos para este momento.
- **Separar o cálculo em um `pricing-service`**: Arquitetura mais aderente a separação de contextos e escalável a longo prazo, mas exigiria mais tempo de implementação, integração e orquestração. Optou-se por manter o código acoplado ao `ridely-service` temporariamente.
- **Modelo assíncrono**: Considerado, mas a implementação de filas não foi realizada devido ao limite de tempo.
- **Observabilidade**: Implementação dos serviços relacionados a observabilidade, como logs e métricas, foi considerada, mas não implementada neste momento.

### Resultados Observados
- Tempo de resposta do endpoint caiu significativamente com o uso de cache Redis.
- Ajuste de capacidades dos pods e hpa contribuiu para aplicação atender aos testes de carga de uma forma mais eficiente. 
- A arquitetura ficou mais flexível para mudanças futuras.

### Lições Aprendidas
- Redis pode ser uma boa solução intermediária, mesmo sem réplica, em estágios iniciais do projeto.
- Começar com uma arquitetura mais simples pode acelerar entregas e validações.
- Ter um plano claro de evolução ajuda a mitigar riscos de acoplamento excessivo.

### Próximos Passos
- Caso o tempo permita, implementar o mecanismo de fila com Redis como passo inicial.
- Caso contrário, manter o modelo síncrono atual com estrutura preparada para migração.
- Monitorar o comportamento do sistema com uso real e reavaliar a necessidade de desacoplamento.
- Revisitar a possibilidade de introduzir um `pricing-service` no futuro com base no crescimento da aplicação e nas necessidades de escalabilidade.

> Nota: Vale a ideia de manter apenas uma simples instância do Redis, sem réplicas, para reduzir custos e simplificar o ambiente. A arquitetura considera que essa solução possa evoluir para algo mais robusto conforme a demanda.
