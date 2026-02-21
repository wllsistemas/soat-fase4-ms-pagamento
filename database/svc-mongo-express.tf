resource "kubernetes_service_v1" "mongo_express_svc" {
  metadata {
    name      = "mongo-express"
    namespace = "default"
    labels    = { app = "mongo-express" }
  }

  spec {
    type = "ClusterIP"
    selector = { app = "mongo-express" }

    port {
      name        = "http"
      port        = 8081
      target_port = 8081
      protocol    = "TCP"
    }
  }
}