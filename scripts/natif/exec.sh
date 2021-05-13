#!/bin/bash

ACTION=$1

CONSOLE='php bin/console'
HOSTS_FILE="/etc/hosts"
HOSTS_TMP_FILE="/etc/hosts.tmp"

hosts=(
    "api.mobads.localhost"
)

execute() {
    $ACTION
}

no-implemented() {
    echo "Command not implemented"
}

#############
# DOCKER
#############

up() {
    sudo apachectl start
}

remove() {
    no-implemented
}

info() {
    echo -e "\n\033[35m==========  Infos  ==========\n\033[37m"

    echo -e "\033[33m Hosts:\033[37m"
    echo -e "\033[37m    - Api:     \033[34m http://${hosts[0]}\033[37m"
}

exec() {
    no-implemented
}

envs() {
    cp behat.yml.dist behat.yml
    brew services start httpd
    brew services start php
}

envs-remove() {
    brew services stop httpd
    brew services stop php
    sudo apachectl stop
    rm -rf coverage
}

images-build() {
    echo "You must add vhost, an example is in script/natif/http/vhost.conf"
}

images-remove() {
    echo "You must remove vhost";
}

networks-create() {
    no-implemented
}

networks-remove() {
    no-implemented
}

hosts-add() {
    for i in ${hosts[*]}; do
        HOST="127.0.0.1       ${i}"
        grep -q -F "${HOST}" "${HOSTS_FILE}" || echo "${HOST}" >> "${HOSTS_FILE}"
    done
}

hosts-remove() {
    for i in ${hosts[*]}; do
        PATTERN="/${i}/d"
        sed "${PATTERN}" "${HOSTS_FILE}" > "${HOSTS_TMP_FILE}" && mv "${HOSTS_TMP_FILE}" "${HOSTS_FILE}"
    done
}

swarm-init() {
    no-implemented
}

volumes-create() {
    no-implemented
}

#############
# SYMFONY
#############

install() {
    cmd 'composer install'
}

#############
# APP
#############

fixtures() {
#            $CONSOLE f:e:p --no-interaction --env=test &&\
     cmd "\
            rm -rf public/upload && \
            mkdir -p public/upload/CardVtc && \
            mkdir -p public/upload/Insurance && \
            mkdir -p public/upload/Kbis && \
            mkdir -p public/upload/Rib && \
            mkdir -p public/upload/Facture && \
            $CONSOLE d:d:d --force --env=test &&\
            $CONSOLE d:d:c --no-interaction --env=test &&\
            $CONSOLE d:s:u --force --env=test &&\
            $CONSOLE d:f:l --no-interaction --env=test &&\
            rm -rf var/log/* var/cache/*\
      "
}

swagger() {
    cmd "$CONSOLE api:swagger:export --output=public/schema.json"
}

migration() {
     cmd "\
            $CONSOLE d:d:d --force &&\
            $CONSOLE d:d:c --no-interaction &&\
            $CONSOLE d:m:m --no-interaction &&\
            $CONSOLE d:m:diff --no-interaction \
      "
}

#############
# TEST
#############

tests() {
    tu
    tf
}

tu() {
    cmd "vendor/bin/simple-phpunit"
}

tu_coverage() {
    cmd "vendor/bin/simple-phpunit --coverage-html=coverage/unit --coverage-xml=coverage/unit/coverage-xml --log-junit=coverage/unit/junit.xml"
    cmd "vendor/bin/infection --threads=4 --coverage=coverage/unit --only-covered"
}

tf() {
    fixtures
    cmd "php -d memory_limit=-1 vendor/behat/behat/bin/behat --no-coverage --format progress --stop-on-failure"
}

tf_coverage() {
    fixtures
    cmd "php -d memory_limit=-1 vendor/behat/behat/bin/behat --format progress"
}

#############
# AUDIT
#############

phpcs() {
    cmd "./vendor/bin/phpcs --standard=.phpcs.xml src"
}

