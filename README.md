# Prueba técnica [bodas.net](https://www.bodas.net/)

## Ejecución

### Requisitos

* PHP 7.4 o superior
* [Composer](https://getcomposer.org/download/)
* Web server (o symfony standalone server)

### Instalación
   [symfony setup](https://symfony.com/doc/current/setup.html)

### Presupuestos del problema

El problema viene con algunos parametros sin especificar que me he visto obligado a definir yo mismo, intentado simplificarlo debido a límite temporal de la prueba.
Estros presupuestos son:
* La velocidad de los ascensores es de un minuto por planta
* Todas las llamadas se encolan en el mismo momento
* La búsqueda de ascensores no tiene ningún criterio especial, se buscan según se han cargado en memoria.