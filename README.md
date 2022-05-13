[comment]: <> (<p align="center"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></p>)
<h1>FitPlace</h1>
<span>
  <a href="https://www.php.net/">
    <img src="https://img.shields.io/badge/-Php-777BB4.svg?logo=php&style=plastic">
  </a>
  <a href="https://readouble.com/laravel/8.x/ja/installation.html">
    <img src="https://img.shields.io/badge/-Laravel-E74430.svg?logo=laravel&style=plastic">
  </a>
  <a href="https://jp.vuejs.org/v2/guide/">
    <img src="https://img.shields.io/badge/-Vue.js-4FC08D.svg?logo=vue.js&style=plastic">  
  </a>
  <a href="https://www.python.org/">
    <img src="https://img.shields.io/badge/-Python-3776AB.svg?logo=python&style=plastic">
  </a>
  <a href="https://redis.io/topics/introduction">
    <img src="https://img.shields.io/badge/-Redis-D82C20.svg?logo=redis&style=plastic">
  </a>
  <a href="https://dev.mysql.com/doc/">
    <img src="https://img.shields.io/badge/-Mysql-4479A1.svg?logo=mysql&style=plastic">
  </a>
  <a href="https://nginx.org/en/docs/">
    <img src="https://img.shields.io/badge/-Nginx-269539.svg?logo=nginx&style=plastic">
  </a>
  <a href="https://www.docker.com/">
    <img src="https://img.shields.io/badge/-Docker-1488C6.svg?logo=docker&style=plastic">
  </a>
</span>

## About FitPlace
筋トレの予定と実施内容をいれることで筋トレをもっとやりやすくするためのサービス

## Rule
### Issue and PR Prefix
- UI/UX: :star2:
- New Function: :hotsprings:
- Devlop Env: :coffee:
- Bug Fix: :shipit:

## Software
### Language
- PHP(7.4)
- Laravel(8.1)
- Vue.js(2.6)
- Pythojn
- MySQL(8.0)
- Nginx(1.1)
- Redis

### Develop
- Docker & docker-compose
- git
- CircleCI(WIP)

## URI
/ is application path

Use "Vue.js" in front end of user side

/admin/ is administration path

Use "blade.php" if front end of admin side

Under a command can check backend routing
>  docker-compose exec application php artisan route:list

## About Develop Environment
This project using docker to developing  

Under a command set up dev env
> make set-up  

access to http://localhost:8000/

### DB Connection
- HOST: 127.0.0.1 
- PORT 3306
- USER: root
- PASS: root
- DB_NAME: fit_place

### Redis Connection
- HOST: 127.0.0.1
- PORT: 6379

### Execute test
Under a command execute test 
> make test
