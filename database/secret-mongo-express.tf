resource "kubernetes_secret_v1" "mongo_express_secret" {
  metadata {
    name      = "mongo-express-secret"
    namespace = "default"
  }

  type = "Opaque"

  data = {
    ME_CONFIG_MONGODB_ADMINUSERNAME = "root"
    ME_CONFIG_MONGODB_ADMINPASSWORD = "rootpass123"
    ME_CONFIG_MONGODB_URL           = "mongodb://root:rootpass123@svc-mongo-pagamento:27017/admin"
    ME_CONFIG_BASICAUTH_USERNAME    = "admin"
    ME_CONFIG_BASICAUTH_PASSWORD    = "admin123"
  }
}