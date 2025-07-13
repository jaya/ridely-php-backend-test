# 🏗️ Proposta de Arquitetura - Projeto Ridely

## 🎯 Visão Geral

A proposta visa garantir escalabilidade, segurança e separação de responsabilidades em um sistema de mobilidade urbana. A aplicação atual é monolítica em Laravel/PHP, mas com ajustes pode evoluir para um modelo baseado em serviços.

---

## 🧱 Diagrama de Alto Nível

```plaintext
[Frontend App]
     |
     v
[API Gateway / Laravel App] --> [PostgreSQL/MySQL]
                         \
                          +--> [Redis Cache]
                          +--> [Queue Worker]
                          +--> [External APIs: Maps, Payments]
```
![Diagrama de arquitetura](docs/architecture.drawio.png)

## 🧩 Domínios (Bounded Contexts)
| Contexto     | Responsabilidade                                |
|--------------|-------------------------------------------------|
| Passageiros  | Cadastro, identificação, preferências           |
| Motoristas   | Localização, disponibilidade, veículos          |
| Corridas     | Solicitação, alocação, status, cancelamento     |
| Pagamentos * | Estimativa e processamento futuro de valores    |

## 🔌 Comunicação entre serviços
- **Internamente**: RESTful JSON (Laravel Controllers + Eloquent ORM)
- **Assíncrono (futuro)**: Filas para eventos como:
  - `RideRequested`
  - `DriverAssigned`
  - `RideCancelled`

## 📈 Estratégia de Escalabilidade
- Stateless app → escalar horizontalmente com containers
- Workers separados para processar filas (ex: `ride:assign`)
- Banco particionado (por região, se necessário)
- Cache de dados frequentes (Redis)

## 🔐 Segurança
- Autenticação via JWT (fácil integração com apps mobile/web)
- Rate limiting por IP/token
- Logs de auditoria (com Monolog ou Sentry)
- Dados sensíveis criptografados em repouso

## 🔍 Observabilidade
- Logs estruturados com Monolog + JSON
- Monitoramento APM com New Relic ou Sentry
- Tracing distribuído (ex: OpenTelemetry, futuramente)

## 🚀 Deploy e Infraestrutura
- Containers com Docker
- Deploy com GitHub Actions e ECS (ou similar)
- Configuração com `.env` e `.env.production`
- Infraestrutura como código sugerida: **Terraform + AWS**
