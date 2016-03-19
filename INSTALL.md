# INSTALACIÓN

## Requerimentos

O aplicativo foi implementado co framework de desenvolvemento Symfony2, traballando sobre a pila LAMP (tamén funciona sobre o sistema operativo windows).

Os requerimentos son:

    * Servidor Apache2, ou calquera outro servidor HTTP configurado para traballar con PHP (foi desenvolvido sobre ubuntu server)
    * Servidor de base de datos MySQL 5.5.x ou MariaDB (é posible que funcione sobre outros xestores de base de datos que soporte o framework Doctrine)
    * Linguaxe de servidor PHP versión 5.6
    * Extensión de Imagemagick para PHP (php5-imagick). Esta extensión é a encargada do procesamento das imaxes das enquisas escaneadas.
    * Xestor de dependencias composer instalado e no PATH (composer.phar)


## Instalación

### Instalación do sistema en Ubuntu server

    sudo apt-get install lamp-server^
    sudo apt-get install php-pear php5-imagick php5-mcrypt php5-intl
    sudo service apache2 restart


### Instalación do aplicativo

Descargar do repositorio os ficheiros fonte e actualizar as dependencias do proxecto:

    git clone 
    cd enquisas-cocido/
    composer.phar update

Defina os parámetros desexados no ficheiro de configuración do aplicativo parameters.yml:

    $EDITOR ./app/config/parameters.yml

Crear a base de datos co nome configurado no ficheiro de parameters.yml:

    echo "create database enquisa" | mysql -u root -p

Crear as táboas e datos da base de datos coas ferramentas de Symfony2 e Doctrine:

    ./app/console doctrine:schema:create
    ./app/console doctrine:fixtures:load


