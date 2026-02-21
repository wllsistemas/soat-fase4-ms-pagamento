resource "kubernetes_config_map_v1" "php_app_config_pagamento" {
  metadata {
    name = "tf-pagamento-configmap"
  }

  data = {
    APP_NAME    = "soat-pagamento"
    APP_VERSION = "1.0.0"
    APP_ENV     = "production"
  }
}