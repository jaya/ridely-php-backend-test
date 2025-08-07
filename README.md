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

| **Camada**              | **Tecnologias / Estratégias**                                                               |
|-------------------------|---------------------------------------------------------------------------------------------|
| **Backend**             | PHP (Laravel) estruturado em um microserviços                                               |
| **Proxy**               | NGINX como reverse proxy local, com suporte a múltiplos domínios e roteamento de serviços   |
| **Orquestração**        | Kubernetes com Helm Charts para deploy de serviços e bancos de dados                        |
| ~~**API Gateway**~~     | ~~Kong configurado para roteamento, autenticação, rate limiting e integração com Keycloak~~ |
| **Autenticação**        | Keycloak (OAuth2/OpenID Connect) como provedor de identidade                                |
| **Mensageria**          | ~~RabbitMQ~~ Redis para comunicação assíncrona entre serviços                               |
| ~~**Infraestrutura**~~  | ~~Provisionamento com Terraform e automações via Ansible~~                                  |
| **Testes**              | Suporte completo a testes E2E, integração, carga, performance, regressão e segurança        |
| ~~**Observabilidade**~~ | ~~Grafana, Prometheus, Loki, Jaeger e OpenTelemetry para métricas, logs e tracing~~         |
| **Documentação**        | Diagramas de arquitetura, queries úteis e coleções do Postman incluídas no repositório      |

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
│   └── skaffold/            # Scripts relacionados ao Skaffold
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

* ~~**API Gateway Kong** para roteamento e controle de acesso;~~
* Microsserviço em **Laravel** com persistência em bancos MySQL, além de cache em Redis;
* ~~Sidecars para coleta de logs com fluentd~~;
* Comunicação assíncrona via Redis ~~**RabbitMQ**~~;
* Autenticação baseada em **Keycloak** (OAuth2/OpenID Connect);
* ~~Observabilidade completa com **Prometheus**, **Grafana**, **Loki** e **Jaeger** para métricas, logs e tracing distribuído.~~

Essa solução garante alta disponibilidade, escalabilidade e monitoramento eficaz.


### Diagrama de contexto
![ridely-context.png](docs/architecture/diagrams/c4/ridely-context.png)

> 🔍 Para detalhes visuais e documentações técnicas aprofundadas, consulte:
>
> * [`docs/ARCHITECTURE.md`](docs/ARCHITECTURE.md)
> * [`docs/SCALABILITY.md`](docs/SCALABILITY.md)


## Arquitetura Local (Kubernetes)
A arquitetura local do Ridely é baseada em Kubernetes, utilizando **Helm** para gerenciar os serviços e bancos de dados. Abaixo está um diagrama simplificado da arquitetura local:

![ridely-kubernetes-component.png](docs/architecture/diagrams/uml/ridely-kubernetes-component.png)

## Serviços Principais

- **Ridely Service**
  - Serviço central que gerencia a lógica das corridas, incluindo solicitações, status e histórico. 
  - Calcula tarifas dinâmicas das corridas com base em variáveis contextuais. 
  - Desenvolvido em Laravel, interage com banco de dados MySQL e cache Redis.
  - Utiliza Redis para filas e eventos relacionados a preços.

> Para mais detalhes ver a documentação do serviço: [README.md](README.md).


- **Auth Service**
  Responsável pela autenticação e autorização dos usuários, utilizando Keycloak para emissão e validação de tokens via OAuth2/OpenID Connect. Persiste dados em banco PostgreSQL.

