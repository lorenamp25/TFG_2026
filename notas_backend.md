# index.php
Contiene la configuracion para todos los request (headers)
request: Peticiones desde la pagina web al backend

Crea la coneccion a la base de datos usada por todos los 
endpoints del backend

Registra todas las posibles rutas para cada entidad que tienen
endpoints en el backend


$router --> Analiza la peticion (request) para determinar que endpoint esta
llegando y que controlador debe atender esa peticion

# db
Se define una clase para conectar con la base de datos
Se usa el port 5432 que es el default de Postgresql
La clase tiene un metodo para crear la conexion usada por el modelo
Tiene un metodo para crear la tablas al comienzo
Tiene un metodo para datos dummy inicial

# models
Hay un archivo por cada entidad.
Cada clase representa una entidad, y sus metodos para operar en la base de datos
Tiene un metodo para cada operacion del CRUD

# routers
Se define como es cada endpoint para cada entidad, y cual/como controlador va a atender ese endpoint

# controllers
Utiliza el `models` para responder a los requests.
Tiene un metodo por cada tipo de request


