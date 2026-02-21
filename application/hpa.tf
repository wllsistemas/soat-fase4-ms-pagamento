resource "kubernetes_horizontal_pod_autoscaler_v2" "hpa_nginx_pagamento" {
  metadata {
    name = "hpa-nginx-pagamento" 
  }

  spec {
    scale_target_ref {
      api_version = "apps/v1"
      kind        = "Deployment"
      name        = "soat-nginx-pagamento"
    }
    min_replicas = 1
    max_replicas = 10

    metric {
      type = "Resource"
      resource {
        name = "cpu"
        target {
          type               = "Utilization"
          average_utilization = 15
        }
      }
    }

    metric {
      type = "Resource"
      resource {
        name = "memory"
        target {
          type          = "AverageValue"
          average_value = "15Mi" 
        }
      }
    }
  }
}