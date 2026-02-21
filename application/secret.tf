resource "kubernetes_secret_v1" "soat_pagamento_secret" {
  metadata {
    name = "soat-pagamento-secret"
  }
  type = "Opaque"
  data = {
    DB_USERNAME = "postgres" 
    DB_PASSWORD = "postgres"     
    DB_NAME = "postgres"  
    DB_PORT = "5432"  
    DB_HOST = "svc-postgres-pagamento"  
  }
}