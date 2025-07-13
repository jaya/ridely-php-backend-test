# 🚖 Ridely - Backend Technical Challenge
## 🙋‍♂️ Submission by Anderson Romano Maia

---

## 📌 Description

This document contains all necessary instructions to run the Ridely project locally, along with the implementation of the core feature: assigning the nearest available driver based on geographical coordinates.

---

## ✅ Implemented Feature

**🚗 Nearest Driver Allocation Based on Coordinates:**

- Input: passenger's name, email, pickup and drop-off coordinates
- Output: a new ride assigned to the closest available driver
- Distance calculated using the Haversine formula
- Data persisted in the database
- Automated tests covering success and error scenarios

---

## 🧱 Requirements

- PHP 8.1+
- Composer
- MySQL 8 (or Docker)
- Laravel 10+

---

## ⚙️ Local Installation

```bash
git clone https://github.com/your-username/ridely-php-backend-test.git
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
Update your .env file with your database credentials:
```bash
php artisan migrate
php artisan serve
```
---
## 🧪 Running Tests
```bash
php artisan test
```
---
## 📬 Implemented Endpoint
### POST `/api/rides/request-driver`

**Description:**  
Automatically assigns the nearest available driver to a new ride request.

**Example Payload:**
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
**Example Response:**
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

## 📄 Additional Documentation

- [`ARCHITECTURE_EN.md`](./ARCHITECTURE_EN.md) – Proposed system architecture, bounded contexts, and tech decisions
- [`SCALABILITY_EN.md`](./SCALABILITY_EN.md) – Strategy to support 10,000 requests per minute

---

## 🙋‍♂️ Author

**Anderson Romano Maia**  
📅 **Submission Date:** July 13, 2025 
📧 **Email:** [andersonromanomaia@gmail.com](mailto:andersonromanomaia@gmail.com)  
🌐 **LinkedIn:** [linkedin.com/in/anderson-romano-maia](https://www.linkedin.com/in/anderson-romano-maia)

---

> Thank you for the opportunity! I’m available for interviews or to discuss any technical details. 🚀

