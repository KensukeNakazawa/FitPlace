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

※ 元々個人開発の webサービスとして開発していましたが、クローズしたのでソースを後悔します。
本番環境は AWS上のEC2に構築し、Github Actionsを用いてデプロイしていました。

## Main Features
- 会員登録 (メール or Twitter)
- ログイン、ログアウト
- 筋トレの記録
- 曜日ごとの筋トレの予定登録
- Line Notify連携
- Line Notifyによるレポーティング

## Software
### Language
- PHP(7.4)
- Laravel(8.1)
- Vue.js(2.6)
- Python
- MySQL(8.0)
- Nginx(1.1)
- Redis

## Setup
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

### Check route
>  docker-compose exec application php artisan route:list
