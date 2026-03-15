FROM node:22

WORKDIR /app

COPY recetashare-app/package*.json ./
RUN npm install

COPY recetashare-app .

RUN npm run build

RUN npm install -g serve

EXPOSE 80

CMD ["serve", "-s", "dist", "-l", "80"]