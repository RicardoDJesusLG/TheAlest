🏨 THE ALEST – Configuración Entorno Local

ARCHIVOS QUE SOLO SE DEBE MODIFICAR LA PRIMERA VEZ QUE SE VA A CONFIGURAR UN NUEVO ENTORNO LOCAL:

- .htaccess

LA BASE DE DATOS NO SE DEBE SUBIR AL REPOSITORIO, SE COMPRIME Y SE DESCARGA DEL HOST O SE PIDE POR PRIVADO

Guía oficial para levantar el proyecto en entorno local replicando producción.

1️⃣ Requisitos Previos

Windows 10 o superior

XAMPP instalado (Apache + MySQL)

PHP 7.4.x

Export de base de datos desde producción (.sql)

Carpeta public_html/files descargada desde producción

2️⃣ Estructura del Proyecto

Ruta esperada en local:

C:/xampp/htdocs/TheAlest/public_html


Puntos importantes:

El DocumentRoot debe apuntar a public_html

El archivo principal es index.php

El routing se maneja mediante .htaccess

Las URLs amigables dependen de mod_rewrite

3️⃣ Configuración de VirtualHost (Recomendado)
Paso 1 – Editar archivo hosts

Abrir como administrador:

C:/Windows/System32/drivers/etc/hosts


Agregar al final:

127.0.0.1 thealest.local

Paso 2 – Habilitar módulos en Apache

Editar:

C:/xampp/apache/conf/httpd.conf


Verificar que estén activos (sin #):

LoadModule rewrite_module modules/mod_rewrite.so
Include conf/extra/httpd-vhosts.conf

Paso 3 – Configurar VirtualHost

Editar:

C:/xampp/apache/conf/extra/httpd-vhosts.conf


Agregar:

<VirtualHost *:80>
    ServerName thealest.local
    DocumentRoot "C:/xampp/htdocs/TheAlest/public_html"

    <Directory "C:/xampp/htdocs/TheAlest/public_html">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>


Reiniciar Apache.

4️⃣ Configuración de config.php

Ubicación:

public_html/cms/inc/config.php


Debe quedar:

$config['site_root'] = '';


Además:

Configurar credenciales locales de base de datos

⚠️ Nunca subir config.php al repositorio

Agregar al .gitignore:

public_html/cms/inc/config.php

5️⃣ Base de Datos

Crear base de datos local (ejemplo: web8755)

Importar el dump SQL:

Vía phpMyAdmin

O vía CLI:

mysql -u root -p web8755 < dump.sql <---dump es el nombre de tu archivo.sql que contiene la base de datos.


Verificar tablas importantes (ej: alest_slider)

6️⃣ Carpeta files

Copiar desde producción:

public_html/files


Colocarla exactamente en:

C:/xampp/htdocs/TheAlest/public_html/files


⚠️ Sin esta carpeta, el frontend cargará sin imágenes.

7️⃣ Verificación Final

Abrir en navegador:

http://thealest.local/


Probar:

/the-hotel

/stay

/es/

Abrir DevTools → Network y verificar que no existan errores 404.

8️⃣ Flujo de Trabajo en Equipo

No subir al repositorio:
public_html/cms/inc/config.php
*.sql


Buenas prácticas:

Compartir dump SQL de forma privada

Usar VirtualHost para mantener entorno igual a producción

Trabajar en ramas (feature/*, fix/*)

Nunca modificar .htaccess para ajustes locales 
