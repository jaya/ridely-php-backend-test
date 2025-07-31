# Ridely - Plataforma de Corridas por Táxi (PHP Backend Test - Architecture Challenge)

**Ridely** é uma plataforma de transporte urbano voltada para a cidade de Aracaju. A solução oferece um ecossistema completo e escalável para corridas de táxi, construído com foco em desempenho, segurança, observabilidade e extensibilidade.

Esta monorepo abrange **backend, infraestrutura como código, documentação, automação, testes e orquestração**, proporcionando um ambiente produtivo para desenvolvimento local, homologação e produção.

## Índice

- [Visão Geral da Solução](#visão-geral-da-solução)
- [Estrutura do Projeto](#estrutura-do-projeto)
- [Arquitetura do Sistema](#arquitetura-do-sistema)
- [Serviços Principais](#serviços-principais)
- [Como Rodar Localmente](#como-rodar-localmente)
- [Como Fazer Deploy](#como-fazer-deploy)



## Visão Geral da Solução

A arquitetura do Ridely foi pensada para garantir **alta coesão e baixa acoplagem**, baseada em microsserviços e padrões modernos de deployment e segurança:

| **Camada**          | **Tecnologias / Estratégias**                                                             |
|---------------------|-------------------------------------------------------------------------------------------|
| **Backend**         | PHP (Laravel) e Node.js (Express) estruturados em microserviços                           |
| **Proxy**           | NGINX como reverse proxy local, com suporte a múltiplos domínios e roteamento de serviços |
| **Orquestração**    | Kubernetes com Helm Charts para deploy de serviços e bancos de dados                      |
| **API Gateway**     | Kong configurado para roteamento, autenticação, rate limiting e integração com Keycloak   |
| **Autenticação**    | Keycloak (OAuth2/OpenID Connect) como provedor de identidade                              |
| **Mensageria**      | RabbitMQ para comunicação assíncrona entre serviços                                       |
| **Infraestrutura**  | Provisionamento com Terraform e automações via Ansible                                    |
| **Testes**          | Suporte completo a testes E2E, integração, carga, performance, regressão e segurança      |
| **Observabilidade** | Grafana, Prometheus, Loki, Jaeger e OpenTelemetry para métricas, logs e tracing           |
| **Documentação**    | Diagramas de arquitetura, queries úteis e coleções do Postman incluídas no repositório    |

---
## Estrutura do Projeto
Abaixo, uma visão geral dos diretórios:

```
root/
├── backend/                 # Serviços de backend e seus respectivos charts Helm
│   ├── charts/              # Charts Helm para os serviços
│   └── services/            # Código para os serviços
│
├── databases/               # Charts Helm para bancos de dados e caches
│   └── charts/
│
├── docs/                    # Documentação técnica e operacional
│   ├── architecture/        # Diagramas e documentos arquiteturais
│   │   └── diagrams/
│   ├── collections/         # Collections do Postman para testes de API
│   ├── useful-queries/      # Scripts SQL úteis para debugging ou manutenção
│   ├── ARCHITECTURE.md      # Documento detalhado da arquitetura
│   └── SCALABILITY.md       # Estratégias de escalabilidade
│
├── frontend/                # Placeholder para os frontends (web/mobile)
│   ├── apps/                # Aplicações frontend (futuramente)
│   └── charts/              # Charts Helm correspondentes
│
├── infrastructure/          # Provisionamento de infraestrutura com Terraform e Ansible
│   ├── ansible/             # Arquivos de configuração do Ansible
│   ├── terraform/           # Arquivos de configuração do Terraform
│   │   ├── environments/    # Configurações de ambiente
│   │   └── modules/         # Módulos reutilizáveis (ex: EKS, VPC, etc)
│
├── observability/           # Monitoramento, logs, tracing e dashboards
│   ├── charts/              # Helm charts para Prometheus, Grafana, Loki e Jaeger
│   │   ├── prometheus/
│   │   ├── grafana/
│   │   ├── loki/
│   │   └── jaeger/
│   ├── instrumentation/     # Configurações e SDKs OpenTelemetry para Laravel e Node.js
│   │   ├── laravel/
│   │   └── nodejs/
│   ├── dashboards/          # Dashboards Grafana exportados (JSON)
│   └── alerts/              # Regras de alertas para Prometheus e Grafana
│
├── scripts/                 # Scripts utilitários e automações
│   ├── docker/              # Scripts relacionados ao Docker
│   ├── helm/                # Scripts relacionados ao Helm
│   ├── kind/                # Scripts relacionados ao KIND
│   ├── kubectl/             # Scripts relacionados ao kubectl
│   ├── nginx/               # Scripts relacionados ao NGINX (se usado)
│   ├── plantuml/            # Scripts relacionados ao PlantUML
│   └── run-local.sh         # Script principal para rodar o projeto localmente
│
├── tests/                   # Testes automatizados de várias naturezas
│   ├── e2e/                 # Testes ponta a ponta
│   ├── integration-tests/   # Testes de integração
│   ├── load-tests/          # Testes de carga
│   ├── performance-tests/   # Testes de performance
│   ├── regression-tests/    # Testes de regressão
│   └── security-tests/      # Testes de segurança
│
├── CHALLENGE.md             # Descrição técnica ou desafio de proposta do projeto
├── README.md                # Este documento
└── TODO.md                  # Tarefas pendentes e planejamento

```

## Arquitetura do Sistema
A arquitetura da plataforma **Ridely** adota uma abordagem baseada em **microserviços** e **infraestrutura distribuída**, com foco em modularidade, escalabilidade e observabilidade.
O sistema Ridely utiliza aplicações móveis e painel administrativo, com microserviços principais para gestão de corridas, autenticação e cálculo de tarifas dinâmicas.

A arquitetura adota:

* **API Gateway Kong** para roteamento e controle de acesso;
* Microsserviços em **Laravel** e **Node.js** com persistência em bancos MySQL e PostgreSQL, além de cache em Redis;
* Comunicação assíncrona via **RabbitMQ**;
* Autenticação baseada em **Keycloak** (OAuth2/OpenID Connect);
* Observabilidade completa com **Prometheus**, **Grafana**, **Loki** e **Jaeger** para métricas, logs e tracing distribuído.

Essa solução garante alta disponibilidade, escalabilidade e monitoramento eficaz.


### Diagrama de contexto
![ridely-context.png](docs/architecture/diagrams/c4/ridely-context.png)

> 🔍 Para detalhes visuais e documentações técnicas aprofundadas, consulte:
>
> * [`docs/ARCHITECTURE.md`](docs/ARCHITECTURE.md)
> * [`docs/SCALABILITY.md`](docs/SCALABILITY.md)


### Arquitetura Kubernetes?

## Serviços Principais

* **Ridely Service**
  Serviço central que gerencia a lógica das corridas, incluindo solicitações, status e histórico. Desenvolvido em Laravel, interage com banco de dados MySQL e cache Redis.

* **Auth Service**
  Responsável pela autenticação e autorização dos usuários, utilizando Keycloak para emissão e validação de tokens via OAuth2/OpenID Connect. Persiste dados em banco PostgreSQL.

* **Pricing Service**
  Calcula tarifas dinâmicas das corridas com base em variáveis contextuais. Implementado em Node.js, utiliza Redis para cache e se comunica via RabbitMQ para eventos relacionados a preços.

Claro! Aqui está um exemplo genérico e bem estruturado para as seções **"Como Rodar Localmente"** e **"Como Fazer Deploy"** em Markdown, que você pode adaptar facilmente ao seu projeto:

---

### Como Rodar Localmente

Para rodar o projeto localmente, siga os passos abaixo. Certifique-se de ter os requisitos mínimos instalados, como `Docker`, `Docker Compose`, `Node.js`, `PHP`, `Composer`, entre outros, conforme necessário.

--- Em progresso ---
Abaixo apenas um template

```bash
# Clone o repositório
git clone https://github.com/sua-org/ridely.git
cd ridely

# Copie os arquivos de ambiente
cp .env.example .env

# Suba os serviços com Docker Compose
docker-compose up --build
```

Depois de iniciado:

* Backend estará disponível em: `http://localhost:8000`
* Frontend (caso exista): `http://localhost:3000`
* Keycloak: `http://localhost:8080`
* RabbitMQ: `http://localhost:15672` (usuário: `guest`, senha: `guest`)

> Alguns serviços podem exigir seeders, migrations ou configurações adicionais. Consulte a documentação do serviço correspondente em `/backend/services`.

---

### Como Fazer Deploy

Este projeto pode ser implantado em ambientes **ECS** ou **EKS**, utilizando **Terraform**, **Helm** e **Ansible**. Abaixo, um fluxo genérico de implantação para ambientes Kubernetes:

--- Em progresso ---
Abaixo apenas um template

#### 1. Provisionar infraestrutura (ex: com Terraform)

```bash
cd infrastructure/terraform/aws
terraform init
terraform plan
terraform apply
```

#### 2. Aplicar configurações com Ansible (opcional)

```bash
cd infrastructure/ansible
ansible-playbook -i inventories/prod main.yml
```

#### 3. Fazer deploy com Helm

```bash
cd backend/charts
helm upgrade --install ridely-service ./ridely-service \
  --namespace ridely \
  --create-namespace \
  -f values.prod.yaml
```

#### 4. Acompanhar status

```bash
kubectl get pods -n ridely
kubectl logs -f deploy/ridely-service -n ridely
```

> 💡 Dica: para testar APIs localmente mesmo após o deploy, use `kubectl port-forward` ou configure um `Ingress`.

