FROM wordpress:4.7.2-php5.6-apache
MAINTAINER Diego Ruggeri <diego@ruggeri.net.br> (@diegor2)

# based upon wordpress Dockerfile: https://git.io/vykuo
# install php extensions needed by https://github.com/bitpay/woocommerce-plugin

RUN set -ex; \
	apt-get update; apt-get install -y libgmp-dev libmcrypt-dev; \
  rm -rf /var/lib/apt/lists/*; \
  ln -s /usr/include/x86_64-linux-gnu/gmp.h /usr/include/gmp.h ; \
  docker-php-ext-install bcmath gmp mcrypt;

COPY entrypoint-wrapper.sh /usr/local/bin/

ENTRYPOINT ["entrypoint-wrapper.sh"]

CMD ["apache2-foreground"]
