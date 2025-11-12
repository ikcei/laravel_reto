DAW - Guía de Instalación y Despliegue
=====================================

Índice
------
1. Obtención del Código Fuente (Evidencia Git)
2. Construcción y Levantamiento de Contenedores
3. Instalación de Dependencias
4. Generar la Clave de Aplicación
5. Ejecutar Migraciones y Seeds
6. Configuración de Acceso Remoto
7. Comprobación
8. Commits Realizados

-------------------------------------------------

Obtención del Código Fuente (Evidencia Git)
-------------------------------------------
El primer paso es clonar el repositorio del proyecto, que contiene el código fuente de Laravel, el Dockerfile y el docker-compose.yml:

git clone https://github.com/ikcei/laravel_reto.git

Ahora deberemos abrir el proyecto con PowerShell. Para ello, navegamos al directorio laravel_reto y abrimos PowerShell en esa ruta:

cd laravel_reto

-------------------------------------------------

Construcción y Levantamiento de Contenedores
--------------------------------------------
Una vez abierta la consola de PowerShell, ejecuta el siguiente comando para levantar los contenedores:

docker compose up -d

Para comprobar que todos los contenedores se han levantado correctamente, ejecuta:

docker ps

-------------------------------------------------

Instalación de Dependencias
---------------------------
Para que la aplicación funcione correctamente, es necesario instalar las dependencias de Laravel dentro del contenedor "app":

docker compose exec app composer install

-------------------------------------------------

Generar la Clave de Aplicación
-------------------------------
Laravel requiere una clave de aplicación para funcionar correctamente. Ejecuta:

docker compose exec app php artisan key:generate

-------------------------------------------------

Ejecutar Migraciones y Seeds
----------------------------
Para inicializar la base de datos de la aplicación con migraciones y seeds, ejecuta:

docker compose exec app php artisan migrate --seed

-------------------------------------------------

Configuración de Acceso Remoto
-------------------------------
1. Modifica el archivo apache.conf y añade las siguientes líneas:

ServerName localhost
ServerAlias lingo.local

2. Abre el Bloc de notas en modo administrador y edita el archivo "hosts" en la siguiente ruta:

C:\Windows\System32\drivers\etc\hosts

Añade la siguiente línea:

127.0.0.1    lingo.local

El archivo final debería verse así:

127.0.0.1    localhost
127.0.0.1    lingo.local

-------------------------------------------------

Comprobación
------------
Abre tu navegador y escribe:

http://lingo.local

Si todo está configurado correctamente, deberías ver la aplicación funcionando.

-------------------------------------------------

Commits Realizados
------------------
Se recomienda llevar un registro de los commits realizados en el repositorio para evidenciar el progreso y las modificaciones implementadas.

-------------------------------------------------

Notas Adicionales
-----------------
- Asegúrate de tener Docker y Docker Compose instalados antes de comenzar.
- Todos los comandos "docker compose exec app ..." deben ejecutarse desde el directorio raíz del proyecto.
- Esta guía está diseñada para un entorno Windows, pero los pasos de Docker se pueden adaptar a Linux o macOS con pequeños cambios en la ruta del archivo "hosts".