phpcpd() {
    cmd "./vendor/sebastian/phpcpd/phpcpd src"
}

phpmd() {
    cmd "./vendor/phpmd/phpmd/src/bin/phpmd src text .phpmd.xml --exclude src/DataFixtures"
}

php_cs_fixer() {
    ./vendor/friendsofphp/php-cs-fixer/php-cs-fixer fix --dry-run --using-cache=no src
    ./vendor/friendsofphp/php-cs-fixer/php-cs-fixer fix --dry-run --using-cache=no tests
    ./vendor/friendsofphp/php-cs-fixer/php-cs-fixer fix --dry-run --using-cache=no features/bootstrap


    ./vendor/friendsofphp/php-cs-fixer/php-cs-fixer fix --dry-run --using-cache=no --rules=@PSR1 src
    ./vendor/friendsofphp/php-cs-fixer/php-cs-fixer fix --dry-run --using-cache=no --rules=@PSR1 tests
    ./vendor/friendsofphp/php-cs-fixer/php-cs-fixer fix --dry-run --using-cache=no --rules=@PSR1 features/bootstrap


    ./vendor/friendsofphp/php-cs-fixer/php-cs-fixer fix --dry-run --using-cache=no --rules=@PSR2 src
    ./vendor/friendsofphp/php-cs-fixer/php-cs-fixer fix --dry-run --using-cache=no --rules=@PSR2 tests
    ./vendor/friendsofphp/php-cs-fixer/php-cs-fixer fix --dry-run --using-cache=no --rules=@PSR2 features/bootstrap


    ./vendor/friendsofphp/php-cs-fixer/php-cs-fixer fix --dry-run --using-cache=no --rules=@Symfony src
    ./vendor/friendsofphp/php-cs-fixer/php-cs-fixer fix --dry-run --using-cache=no --rules=@Symfony tests
    ./vendor/friendsofphp/php-cs-fixer/php-cs-fixer fix --dry-run --using-cache=no --rules=@Symfony features/bootstrap
}

php_cs_fixer_apply() {
    ./vendor/friendsofphp/php-cs-fixer/php-cs-fixer fix --using-cache=no --verbose src/
    ./vendor/friendsofphp/php-cs-fixer/php-cs-fixer fix --using-cache=no --verbose tests/
    ./vendor/friendsofphp/php-cs-fixer/php-cs-fixer fix --using-cache=no --verbose features/bootstrap/

    ./vendor/friendsofphp/php-cs-fixer/php-cs-fixer fix --using-cache=no --rules=@PSR1 --verbose src
    ./vendor/friendsofphp/php-cs-fixer/php-cs-fixer fix --using-cache=no --rules=@PSR1  --verbose tests
    ./vendor/friendsofphp/php-cs-fixer/php-cs-fixer fix --using-cache=no --rules=@PSR1 --verbose features/bootstrap

    ./vendor/friendsofphp/php-cs-fixer/php-cs-fixer fix --using-cache=no --rules=@PSR2 --verbose src
    ./vendor/friendsofphp/php-cs-fixer/php-cs-fixer fix --using-cache=no --rules=@PSR2  --verbose tests
    ./vendor/friendsofphp/php-cs-fixer/php-cs-fixer fix --using-cache=no --rules=@PSR2 --verbose features/bootstrap

    ./vendor/friendsofphp/php-cs-fixer/php-cs-fixer fix --using-cache=no --rules=@Symfony --verbose src
    ./vendor/friendsofphp/php-cs-fixer/php-cs-fixer fix --using-cache=no --rules=@Symfony  --verbose tests
    ./vendor/friendsofphp/php-cs-fixer/php-cs-fixer fix --using-cache=no --rules=@Symfony --verbose features/bootstrap
}

phpmetrics() {
    cmd "phpdbg -qrr ./vendor/bin/phpmetrics --report-html=reports --exclude=vendor,bin,reports,tests,var,features,src/Kernel.php,src/DataFixtures,src/Migrations ./"
}

cmd() {
    bash -c "$1"
}

execute
