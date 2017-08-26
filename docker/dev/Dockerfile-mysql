FROM mysql:5.7.19

ENV MYSQL_ROOT_PASSWORD=ptron
ENV MYSQL_DATABASE=ptron

ADD schema.sql /docker-entrypoint-initdb.d
ADD seed.sql /docker-entrypoint-initdb.d
