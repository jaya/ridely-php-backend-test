# 🚖 Ridely - Teste Técnico de Backend
## 🙋‍♂️ Entrega de Anderson Romano Maia

---

## 📌 Descrição

Este documento contém as instruções completas para rodar localmente o sistema Ridely, com a feature implementada de alocação do motorista mais próximo com base em coordenadas geográficas, conforme solicitado no desafio técnico.

---

## ✅ Funcionalidade Implementada

**🚗 Alocação do motorista mais próximo com base em coordenadas:**

- Entrada: nome, e-mail do passageiro, localização de origem e destino
- Saída: corrida criada com o motorista mais próximo disponível
- Lógica baseada em fórmula Haversine
- Dados persistidos em banco de dados
- Testes automatizados cobrindo o caso de sucesso e erro

---

## 🧱 Requisitos

- PHP 8.1+
- Composer
- MySQL 8 (ou Docker)
- Laravel 10+

---

## ⚙️ Instalação Local

```bash
git clone https://github.com/seu-usuario/ridely-php-backend-test.git
cd ridely-php-backend-test

composer install
cp .env.example .env
php artisan key:generate
```
Configure seu .env com as credenciais corretas do MySQL:
```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ridely
DB_USERNAME=root
DB_PASSWORD=senha
```
Crie o banco ridely no MySQL e então rode:
```bash
php artisan migrate
php artisan serve
```
---
## 🧪 Rodar Testes Automatizados
```bash
php artisan test
```
---
## 📬 Endpoint Implementado

### POST `/api/rides/request-driver`

**Descrição:**  
Aloca automaticamente o motorista disponível mais próximo para uma corrida.


## Exemplo de Payload:

```json
{
  "passenger": {
    "name": "João da Silva",
    "email": "joao@email.com"
  },
  "pick_up": {
    "latitude": -10.909095,
    "longitude": -37.077946
  },
  "drop_off": {
    "latitude": -10.920095,
    "longitude": -37.085946
  }
}
```
## Exemplo de Resposta:

```json
{
  "id": 1,
  "status": "REQUESTED",
  "pick_up": "-10.909095,-37.077946",
  "drop_off": "-10.920095,-37.085946",
  "driver": {
    "name": "Motorista A",
    "car": {
      "license_plate": "ABC1234",
      "model": "Corolla",
      "color": "Black"
    }
  }
}
```
---

## 📄 Documentação adicional

- [`ARCHITECTURE.md`](./ARCHITECTURE.md) – Arquitetura proposta com diagramas e domínios
- [`SCALABILITY.md`](./SCALABILITY.md) – Estratégia para suportar 10.000 requisições por minuto

---

## 🙋‍♂️ Autor

**Anderson Romano Maia**  
📅 **Data de entrega:** 13/07/2025  
📧 **Email:** [andersonromanomaia@gmail.com](mailto:andersonromanomaia@gmail.com)  
🌐 **LinkedIn:** [linkedin.com/in/anderson-romano-maia](https://www.linkedin.com/in/anderson-romano-maia)

---

> Obrigado pela oportunidade! Estou à disposição para entrevistas ou aprofundar detalhes técnicos. 🚀

