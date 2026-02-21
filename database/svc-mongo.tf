resource "kubernetes_service_v1" "svc_mongo_pagamento" {
  metadata {
    name      = "svc-mongo-pagamento"
  }

  spec {
    type = "ClusterIP"
    selector = { app = "soat-mongo-pagamento" }

    port {
      name        = "mongodb"
      port        = 27017
      target_port = 27017
      protocol    = "TCP"
    }
  }
}