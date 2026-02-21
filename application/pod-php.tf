resource "kubernetes_deployment_v1" "soat_php_pagamento" {
  metadata {
    name = "soat-php-pagamento"
  }

  spec {
    selector {
      match_labels = {
        app = "soat-php-pagamento"
      }
    }
    template {
      metadata {
        labels = {
          app = "soat-php-pagamento"
        }
      }
      spec {
        container {
          name  = "soat-php-pagamento"
          image = "wllsistemas/php_lab_fase4:pagamento-v1"
          port {
            container_port = 9000
          }
          env {
            name = "ENV_APP_ENV"
            value_from {
              config_map_key_ref {
                name = "tf-pagamento-configmap"
                key  = "APP_ENV"
              }
            }
          }
          env {
            name = "ENV_APP_NAME"
            value_from {
              config_map_key_ref {
                name = "tf-pagamento-configmap"
                key  = "APP_NAME"
              }
            }
          }
          env {
            name = "ENV_APP_VERSION"
            value_from {
              config_map_key_ref {
                name = "tf-pagamento-configmap"
                key  = "APP_VERSION"
              }
            }
          }
        
          # Datadog APM (dd-trace-php)
          env {
            name = "DD_AGENT_HOST"
            value_from {
              field_ref {
                field_path = "status.hostIP"
              }
            }
          }

          env {
            name  = "DD_TRACE_AGENT_PORT"
            value = "8126"
          }

          env {
            name  = "DD_TRACE_ENABLED"
            value = "true"
          }

          env {
            name  = "DD_SERVICE"
            value = "soat-php-pagamento"
          }

          env {
            name = "DD_ENV"
            value_from {
              config_map_key_ref {
                name = "tf-pagamento-configmap"
                key  = "APP_ENV"
              }
            }
          }

          env {
            name = "DD_VERSION"
            value_from {
              config_map_key_ref {
                name = "tf-pagamento-configmap"
                key  = "APP_VERSION"
              }
            }
          }

          env {
            name  = "DD_LOGS_INJECTION"
            value = "true"
          }

          env {
            name  = "DD_RUNTIME_METRICS_ENABLED"
            value = "true"
          }

          env {
            name  = "DD_TRACE_DEBUG"
            value = "true"
          }

          env {
            name  = "DD_TRACE_LOG_LEVEL"
            value = "debug"
          }

          env {
            name  = "DD_TRACE_LOG_FILE"
            value = "/dev/stderr"
          }

        }
      }
    }
  }
}