- **Location Service (Nominatim)**  
  Serviço externo responsável por fornecer informações de localização e geolocalização. Utilizado para realizar encontrar o endereço de origem e destino das corridas.
  - URL: https://nominatim.openstreetmap.org/
  - Limite de requisições: 1 requisição por segundo.
  - Estrategia de cache: Utiliza cache local para evitar múltiplas requisições ao serviço externo, isso se aplica a requsições de dados de localização repetidos.
  > Nota: A nível de desenvolvimento usamos a versão gratuira e pública e a estratégia de cache para prevenir que várias chamadas repetidas sejam feitas. 
  Em um teste foi utilizado um helm chart com o serviço mais o arquivo de endereços para a região do nordeste (https://download.geofabrik.de/south-america/brazil.html),
  Porém a inicialização do serviço toma muito tempo para ficar pronto para uso, o que limitou inicialmente a sua aplicação.   
   
  
   


## Dependencies do projeto

O projeto utiliza as seguintes dependências:
 - Artillery
 - Docker
 - Kubernetes
 - Helm
 - Kind
 - Skaffold
 - PlantUML

### Instalação das dependencies

#### Kind
Você pode instalar o Kind executando o seguinte comando:
```bash
  ./scripts/kind/kind-install.sh 
```
> Nota: Você deve executar este comando na raiz do projeto.

#### Skaffold
Você pode instalar o Skaffold executando o seguinte comando:
```bash
  ./scripts/skaffold/skaffold-install.sh 
```
> Nota: Você deve executar este comando na raiz do projeto.

#### PlantUML
Você pode instalar o PlantUML executando o seguinte comando:
```bash
  ./scripts/plantuml/plantuml-install.sh 
```
> Nota: Você deve executar este comando na raiz do projeto.

#### Outros
Recomenda-se realizar a instalação de forma padrão, através do gerenciador de pacotes do sistema operacional.


## Preparando o ambiente

### Criando o cluster
Execute o comando a seguir na raiz do projeto:
```bash
  ./scripts/kind/kind-create-cluster.sh
```
> Nota: Você deve executar este comando na raiz do projeto.

### Configurando o contexto
Execute o comando a seguir na raiz do projeto:
```bash
  ./scripts/kubectl/kubectl-config-context.sh
```
> Nota: Você deve executar este comando na raiz do projeto.
 
### Criando o namespace
Execute o comando a seguir na raiz do projeto:
```bash
  ./scripts/kubectl/kubectl-create-namespace.sh 
```
> Nota: Você deve executar este comando na raiz do projeto.

## Como Rodar Localmente

### Aplicação completa
Execute o comando a seguir na raiz do projeto:
```bash
  ENVIRONMENT_TYPE=dev skaffold dev --no-prune --namespace=ridely
```
> Nota: Você deve executar este comando na raiz do projeto.

### Apenas databases
Execute o comando a seguir na raiz do projeto:
```bash
  ENVIRONMENT_TYPE=dev skaffold dev --no-prune -p databases-only --namespace=ridely
```
> Nota: Você deve executar este comando na raiz do projeto.

### Apenas a autenticação
Execute o comando a seguir na raiz do projeto:
```bash
  ENVIRONMENT_TYPE=dev skaffold dev --no-prune -p auth-service-only --namespace=ridely
```
> Nota: Você deve executar este comando na raiz do projeto.

### Apenas a aplicação PHP + Banco de Dados
Execute o comando a seguir na raiz do projeto:
```bash
  ENVIRONMENT_TYPE=dev skaffold dev --no-prune -p ridely-service-only --namespace=ridely
```
> Nota: Você deve executar este comando na raiz do projeto.


## Desenvolvimento

Se você precisa desenvolver localmente, siga as instruções abaixo para configurar o ambiente de desenvolvimento conforme a sua necessidade.

### Ativar service de autenticação
Execute o comando a seguir na raiz do projeto:
```bash
  ./scripts/helm/helm-install-charts.sh auth-service
```
> Nota: Você deve executar este comando na raiz do projeto.

### Ativar banco de dados
Execute o comando a seguir na raiz do projeto:
```bash
  ./scripts/helm/helm-install-charts.sh ridely-database
```
> Nota: Você deve executar este comando na raiz do projeto.

### Ativar redis
Execute o comando a seguir na raiz do projeto:
```bash
  ./scripts/helm/helm-install-charts.sh ridely-cache-database
```
> Nota: Você deve executar este comando na raiz do projeto.

### Ativar aplicação PHP
Execute o comando a seguir na raiz do projeto:
```bash
  ./scripts/helm/helm-install-charts.sh ridely-service
```
> Nota: Você deve executar este comando na raiz do projeto.

### Habilitar a exposição dos serviços
Execute o comando a seguir na raiz do projeto:
```bash
  ./scripts/kubectl/kubectl-port-forward.sh 
```
> Nota: Você deve executar este comando na raiz do projeto.

## Documentação


## Gerar diagramas com imagens

Após instalar o PlantUML, utilize o script abaixo para gerar imagens PNG dos diagramas `.puml` definidos no projeto.  
As imagens serão geradas automaticamente na pasta correspondente.

```
./scripts/plantuml/plantuml-create-diagrams.sh
```
> Nota: Você deve executar este comando na raiz do projeto.
 
