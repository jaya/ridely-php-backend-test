# Teste em Carga em Development


Estou incluindo essa seção para descrever um pouco dos testes que fiz em dev.
O tempo de processamento local é péssimo, pois a maquina/CPU tem que fazer tudo ao mesmo tempo, mas ajuda a entender um pouco da configuração e comportamento.
Em um cenário de `staging/sandbox` os números seriam mais amigáveis.

## Configurações do ambiente
PODS:
```
  NAME                                                            READY   STATUS      RESTARTS   AGE
  auth-service-keycloak-0                                         1/1     Running     0          10m
  auth-service-postgresql-0                                       1/1     Running     0          10m
  ridely-cache-database-redis-master-0                            1/1     Running     0          10m
  ridely-cache-database-redis-replicas-0                          1/1     Running     0          10m
  ridely-cache-database-redis-replicas-1                          1/1     Running     0          10m
  ridely-cache-database-redis-replicas-2                          1/1     Running     0          9m43s
  ridely-database-b7fc6b564-srzk6                                 1/1     Running     0          10m
  ridely-service-migrate-job-q8dnd                                0/1     Completed   0          10m
  ridely-service-nginx-59b544768f-m98hd                           1/1     Running     0          10m
  ridely-service-php-fpm-74d9548796-85wzp                         1/1     Running     0          10m
  ridely-service-php-fpm-74d9548796-fkfm8                         1/1     Running     0          7m25s
  ridely-service-php-fpm-74d9548796-hd9rd                         0/1     Pending     0          7m25s
  ridely-service-php-fpm-74d9548796-kbxkr                         1/1     Running     0          7m55s
  ridely-service-php-fpm-74d9548796-qj5nj                         1/1     Running     0          9m56s
  ridely-service-process-ride-estimates-worker-65cff4fc64-dkf8m   1/1     Running     0          10m
  ridely-service-process-ride-estimates-worker-65cff4fc64-mnztb   1/1     Running     0          10m
  ridely-service-process-ride-estimates-worker-65cff4fc64-nrzrc   1/1     Running     0          10m
  ridely-service-process-ride-estimates-worker-65cff4fc64-p5vzl   1/1     Running     0          10m
  ridely-service-process-ride-estimates-worker-65cff4fc64-vwnmm   1/1     Running     0          10m
  ridely-service-process-ride-estimates-worker-65cff4fc64-x5c52   1/1     Running     0          10m

```
## Cenários

### Cenário 300 requisições em um minuto
Comando:
```
npm run test:ridely-service:request-driver-no-auth
```
> Notas: esse commando foi executado na pasta: [load-tests](../tests/load-tests)

Report:
```
  All VUs finished. Total time: 33 seconds

--------------------------------
Summary report @ 13:24:26(-0300)
--------------------------------

http.codes.200: ................................................................ 150
http.codes.201: ................................................................ 150
http.downloaded_bytes: ......................................................... 371934
http.request_rate: ............................................................. 10/sec
http.requests: ................................................................. 300
http.response_time:
  min: ......................................................................... 64
  max: ......................................................................... 1855
  mean: ........................................................................ 439.5
  median: ...................................................................... 308
  p95: ......................................................................... 1200.1
  p99: ......................................................................... 1436.8
http.response_time.2xx:
  min: ......................................................................... 64
  max: ......................................................................... 1855
  mean: ........................................................................ 439.5
  median: ...................................................................... 308
  p95: ......................................................................... 1200.1
  p99: ......................................................................... 1436.8
http.responses: ................................................................ 300
vusers.completed: .............................................................. 150
vusers.created: ................................................................ 150
vusers.created_by_name.Request driver (Non Authenticated): ..................... 150
vusers.failed: ................................................................. 0
vusers.session_length:
  min: ......................................................................... 274.7
  max: ......................................................................... 2552.5
  mean: ........................................................................ 907.5
  median: ...................................................................... 596
  p95: ......................................................................... 2143.5
  p99: ......................................................................... 2231

```
Notas:
O processamento dos itens em background foram encerrados junto com as requisições praticamente.

### Cenário 600 ~ 900 requisições em um minuto
Comando:
```
npm run test:ridely-service:request-driver-60-10-no-auth
```
> Notas: esse commando foi executado na pasta: [load-tests](../tests/load-tests)

