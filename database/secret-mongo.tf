resource "kubernetes_secret_v1" "secret_mongo_pagamento" {
  metadata {
    name = "soat-secret-mongo-pagamento"
  }

  type = "Opaque"
  data = {
    MONGO_INITDB_ROOT_USERNAME = "root"
    MONGO_INITDB_ROOT_PASSWORD = "rootpass123"
  }
}