# 🏪 Sistema de Gestión para Cafetería

Este sistema web permite gestionar el inventario de productos de una cafetería, facilitando operaciones CRUD (Crear, Leer, Actualizar y Eliminar) con almacenamiento en SQL Server. El sistema incluye funcionalidades para realizar ventas y ofrecer estadísticas sobre el inventario.

## 🚀 Características Principales

- **Gestión de Productos**: Agregar, editar, eliminar y listar productos.
- **Control de Ventas**: Realizar ventas de productos y actualizar el stock automáticamente.
- **Estadísticas**: Mostrar el producto con más stock y el más vendido.
- **Manejo de Imágenes**: Subir y mostrar imágenes de productos.
- **Interfaz Amigable**: Diseño responsive para uso en dispositivos móviles y de escritorio.

## 🛠️ Tecnologías Utilizadas

- **Backend**: PHP 8.3
- **Base de datos**: SQL Server
- **Frontend**: HTML5, CSS3, JavaScript

## 📋 Requisitos Previos

Antes de instalar el sistema, asegúrate de tener los siguientes requisitos:

- PHP 8.3
- SQL Server 
- Servidor web (Apache o Nginx)
- Extensión SQL Server para PHP (`sqlsrv`)
- Controladores Microsoft ODBC para SQL Server
- GD Library para manipulación de imágenes
- Navegador web moderno


## 📥 Instalación

Para instalar y ejecutar la aplicación, sigue estos pasos:

**Configurar el Servidor**
Copia la carpeta del proyecto en la carpeta htdocs de tu instalación de XAMPP o en la raíz de tu servidor web.

**Configurar la Base de Datos**
Abre SQL Server Management Studio (o tu herramienta de gestión de bases de datos preferida).
Crea una nueva base de datos llamada cafeteria.
Importa el archivo SQL que se encuentra en database/cafeteria.sql 

**Configurar el Archivo de Configuración**
Asegúrate de que el archivo config/database.php tenga las credenciales correctas para tu base de datos. Este archivo debe contener la información necesaria para conectarse a la base de datos, como el nombre del servidor, el nombre de la base de datos, el usuario y la contraseña.

**Iniciar el Servidor**
Si usas XAMPP, inicia Apache y MySQL desde el panel de control.

**Acceder a la Aplicación**



### Resumen de la Estructura

- **Instalación**: Es la sección principal que contiene todos los pasos necesarios para que un usuario instale y configure tu aplicación.
- **Subsecciones**: Cada paso está numerado y tiene un título claro que indica qué se debe hacer (por ejemplo, "Configurar el Servidor", "Configurar la Base de Datos", etc.).
- **Instrucciones Detalladas**: Dentro de cada subsección, proporciona instrucciones claras y concisas sobre lo que el usuario debe hacer.

### ¿Por qué esta Estructura?

Esta organización ayuda a los usuarios a seguir los pasos de manera lógica y ordenada, asegurando que no se salten ningún paso importante durante la instalación y configuración de la aplicación. 

Si necesitas más aclaraciones o ajustes, ¡no dudes en preguntar!



1. **Enlace al Repositorio**
   Puedes acceder al repositorio en GitHub utilizando el siguiente enlace:
   
   [Repositorio CafeteriaPHP](https://github.com/jhonny1113/CafeteriaPHP.git)
