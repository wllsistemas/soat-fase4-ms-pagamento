resource "kubernetes_service_v1" "svc_nginx_pagamento" {
  metadata {
    name = "svc-nginx-pagamento"
  }

   spec {
    type = "LoadBalancer"

    selector = {
      app = "soat-nginx-pagamento"
    }

    port {
      name        = "http"
      port        = 80
      target_port = 80
      protocol    = "TCP"
    }
  }
}