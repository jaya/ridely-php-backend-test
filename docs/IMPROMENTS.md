# Melhorias Futuras

## Kubernetes
- Observabilidade
  - Implementar Sidecars par Fluentd para centralização de logs
- Escalabilidade
  - Revisar configurações do PHP e Nginx para melhor performance
    - Provavelmente separar o nginx para que o PHP-FPM possa ter mais instancias

## Ridely service
- Database
  - Doctrine
    - Implementar Doctrine ORM para abstração de banco de dados
  - Transações
    - Implementar transações em pontos críticos do serviço
- Filas
  - Implementar filas com Redis em pontos criticos
- Cache
  - Aplicar cache em endpoints que fazem muitas consultas ao banco de dados
- Segurança
  - Implementar UUID no banco de dados e API
- Testes
  - Implementar testes de integração
  - Implementar mais testes de carga
  - Implementar testes de contrato (Pact)
- Logs
  - Revisar logs, tratar erros e exceções que não devem ser expostas
  - 