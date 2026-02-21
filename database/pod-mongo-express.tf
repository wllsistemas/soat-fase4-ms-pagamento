resource "kubernetes_deployment_v1" "mongo_express" {
  metadata {
    name      = "mongo-express"
    namespace = "default"
    labels    = { app = "mongo-express" }
  }

  spec {
    replicas = 1

    selector {
      match_labels = { app = "mongo-express" }
    }

    template {
      metadata {
        labels = { app = "mongo-express" }
      }

      spec {
        container {
          name  = "mongo-express"
          image = "mongo-express:1.0.2-20"

          port {
            name           = "http"
            container_port = 8081
          }

          env_from {
            secret_ref {
              name = kubernetes_secret_v1.mongo_express_secret.metadata[0].name
            }
          }
        }
      }
    }
  }
}