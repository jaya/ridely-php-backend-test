# 🧠 Desafio de System Design - Projeto Ridely

## 🌟 Objetivo do Desafio

Este desafio técnico tem como base o **Ridely**, uma plataforma de transporte por táxi com foco na mobilidade urbana de Aracaju. Nosso objetivo é avaliar sua **capacidade como Solutions Architect ou Software Architect**, tanto na **proposição de uma arquitetura escalável**, **segura** e **resiliente**, quanto na **implementação técnica de uma funcionalidade core da aplicação**.

Você irá:

- Analisar a arquitetura atual e identificar gargalos
- Propor uma nova estrutura baseada em boas práticas de system design
- Codificar **pelo menos uma feature** que esteja alinhada com a nova arquitetura proposta
- Documentar tecnicamente suas decisões e estrutura

---

## 🧩 Escopo do Desafio

Você será avaliado com base em três entregáveis:

### 1. ✍️ **Feature Obrigatória Implementada**

Você deve implementar **uma feature funcional** do sistema de corrida de táxi. Sugerimos uma das opções abaixo (escolha uma):

- 📍 **Cálculo de tempo e distância via API externa (Google Maps Directions API ou similar)**
- 🚗 **Alocação do motorista mais próximo com base em coordenadas**
- 💰 **Cálculo do preço estimado da corrida**
- ❌ **Implementação da política de cancelamento de corrida**

Sua feature deve conter:
- Testes automatizados (unitários e/ou integrados)
- Logging adequado
- Tratamento de erros e validações
- Autenticação aplicada (se necessário)
- Dados persistidos corretamente (corrida, passageiro, motorista, status, etc.)

---

### 2. 🏗️ **Proposta de Arquitetura (System Design)**

Crie um documento chamado `ARCHITECTURE.md` no repositório com a **descrição da nova arquitetura sugerida**. Este documento deve conter:

- Diagrama de alto nível (pode ser imagem ou link para ferramenta externa)
- Separação por domínios (bounded contexts, DDD se aplicável)
- Estratégia de comunicação entre serviços (REST, Events, Message Queues)
- Estratégia de escalabilidade (horizontal, filas, workers, etc.)
- Estratégia de autenticação e segurança (JWT, OAuth, etc.)
- Ferramentas de observabilidade e rastreamento sugeridas
- Considerações sobre deploy, containers e boas práticas

> Não é necessário usar Terraform, mas explique como aplicaria infraestrutura como código se fosse necessário.

---

### 3. 🚀 **Plano de Escalabilidade**

Crie um arquivo `SCALABILITY.md` contendo suas ideias para escalar o sistema Ridely para **10.000 requisições por minuto**. O conteúdo deve incluir:

- Estratégia de escalabilidade da API
- Otimizações no banco de dados (índices, particionamento, etc.)
- Uso de cache (ex: Redis para estimativas ou rotas frequentes)
- Processamento concorrente e filas (RabbitMQ, Kafka, etc.)
- Sugestão de indexação geoespacial (ex: PostGIS, Redis GEO, MongoDB)

---

## 📄 Documentação Técnica

Inclua na raiz do projeto um diretório `/docs/` com qualquer documentação adicional que você julgar importante:

- README com instruções de instalação e execução
- Diagrama(s) de sequência ou fluxo de dados (opcional)
- Postmortem de decisões arquiteturais (opcional)
- Justificativas técnicas para bibliotecas/frameworks escolhidos

---

## 📦 Requisitos Técnicos

- A linguagem original do projeto é **PHP**, mas você pode propor a adoção de microserviços com outras linguagens (Node.js, Go, etc.) caso julgue relevante.
- O repositório deve conter código organizado, versionado e funcional.
- É obrigatório o uso de versionamento via Git e envio da proposta via **Pull Request**.

---

## 🔐 Considerações de Segurança

Sua solução deve considerar:

- Proteção contra injeções, CSRF e XSS
- Autenticação baseada em token (JWT, OAuth, API Key)
- Rate limiting e proteção contra brute-force
- Logging seguro (evite registrar dados sensíveis)
- Criptografia em trânsito (HTTPS) e em repouso (se aplicável)

---

## 📆 Entrega

1. Faça um **fork do repositório oficial** que será fornecido.
2. Crie uma **branch com seu nome** no padrão: `nome_sobrenome`.
3. Submeta um **Pull Request para a branch `main`** com:
   - Título: `Entrega - nome_sobrenome`
   - Corpo do PR contendo:
     - Nome completo
     - Data da entrega
     - Observações (se desejar)
4. Certifique-se de que o repositório contenha:
   - A feature obrigatória funcional
   - `ARCHITECTURE.md`
   - `SCALABILITY.md`
   - Testes
   - Documentação técnica relevante

---

## ⚖️ Critérios de Avaliação

| Critério                         | Peso |
|----------------------------------|------|
| Qualidade e clareza do código    | 20%  |
| Arquitetura proposta             | 20%  |
| Escalabilidade e segurança       | 20%  |
| Testes e cobertura               | 15%  |
| Documentação técnica             | 15%  |
| Organização do repositório       | 10%  |

---

## 🏁 Dica Final

Pense como um arquiteto. Faça escolhas orientadas ao problema, pensando na evolução do produto no longo prazo. Mostre sua capacidade de comunicar decisões técnicas com clareza.

Boa sorte! 🚖🚦
