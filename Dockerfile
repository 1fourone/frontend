# Import custom "afs" image
FROM 1fourone/php-apache-python-afs:latest
COPY ./mid /var/www/html
RUN chown -R www-data:www-data /var/www/html