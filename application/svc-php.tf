resource "kubernetes_service_v1" "svc_php_pagamento" {
  metadata {
    name = "svc-php-pagamento" # ATENÇÃO AO NOME DO SERVICE PARA SER O MESMO NAME DO UPSTREAM NA IMAGEM DO NGINX 
  }

  spec {
    type = "ClusterIP"
    selector = {
      app = "soat-php-pagamento"
    }
    port {
      port        = 9000 # Porta que o Service expõe internamente
      target_port = 9000 # Porta na qual o container PHP-FPM está escutando
    }
  }
}