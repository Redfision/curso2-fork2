from django.contrib import admin
from django.urls import include, path
from rest_framework.authtoken.views import obtain_auth_token
from rest_framework.routers import DefaultRouter

from avisos.views import AvisoViewSet, yo

router = DefaultRouter()
router.register(r"avisos", AvisoViewSet)

urlpatterns = [
    path("admin/", admin.site.urls),
    path("api/", include(router.urls)),
    path("api-auth/", include("rest_framework.urls")),
    path("api/token", obtain_auth_token),
    path("api/yo", yo),
]
