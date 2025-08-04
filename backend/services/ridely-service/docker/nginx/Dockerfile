FROM nginx:1.25-alpine

COPY ./certificate.crt  /etc/nginx/conf.d/certificate.crt
COPY ./certificate.key   /etc/nginx/conf.d/certificate.key

# Remove the default localhost server config
RUN rm /etc/nginx/conf.d/default.conf
