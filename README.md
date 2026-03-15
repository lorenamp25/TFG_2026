
# Acceso a la base de datos
Desde la terminal en el Docker-desktop:
 `psql -U receta`       --> Conecta a la base de datos
 `\dt`                  --> Lista las tablas creadas
 `\d categorias`        --> Muestra la estructura de la tabla pedida


Herramienta para generar el diagrama:
   https://dbdiagram.io/d

pg_dump --schema-only  receta -U receta




# Funcionamiento:
2. Pagina Categorias: Mostrar card de las categorias y al click en categoria ir a las recetas de la categoria.
3. Pagina Ingredientes: Mostrar card de los ingredientes y al click en el ingrediente ir a las recetas que usan ese ingrediente
4. Pagina Recetas: Card de todas las recetas, al click en una receta mostrar la receta completa, donde se puede comentar
5. Admin Categorias: 
6. Admin Ingredientes: 





