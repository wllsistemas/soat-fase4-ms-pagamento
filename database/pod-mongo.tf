resource "kubernetes_stateful_set_v1" "soat_mongo_pagamento" {
  metadata {
    name      = "soat-mongo-pagamento"
  }

  spec {
    service_name = kubernetes_service_v1.svc_mongo_pagamento.metadata[0].name
    replicas     = 1

    selector {
      match_labels = { app = "soat-mongo-pagamento" }
    }

    template {
      metadata {
        labels = { app = "soat-mongo-pagamento" }
      }

      spec {
        container {
          name  = "mongo"
          image = "mongo:7"

          port {
            name           = "mongodb"
            container_port = 27017
          }

          env_from {
            secret_ref {
              name = kubernetes_secret_v1.secret_mongo_pagamento.metadata[0].name
            }
          }

          volume_mount {
            name       = "mongo-data"
            mount_path = "/data/db"
          }

          readiness_probe {
            tcp_socket {
              port = 27017
            }
            initial_delay_seconds = 10
            period_seconds        = 10
          }

          liveness_probe {
            tcp_socket {
              port = 27017
            }
            initial_delay_seconds = 30
            period_seconds        = 10
          }
        }
      }
    }

    volume_claim_template {
      metadata {
        name = "mongo-data"
      }

      spec {
        access_modes = ["ReadWriteOnce"]
        storage_class_name = "gp3"

        resources {
          requests = {
            storage = "5Gi"
          }
        }
      }
    }
  }
}