# ============================================================
#  ETAPA 1 — BUILD DE ANGULAR
#  Usa Node.js para compilar la aplicación
# ============================================================

# Imagen base oficial de Node en versión ligera (Alpine)
FROM node:22.12.0-alpine AS build

# Directorio de trabajo dentro del contenedor
WORKDIR /app

# Copia package.json para instalar dependencias
COPY package.json ./

# Instala dependencias sin mostrar demasiada salida
RUN npm install --silent

# Añade node_modules al PATH para ejecutar comandos npm globales
ENV PATH=/app/node_modules/.bin:$PATH

# Copia TODO el código fuente al contenedor
COPY . ./

# Compila la app Angular en modo producción
RUN npm run build -- --configuration=production



# ============================================================
#  ETAPA 2 — SERVIDOR NGINX PARA PRODUCCIÓN
# ============================================================

# Imagen base de Nginx en versión Alpine (ligera)
FROM nginx:alpine

# Eliminamos configuraciones por defecto de Nginx
RUN rm -rf /etc/nginx/conf.d/* /etc/nginx/nginx.conf /etc/nginx/default.conf /usr/share/nginx/html/*

# Copiamos nuestra configuración personalizada de Nginx
COPY nginx.conf /etc/nginx/nginx.conf
COPY nginx.conf /etc/nginx/default.conf

# Copiamos los archivos generados por Angular en la etapa de build
COPY --from=build /app/dist/recetashare-app/browser/ /usr/share/nginx/html/

# Damos permisos adecuados a los archivos servidos por Nginx
RUN chmod -R 755 /usr/share/nginx/html

# Exponemos el puerto 80 para acceder a la aplicación
EXPOSE 80