Report:
```
  All VUs finished. Total time: 1 minute, 15 seconds

--------------------------------
Summary report @ 13:28:45(-0300)
--------------------------------

errors.ETIMEDOUT: .............................................................. 464
http.codes.200: ................................................................ 136
http.codes.201: ................................................................ 232
http.downloaded_bytes: ......................................................... 456504
http.request_rate: ............................................................. 10/sec
http.requests: ................................................................. 832
http.response_time:
  min: ......................................................................... 163
  max: ......................................................................... 10084
  mean: ........................................................................ 5701.5
  median: ...................................................................... 6187.2
  p95: ......................................................................... 9607.1
  p99: ......................................................................... 9999.2
http.response_time.2xx:
  min: ......................................................................... 163
  max: ......................................................................... 10084
  mean: ........................................................................ 5701.5
  median: ...................................................................... 6187.2
  p95: ......................................................................... 9607.1
  p99: ......................................................................... 9999.2
http.responses: ................................................................ 368
vusers.completed: .............................................................. 136
vusers.created: ................................................................ 600
vusers.created_by_name.Request driver (Non Authenticated): ..................... 600
vusers.failed: ................................................................. 464
vusers.session_length:
  min: ......................................................................... 660.7
  max: ......................................................................... 19261.8
  mean: ........................................................................ 10599.3
  median: ...................................................................... 10832
  p95: ......................................................................... 18220
  p99: ......................................................................... 18963.6


```
Notas:
Apesar do ETIMEDOUT, as requsições dentro dos containers foram todas realizadas com sucesso.
O processamento dos itens em background foram encerrados cerca de 1 minuto depois.

```
redis-cli XLEN ride_estimates_stream
(integer) 128
```


### Cenário 3000 requisições em um minuto
Comando:
```
npm run test:ridely-service:request-driver-60-50-no-auth
```
> Notas: esse commando foi executado na pasta: [load-tests](../tests/load-tests)

Report:
```
  All VUs finished. Total time: 1 minute, 15 seconds

--------------------------------
Summary report @ 13:28:45(-0300)
--------------------------------

All VUs finished. Total time: 1 minute, 11 seconds

--------------------------------
Summary report @ 13:35:22(-0300)
--------------------------------

errors.ETIMEDOUT: .............................................................. 3000
http.codes.201: ................................................................ 46
http.downloaded_bytes: ......................................................... 56913
http.request_rate: ............................................................. 44/sec
http.requests: ................................................................. 3046
http.response_time:
  min: ......................................................................... 1373
  max: ......................................................................... 9849
  mean: ........................................................................ 6350.1
  median: ...................................................................... 6838
  p95: ......................................................................... 9607.1
  p99: ......................................................................... 9801.2
http.response_time.2xx:
  min: ......................................................................... 1373
  max: ......................................................................... 9849
  mean: ........................................................................ 6350.1
  median: ...................................................................... 6838
  p95: ......................................................................... 9607.1
  p99: ......................................................................... 9801.2
http.responses: ................................................................ 46
vusers.created: ................................................................ 3000
vusers.created_by_name.Request driver (Non Authenticated): ..................... 3000
vusers.failed: ................................................................. 3000



```
Notas:
Apesar do ETIMEDOUT, as requsições dentro dos containers foram todas realizadas com sucesso.
O processamento dos itens em background foram encerrados cerca de 8 minutos depois.

```
redis-cli XLEN ride_estimates_stream
(integer) 419
```
Nota: Esse número foi aumentando conforme o Nginx e PHP-FPM iam processando.
Dois minutos depois o numero de items na fila chegou á um pouco mais de 1000 itens. 

### Snapshot da memoria durante o processamento
```
NAME                                                            CPU(cores)   MEMORY(bytes)   
auth-service-keycloak-0                                         20m          353Mi           
auth-service-postgresql-0                                       9m           25Mi            
ridely-cache-database-redis-master-0                            60m          17Mi            
ridely-cache-database-redis-replicas-0                          36m          15Mi            
ridely-cache-database-redis-replicas-1                          47m          15Mi            
ridely-cache-database-redis-replicas-2                          53m          15Mi            
ridely-database-b7fc6b564-srzk6                                 90m          509Mi           
ridely-service-nginx-59b544768f-m98hd                           25m          17Mi            
ridely-service-php-fpm-74d9548796-85wzp                         368m         175Mi           
ridely-service-php-fpm-74d9548796-kbxkr                         336m         188Mi           
ridely-service-process-ride-estimates-worker-65cff4fc64-dkf8m   14m          36Mi            
ridely-service-process-ride-estimates-worker-65cff4fc64-mnztb   8m           36Mi            
ridely-service-process-ride-estimates-worker-65cff4fc64-nrzrc   13m          36Mi            
ridely-service-process-ride-estimates-worker-65cff4fc64-p5vzl   15m          36Mi            
ridely-service-process-ride-estimates-worker-65cff4fc64-vwnmm   15m          36Mi            
ridely-service-process-ride-estimates-worker-65cff4fc64-x5c52   15m          36Mi
```   