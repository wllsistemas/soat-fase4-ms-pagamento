resource "kubernetes_deployment_v1" "soat_nginx_pagamento" {
  metadata {
    name = "soat-nginx-pagamento"
  }

  spec {
    selector {
      match_labels = {
        app = "soat-nginx-pagamento"
      }
    }
    template {
      metadata {
        labels = {
          app = "soat-nginx-pagamento"
        }
      }
      spec {
        container {
          name  = "soat-nginx-pagamento"
          image = "wllsistemas/php_lab_nginx:fase4-teste-pagamento-nginx2"
          port {
            container_port = 80
          }
        }
      }
    }
  }
}