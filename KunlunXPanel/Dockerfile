FROM ubuntu
RUN export TZ=UTC
RUN apt-get update -y
ARG DEBIAN_FRONTEND=noninteractive
ENV TZ=UTC
RUN apt-get install -y tzdata
RUN apt-get install -y curl
RUN apt-get install -y net-tools
RUN apt-get install -y inetutils-ping
RUN curl -sL https://deb.nodesource.com/setup_14.x | bash -
RUN apt-get install -y nodejs
RUN npm config set registry=http://registry.npm.taobao.org/
RUN npm install -g pm2
RUN mkdir -p /KunlunXPanel
WORKDIR /KunlunXPanel
COPY . /KunlunXPanel
RUN npm i
EXPOSE  8080





