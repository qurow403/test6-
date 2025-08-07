# test6

## 概要
このプロジェクトは勤怠アプリ作成を目的としたものです。


## 環境構築
・Dockerビルド
1.git clone git@github.com:qurow403/test6-.git
2.docker-compose up -d


・Laravel環境構築
1.docker-compose exec php bash
2.composer install
3.データベースに接続するために.envファイルを作成
  .envファイルは、.env.exampleファイルをコピーして作成
  作成後、環境変数を設定
  cp .env.example .env
4..env ファイルの変更
    DB_HOSTをmysqlに変更
    DB_DATABASEをlaravel_dbに変更
    DB_USERNAMEをlaravel_userに変更
    DB_PASSをlaravel_passに変更
    MAIL_FROM_ADDRESSに送信元アドレスを設定
5.php artisan key:generate
6.php artisan migrate
7.php artisan db:seed
8.php artisan test


## ログイン情報
・一般ユーザー
   id：user@example.com
   pass：password
・管理者
   id：admin@example.com
   pass：password


・使用技術
PHP 8.4.3
Laravel 8.83.29
MySQL 15.1
MailHog最新


## ER図
![ER図](src/docs/er_diagram.png)

[→ dbdiagram.io で開く（編集・拡大表示・編集可）](https://dbdiagram.io/d/683bdd30bd74709cb77f7db5)


・開発用URL
開発環境：http://localhost/
phpMyAdmin:http://localhost:8080/
MailHog：http://localhost:8025/
