FROM node:22

WORKDIR /app

COPY recetashare-app/package*.json ./
RUN npm install

COPY recetashare-app .

RUN npm run build

RUN npm install -g serve

EXPOSE 3000

CMD ["serve", "-s", "dist/recetashare-app/browser", "-l", "3000", "--single"]