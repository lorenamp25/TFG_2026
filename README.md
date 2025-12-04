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




# NOTAS/PREGUNTAS
-1 .si actualizo algo de datos  en el localhost tiene que aparecer en el proyecto desplegado  (87.....) en la base de datos por ejemplo , y ver que se creo bien en la base de datos 
   **Esto no puede suceder, por que localhost es el entorno de desarrollo y el servidor 87xxx es el de produccion, cada uno tiene su propia base de datos y servicios.**

-2 en la validación de contraseña  de registro dice de poner un método o como se diga que es pattern me dijo, para validar mejor las contraseñas y sean mas seguras
    **LISTO**
  
-3 cuando elimino una receta que me salga 1 mensaje en admin de que se borro correctamente ya sea receta categoría o ingrediente y que pregunte si quiere ocornfirmar la eliminiacion  (creo que eso ya lo tenemos pero por si acaso)
   --EN PROCESO--

-4 que el admin que soy yo pueda poder borrar todas las recetas, no solo las que cree yo  (PORQUE SOY EL ADMIN)
   **LISTO**

-5 cuando pulsas una receta para verla te sale categoría 4 , tendría que salir la categoría que selecciono o si no lo consigo dice que quite esa opción y no se vea 
   **LISTO**

-6.a. también dijo que en la tabla de ingredientes de las recetas  donde pone cantidad tiene que poner numero porque si pones 0 te deja crear la receta y eso no se debería poder hacer 
   **LISTO**

-6.b. y en unidad poner un select parap poder elegir gr o kilos o cucharadas o unidades etc(recomendado)
  __SI HAY TIEMPO__

-7 en el apartado ingredientes tengo que decir el funcionamiento de eso el me dijo que dijera que de ahí se cargan a la base dedatos y que es visual solo 
   **LISTO** Es para la defensa.

-8 comprobar que cuando te registras como nuevo usuario te deja poner la fecha cualquiera después de hoy ( y eso esta mal no te puede dejar seleccionar un día que todavía no ha sido)
   **LISTO**

-9 si por ejemplo crea pepe que es un usuario normal una receta yo siendo admin debería poder borrar esa receta 
   **LISTO** Repetido punto 4.

-10 puedo confirmar 1 receta sin rellenar todos los campos (eso esta mal)
   **LISTO**

-11 cada usuario normal puede editar y borrar sus recetas (borrar solo podrá el admin no?)

-12  arreglar el botón de registrarse el que pone ya tienes cuenta inicia sesión (el me dijo que si no consigo enlazar eso que borre directamente el botón y no me lie) 
 **LISTO**
-13 el css bien del menú de admin porque cuesta apretar en el desplegable 
 **LISTO**
14-arreglar en versión móvil el apartado de categorías y ingredientes del apartado admin porque tapa el menú por detrás   
preguntas
 **LISTO**
-15 en versión móvil cambiar el color directamente el texto donde pone admin para diferenciarlo de que es un apartado diferente 
 **LISTO**
-16 que el admin pueda borrar todas las recetas creadas 
   **LISTO** Repetido punto 4.

como encriptamos la contraseña con que métodos funciones etc 
con que método enlazo al pulsar una categoría para que me lleve a esa categoría con esas recetas 
como encripto la contraseña 
como puse la opción de admin dice =true ? 

como haces para actualizar el servidor en render 