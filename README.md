# Lorena TFG

# Modelo de datos
## Entidades
* Usuario

* Categoria

* Receta
* RecetaIngrediente
* RecetaInstruccion

* Comentario

* SolicutudCambio

* Mensajes



# Preguntas
1. Debo crear la opcion para registrar nuevos usuarios?
2. 


# Acceso a la base de datos
Desde la terminal en el Docker-desktop:
 `psql -U receta`       --> Conecta a la base de datos
 `\dt`                  --> Lista las tablas creadas
 `\d categorias`        --> Muestra la estructura de la tabla pedida


Herramienta para generar el diagrama:
   https://dbdiagram.io/d

pg_dump --schema-only  receta -U receta


# Progreso temas puntuales
1. Falta actualizar DER (categoria)
2. Login.
3. Agregar que hacer al click en card categoria
4. Leer receta con datos del usuario incluido en la lista ?
5. Arreglar CSS del menu
6. 


# Funcionamiento:
2. Pagina Categorias: Mostrar card de las categorias y al click en categoria ir a las recetas de la categoria.
3. Pagina Ingredientes: Mostrar card de los ingredientes y al click en el ingrediente ir a las recetas que usan ese ingrediente
4. Pagina Recetas: Card de todas las recetas, al click en una receta mostrar la receta completa, donde se puede comentar
5. Admin Categorias: Falta diseño, falta no acceso si no admin
6. Admin Ingredientes: Falta todo
7. Login



# Deploy

Revisar en `environment.ts` la url correcta


Enviroment variables backend:
DB_HOST=dpg-d4l0ifje5dus73fgmte0-a
DB_NAME=receta_ap16
DB_USER=receta
DB_PASS=yc9bZgwJE8z0P6H2qY9ceSoSe9nmU81z
DB_INIT=1