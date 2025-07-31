| Tópico/Fila             | Origem           | Destino(s)                                             | Uso principal                       |
|-------------------------|------------------|--------------------------------------------------------|-------------------------------------|
| `ride.requested`        | `ridely-service` | `pricing-service`                                      | Calcular estimativa de preço        |
| `ride.accepted`         | `ridely-service` | (opcionalmente outros, ex: analytics)                  | Acompanhar corrida                  |
| `driver.status.changed` | `ridely-service` | (opcionalmente `pricing-service`, admin-service, etc.) | Atualizar mapa de motoristas ativos |
