# pull official base image
FROM node:22.12.0-alpine AS build

WORKDIR /app
COPY package.json ./
RUN npm install --silent

ENV PATH=/app/node_modules/.bin:$PATH


COPY . ./
RUN npm run build -- --configuration=production

# Production stage
FROM nginx:alpine

RUN rm -rf /etc/nginx/conf.d/* /etc/nginx/nginx.conf /etc/nginx/default.conf /usr/share/nginx/html/*
COPY nginx.conf /etc/nginx/nginx.conf
COPY nginx.conf /etc/nginx/default.conf

# Copy the built Angular files
COPY --from=build /app/dist/recetashare-app/browser/ /usr/share/nginx/html/

# Ensure permissions are set correctly
RUN chmod -R 755 /usr/share/nginx/html

EXPOSE 80
