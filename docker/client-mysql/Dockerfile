FROM mysql:8.4.5-oraclelinux9

RUN microdnf install dnf -y
RUN dnf -y update
RUN dnf -y install rsyslog less vim-minimal glibc-langpack-ja

ENV LANG=ja_JP.UTF-8
ENV LANGUAGE=ja_JP:ja
ENV LC_ALL=ja_JP.UTF-8

ENTRYPOINT ["docker-entrypoint.sh"]
EXPOSE 3306
CMD ["mysqld"]