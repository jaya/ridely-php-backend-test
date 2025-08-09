# Estratégia de escalabilidade

## Estratégia de Escalabilidade da API

Para atender uma carga de até 10.000 requisições por minuto, a aplicação PHP será executada com Nginx + PHP-FPM atrás de um proxy reverso e também com os workers para processamento da fila do Redis Stream.
A aplicação está containerizada e orquestrada via Kubernetes, o que facilita o escalonamento automático com base em métricas.
Então temos a seguinte configuração:
- ridely-service-nginx:
  - inicia:
    - 1 instancia
  - escalado:
    - 2 instancias/replicas (Se necessário)
  - alterações no helm:
    - Calibrar capacidades de CPU e memoria conforme a necessidade.
- ridely-service-php-fpm:
  - inicia:
    - 4 instancias
  - escalado:
    - 8 instancias
  - alterações no helm:
    - Calibrar capacidades de CPU e memoria conforme a necessidade. 
- ridely-service-process-ride-estimates-worker:
  - inicia:
    - 4 instancias
  - escalado:
    - 6 a 8 instancias
  - alterações no helm:
    - A principio acredito que a configuração atual atende bem. Provavelmente 6 instancias vão resolver o problema.
- redis-cache-database-redis:
  - inicia:
    - 4 instancias 
      - 1 master
      - 3 replicas
  - escalado:
    - 4 instancias
      - 1 master
      - 3 replicas
  - alterações no helm:
    - Provavelmente o PVC dele precisaria ser aumentado para suportar mais volume de dados, mas quem sabe a migração para o serviço em nuvem gerenciado (ElasticCache) seja o mais recomendado.
    - Vale lembrar que o Redis tem um limite vertical também, o máximo que pode armazenar é 15GB, então se a demanda do serviço crescer muito mais, será necessário utilizar outro mecanismo de fila/stream.
- ridely-service-database:
  - inicia:
    - 1 instancias (rw)
  - escalado:
    - 3 instancias
      - 1 master (rw)
      - 2 replicas (ro)
  - alterações no helm:
    - Provavelmente o PVC dele precisaria ser aumentado para suportar mais volume de dados, mas quem sabe a migração para o serviço em nuvem gerenciado (RDS) seja o mais recomendado.
    - - Calibrar capacidades de CPU e memoria conforme a necessidade.
- auth-service-nginx:
  - inicia:
    - 1 instancia
  - escalado:
    - 2 instancias/replicas (Se necessário)
  - alterações no helm:
    - Calibrar capacidades de CPU e memoria conforme a necessidade.
    - Talvez para o banco de dados dele possa ser interessante também uma calibração.

  
Além disso, considerando o uso de estratégias de deploy inteligentes, como Canary e A/B Testing, para permitir validações progressivas em produção com menor risco.

## Otimizações no Banco de Dados

- **Índices:** É importante considerar a aplicação de índices em colunas de consulta frequente, especialmente em campos como: `created_at`, `status` e `activation_date`.
- **Read replicas:** será configurada uma instância read-only para balancear a carga de leitura e melhorar a performance de queries menos críticas.
- **Transações**: Utilização de transações com mais frequência na aplicação, principalmente em pontos críticos da mesma.
- **Views ou tabelas temporárias**: Quem sabe a criação de views ou tabelas temporárias pode ajudar na performance de consulta de dados constantes.

## Uso de Cache
Acredito que uma boa estratégia é explorar mais o uso de cache nas aplicações, seja a nível de aplicação assim como de servidor/CDNS.
- **Redis:**
    - Estimativas de corridas: 
      - Já foi aplicado uma camada de cacheamento no serviço para evitar realizar requisições externas (Location Service/Nominatim) no qual utilizamos o lat e lon para fazer o calculo de KMs.
      - Já foi aplicada uma separação entre a solicitação de cotação da estimativa da corrida (request-driver) e o processamento (Job).
        - Em caso de necessidade de melhoria, toda essa feature pode ser separada em um outro serviço ou ser realizado um planejamento para uma solução assincrona mais completa.
          - Por exemplo o endpoint request-driver pode ser assincrono, para que ele seja mais performatico. A maior diferença será a necessidade de um novo endpoint para coletar o ID criado após o processamento.
- **Cache por headers HTTP:** poderá ser configurado para endpoints de leitura estática.

## Processamento Concorrente e Filas

- A arquitetura atual usa Redis Streams mas a futura prevê o uso de **RabbitMQ**, isso nos ajudará principalmente em:
    - Processamento assíncrono de estimativas de preço, requisição de corrida.
    - Jobs de background, como persistência em banco de dados, envio de notificações, etc.
- Isso permite desacoplar operações críticas do request/response e suportar cargas maiores com resiliência.

## Serviço de localização
- Há a possibilidade de hospedar esse serviço e criar as otimizações ou usar serviço de terceiros através de licenças.
- Acredito que pode ser viável a configuração de um serviço gerenciado pela empresa já que a demanda da mesma é limitada a cidade e regiões proximas. 

## Indexação Geoespacial

- Pode ser um solução a ser considerada também, ara suportar consultas otimizadas com base em localização.
  - A principio é algo que precisa ser analisado.