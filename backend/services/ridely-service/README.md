# Ridely Service

## Tests
http://127.0.0.1:8000/coverage/unit/index.html

Indicar extensões necessarias
- Redis
- Xml
- MySQL
- XDebug
- Curl


## Atualizar documentação da API

Execute o seguinte comando:

```bash
    ./scripts/init.sh
```
> Nota: este comando dever ser executado na pasta do serviço.
 

## Executar o serviço
```bash
    php artisan serve
```

## Executar o worker para processar as filas
```bash
    php artisan queue:process-ride-estimates
```