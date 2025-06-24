# laravel-oauth

## Over View
OAuth2プロセス(+ Open ID Connect)学習用プロジェクト

## Details
OAuth2の認可サーバー、リソースサーバー、クライアントアプリケーションをそれぞれ別のコンテナで構築し、学習用に利用する。\
さらに、OAuth2認可機能を基盤に追加実装した Open ID Connect (OIDC) による認証プロセスを確認することができる。

### データ配置
それぞれに配置されているデータは下記の通り。

- 認可サーバー (Laravel Passport を利用)
  - ユーザー
  - ロール
  - スコープ
  - パーミッション
  - OAuth2クライアント
    - PasswordGrant用クライアント
    - CodeGrant用クライアント
    - リソースサーバー用クライアント
    - OIDC用CodeGrant用クライアント 
- リソースサーバー
  - 顧客情報
  - 商品情報
- クライアントアプリケーション
  - ユーザートークン(認可プロセス中に取得する情報)

### プロジェクト前提
認可プロセスにおけるプロジェクト挙動の前提は下記の通り

- 認可サーバーで実装されている認可方式は下記の通り
  - CodeGrant(認可コードグラント)を利用した認可プロセス
  - PasswordGrant(パスワードグラント)を利用した認可プロセス
- 認可サーバーは下記インターフェースを提供
  - CodeGrant(Code取得用)用ユーザー認証Web画面
  - CodeGrant用トークン取得API 
  - PasswordGrant用トークン取得API
  - イントロスペクションAPI
- 各ユーザーには下記のロールが割り当てられる
  - 管理者(manager)
    - 全てのリソースにアクセス可能
  - 一般ユーザー(staff)
    - 商品情報のみアクセス可能
- ロールに関連してユーザーに割り当てられるスコープは下記の通り
  - 管理者(manager) : confidential(機密情報)
  - 一般ユーザー(staff) : general(一般情報)
- ロールに関連してユーザーに割り当てられるパーミッションは下記の通り
  - 管理者(manager) : read, write, delete
  - 一般ユーザー(staff) : read
- リソースサーバーは下記インターフェースを提供
  - 顧客情報取得API
  - 商品情報取得API
- リソースサーバーから認可サーバーへのイントロスペクションAPIは、認可状況の確認が必要になった都度リクエストされる。(商用利用の場合はキャッシュ利用を推奨)
- クライアントアプリケーションは認可プロセス確認用ツールの位置付けのため、認証機能は実装されていない。

### 認可プロセス
このプロジェクトにおける認可プロセスの流れは下記の通り。

#### PasswordGrant
```mermaid
sequenceDiagram
    participant User
    participant ClientApp as クライアントアプリケーション
    participant AuthServer as 認可サーバー (Laravel Passport)
    participant ResourceServer as リソースサーバー

    %% Step 1: Login UI
    User->>ClientApp: アプリケーションにアクセス (ユーザー名)
    ClientApp-->>User: トークン取得情報入力画面を表示

    %% Step 2: Token Request with Credentials
    User->>ClientApp: PasswordGrantを選択 (パスワードを入力)
    ClientApp->>AuthServer: Password Grantでトークン要求 (username/password) - (4)
    AuthServer-->>ClientApp: アクセストークン発行 - (7)

    %% Step 3: Access Protected Resource
    ClientApp-->>User: 取得情報選択画面表示
    User->>ClientApp: 取得情報(顧客 or 商品)選択
    ClientApp->>ResourceServer: アクセストークン付きでリソース要求 - (9)
    ResourceServer->>AuthServer: トークンのイントロスペクション (有効性・スコープ確認) - (10)
    AuthServer-->>ResourceServer: トークン情報を返却 - (11)

    %% Step 4: Response
    ResourceServer-->>ClientApp: 保護リソースのデータ返却
    ClientApp-->>User: 結果を表示
```

#### CodeGrant
```mermaid
sequenceDiagram
  participant User
  participant ClientApp as クライアントアプリケーション
  participant AuthServer as 認可サーバー (Laravel Passport)
  participant ResourceServer as リソースサーバー

%% Step 1: Authorization Request
  User->>ClientApp: アプリケーションにアクセス (ユーザー名)
  ClientApp-->>User: トークン取得情報入力画面を表示
  User->>ClientApp: CodeGrantを選択
  ClientApp-->>User: 認可エンドポイントにリダイレクト
  User-->>AuthServer: 認可リクエスト (リダイレクトURL含む) - (1)

%% Step 2: Authentication and Authorization
  AuthServer-->>User: ログイン画面表示
  User->>AuthServer: 認証情報入力
  AuthServer-->>User: 認可画面表示
  User->>AuthServer: アクセス許可

%% Step 3: Redirect with Code
  AuthServer-->>User: クライアントアプリケーションへリダイレクト (code付き) - (3)
  User-->>ClientApp: リダイレクト (code)

%% Step 4: Token Exchange
  ClientApp->>AuthServer: 認可コードでトークン要求 - (5)
  AuthServer-->>ClientApp: アクセストークン発行 - (7)

%% Step 5: Access Protected Resource
  ClientApp-->>User: 取得情報選択画面表示
  User->>ClientApp: 取得情報(顧客 or 商品)選択
  ClientApp->>ResourceServer: アクセストークン付きでリソース要求 - (9)
  ResourceServer->>AuthServer: トークンのイントロスペクション (有効性・スコープ確認) - (10)
  AuthServer-->>ResourceServer: トークン情報を返却 - (11)

%% Step 6: Response
  ResourceServer-->>ClientApp: 保護リソースのデータ返却
  ClientApp-->>User: 結果を表示
```

### OIDC認証プロセス
このプロジェクトにおけるOIDC認証プロセスの流れは下記の通り。
```mermaid
sequenceDiagram
  participant User
  participant ClientApp as クライアントアプリケーション
  participant AuthServer as 認可サーバー (Laravel Passport)
  participant ResourceServer as リソースサーバー

%% Step 1: Authorization Request
  User->>ClientApp: アプリケーションにアクセス (ユーザー名)
  ClientApp-->>User: トークン取得情報入力画面を表示
  User->>ClientApp: CodeGrantを選択
  ClientApp-->>User: 認可エンドポイントにリダイレクト
  User-->>AuthServer: 認可リクエスト (リダイレクトURL + scope=openid 含む) - (2)

%% Step 2: Authentication and Authorization
  AuthServer-->>User: ログイン画面表示
  User->>AuthServer: 認証情報入力
  AuthServer-->>User: 認可画面表示
  User->>AuthServer: アクセス許可

%% Step 3: Redirect with Code
  AuthServer-->>User: クライアントアプリケーションへリダイレクト (code付き) - (3)
  User-->>ClientApp: リダイレクト (code)

%% Step 4: Token Exchange
  ClientApp->>AuthServer: 認可コードでトークン要求(OIDC用) - (6)
  AuthServer-->>ClientApp: アクセストークン + IDトークン 発行 - (8)

%% Step 5: Token Parse
  ClientApp->>ClientApp: IDトークンの検証(JWT署名確認)
  ClientApp-->>User: ユーザー情報表示(IDトークンの内容)

%% Step 6: Request user details
  User->>ClientApp: ユーザー詳細情報取得
  ClientApp->>AuthServer: ユーザー情報要求(`userinfo`エンドポイント) - (12)
  AuthServer-->>ClientApp: ユーザー情報返却 - (13)
  ClientApp-->>User: ユーザー情報表示
```

### リクエスト例
#### (1) 認可リクエスト (リダイレクトURL含む)
```html
http://localhost.auth-app.sample.jp/oauth/authorize?client_id=4&redirect_uri=http%3A%2F%2Flocalhost.client-app.sample.jp%2Fauth%2Fcallback&response_type=code&scope=&state=stateDummy
```

#### (2) 認可リクエスト (リダイレクトUR + scope=openid L含む)
```html
http://localhost.auth-app.sample.jp/oauth/authorize?client_id=4&redirect_uri=http%3A%2F%2Flocalhost.client-app.sample.jp%2Fauth%2Fcallback&response_type=code&scope=openid%20profile%20email&state=stateDummy
```

#### (3) アクライアントアプリケーションへリダイレクト (code付き)
```html
http://localhost.client-app.sample.jp/auth/callback?code=def...a56&state=stateDummy
```

#### (4) Password Grantでトークン要求 (username/password)
PasswordGrant用クライアントにリクエスト
```shell
curl -i -X POST http://localhost.auth-app.sample.jp/oauth/token \
  -H "Content-Type: application/json" \
  -d '{
    "grant_type": "password",
    "client_id": "1",
    "client_secret": "2Oi***uV2",
    "username": "manager@test.com",
    "password": "password123",
    "scope": "*"
}'
```

#### (5) 認可コードでトークン要求
CodeGrant用クライアントにリクエスト。
```shell
curl -X POST http://localhost.auth-app.sample.jp/oauth/token \
  -H "Content-Type: application/json" \
  -d '{
    "grant_type": "authorization_code",
    "client_id": "2",
    "client_secret": "pti***v6B",
    "redirect_uri": "http://localhost.client-app.sample.jp/auth/callback",
    "code": "def...a56"
}'
```

#### (6) 認可コードでトークン要求(OIDC用)
OIDC用CodeGrantクライアントにリクエスト。(クライアントアプリケーションへのコールバックであるリダイレクトURLが異なる)
```shell
curl -X POST http://localhost.auth-app.sample.jp/oauth/token \
  -H "Content-Type: application/json" \
  -d '{
    "grant_type": "authorization_code",
    "client_id": "4",
    "client_secret": "B3r***BSS",
    "redirect_uri": "http://localhost.client-app.sample.jp/oidc/callback",
    "code": "def***e0b"
}'
```

#### (7) アクセストークン発行
```json
{
  "token_type": "Bearer",
  "expires_in": 1296000,
  "access_token": "eyJ***n0k",
  "refresh_token": "def***36e"
}
```

#### (8) アクセストークン + IDトークン 発行
```json
{
  "token_type": "Bearer",
  "expires_in": 1296000,
  "access_token": "eyJ***fyc",
  "refresh_token": "def***889",
  "id_token": "eyJ***Bp0"
}
```

##### id_token(JWT claims)構成例
- iss = "http://localhost.auth-app.sample.jp"
- jti = "4b2f7a6b82808bad06da38658e01082c"
- iat = {DateTimeImmutable}
  - date = "2025-06-22 19:45:24.699444"
  - timezone_type = {int} 3
  - timezone = "Asia/Tokyo"
- exp = {DateTimeImmutable}
  - date = "2025-06-22 20:45:24.699444"
  - timezone_type = {int} 3
  - timezone = "Asia/Tokyo"
- sub = "1"
- name = "manager"
- email = "manager@test.com"
- email_verified = true

#### (9) アクセストークン付きでリソース要求
```shell
curl -X GET http://localhost.resource-app.sample.jp/api/customers \
  -H "Authorization: Bearer eyJ***n0k"
```

#### (10) トークンのイントロスペクション (有効性・スコープ確認)
リソースサーバー用クライアントにリクエスト
```shell
curl -X POST http://localhost.auth-app.sample.jp/api/oauth/introspect \
  -H "Authorization: Basic $(echo -n '3:yig***9HH' | base64)" \
  -H "Content-Type: application/json" \
  -d '{"token": "eyJ***n0k"}'
```

#### (11) トークン情報を返却
ここで応答される`client_id`はトークン要求時のクライアントID
```json
{
  "active": true,
  "client_id": 2,
  "username": "manager@test.com",
  "permissions": ["read", "write", "delete"],
  "scopes": ["confidential", "general"],
  "exp": 1780317075,
  "sub": 1,
  "iss": "http://localhost.auth-app.sample.jp",
  "token_type": "access_token"
}
```

#### (12) ユーザー情報要求(`userinfo`エンドポイント)
```shell
curl -X GET localhost.auth-app.sample.jp/api/userinfo \
  -H "Authorization: Bearer eyJ***hug"
```

#### (13) ユーザー情報返却
```json
{
  "sub": 1,
  "name": "manager",
  "email": "manager@test.com",
  "role_id": 1,
  "created_at": "2025-06-22 21:02:01",
  "updated_at": "2025-06-23 09:36:15"
}
```

## composition
- リバースプロキシ
  - Nginx 
- 認可サーバー
  - (バックエンド共通)
  - Breeze
  - Inertia
  - Vue3
- リソースサーバー
  - (バックエンド共通)
- クライアントアプリケーション
  - (バックエンド共通)
  - Inertia
  - Vue3
- バックエンド共通
  - Nginx
  - Laravel 
- その他
  - MySQL
  - Docker
  - Redis

## install and Usage
### 1. コンテナビルド
```shell
docker compose build
```

### 2. 初期設定のためにコンテナ起動
```shell
docker compose up -d auth-server auth-db resource-server resource-db client-server client-db
```

### 3. 初期設定(バックエンドアプリケーション環境)
#### 認可サーバー
```shell
docker compose exec auth-server bash
composer install
cp -p /var/www/app/.env.example /var/www/app/.env
php artisan key:generate
php artisan passport:keys
exit
```

#### リソースサーバー
```shell
docker compose exec resource-server bash
composer install
cp -p /var/www/app/.env.example /var/www/app/.env
php artisan key:generate
exit
````

#### クライアントアプリケーション
```shell
docker compose exec client-server bash
composer install
cp -p /var/www/app/.env.example /var/www/app/.env
php artisan key:generate
openssl genrsa -out storage/oauth-private.key 4096
openssl rsa -in storage/oauth-private.key -pubout -out storage/oauth-public.key
exit
````

※フロントアプリケーションのインストールは[後続作業](#7-全コンテナ起動)でnodeコンテナ起動時に自動で実行されるため割愛

### 4. 初期設定(データベース設定)
#### 認可サーバー
```shell
docker compose exec auth-db bash
mysql -u root -proot -e "CREATE USER 'laravelUser' IDENTIFIED BY 'password000'"
mysql -u root -proot -e "GRANT all ON *.* TO 'laravelUser'"
mysql -u root -proot -e "FLUSH PRIVILEGES"
mysql -u root -proot -e "CREATE DATABASE auth_management"
exit
```
```shell
docker compose exec auth-server bash
php artisan migrate:fresh --seed
exit
```

#### リソースサーバー
```shell
docker compose exec resource-db bash
mysql -u root -proot -e "CREATE USER 'laravelUser' IDENTIFIED BY 'password000'"
mysql -u root -proot -e "GRANT all ON *.* TO 'laravelUser'"
mysql -u root -proot -e "FLUSH PRIVILEGES"
mysql -u root -proot -e "CREATE DATABASE resource_management"
exit
```
```shell
docker compose exec resource-server bash
php artisan migrate:fresh --seed
exit
```

#### クライアントアプリケーション
```shell
docker compose exec client-db bash
mysql -u root -proot -e "CREATE USER 'laravelUser' IDENTIFIED BY 'password000'"
mysql -u root -proot -e "GRANT all ON *.* TO 'laravelUser'"
mysql -u root -proot -e "FLUSH PRIVILEGES"
mysql -u root -proot -e "CREATE DATABASE client_management"
exit
```
```shell
docker compose exec client-server bash
php artisan migrate:fresh --seed
exit
```

### 5. hosts設定
hosts に下記エントリーを追加
```shell
127.0.0.1 localhost.auth-app.sample.jp
127.0.0.1 localhost.auth-node.sample.jp
127.0.0.1 localhost.resource-app.sample.jp
127.0.0.1 localhost.resource-node.sample.jp
127.0.0.1 localhost.client-app.sample.jp
127.0.0.1 localhost.client-node.sample.jp
```

### 6. コンテナ停止
```shell
docker compose down
```

### 7. 全コンテナ起動
```shell
docker compose up -d
```

### 8. コンテナ確認
```shell
docker ps
docker compose ps
```

### 9. OAuthクライアント作成
```shell
docker compose exec auth-server bash
```

PasswordGrant用クライアントを作成
```shell
php artisan passport:client --password

 What should we name the password grant client? [Laravel Password Grant Client]:
 > PasswordGrantClient

 Which user provider should this client use to retrieve users? [users]:
  [0] users
 > 

   INFO  Password grant client created successfully.  

  Client ID ...................................................................................................................................... 1  
  Client secret ........................................................................................... 2Oi**********************************uV2 
```

CodeGrant用クライアント(クライアントアプリケーション用)を作成
```shell
php artisan passport:client

 Which user ID should the client be assigned to? (Optional):
 > 

 What should we name the client?:
 > CodeGrantClient

 Where should we redirect the request after authorization? [http://localhost.auth-app.sample.jp/auth/callback]:
 > http://localhost.client-app.sample.jp/auth/callback

   INFO  New client created successfully.  

  Client ID ...................................................................................................................................... 2  
  Client secret ........................................................................................... pti**********************************v6B
```

リソースサーバー用クライアントを作成
```shell
php artisan passport:client --client

 What should we name the client? [Laravel ClientCredentials Grant Client]:
 > ResourceServerClient

   INFO  New client created successfully.  

  Client ID ...................................................................................................................................... 3  
  Client secret ........................................................................................... yig**********************************9HH 
```

OIDC用CodeGrantクライアント(クライアントアプリケーション用)を作成
```shell
php artisan passport:client

 Which user ID should the client be assigned to? (Optional):
 > 

 What should we name the client?:
 > CodeGrantClientForOIDC     

 Where should we redirect the request after authorization? [http://localhost.auth-app.sample.jp/auth/callback]:
 > http://localhost.client-app.sample.jp/oidc/callback

   INFO  New client created successfully.  

  Client ID ...................................................................................................................................... 4  
  Client secret ........................................................................................... B3r**********************************BSS
```

```shell
exit
```

### 10. OAuthクライアント情報をアプリケーションに設定
#### リソースサーバー
`resource-app/.env`の下記エントリーに、作成したクライアント情報を設定。
```env
INTROSPECTION_CLIENT_ID=(リソースサーバー用クライアントID)
INTROSPECTION_CLIENT_SECRET=(リソースサーバー用クライアントシークレット)
```

#### クライアントアプリケーション
`client-app/.env`の下記エントリーに、作成したクライアント情報を設定。
```env
AUTH_PASSWORD_GRANT_CLIENT_ID=(PasswordGrant用クライアントID)
AUTH_PASSWORD_GRANT_CLIENT_SECRET=(PasswordGrant用クライアントシークレット)

AUTH_CODE_GRANT_CLIENT_ID=(CodeGrant用クライアントID)
AUTH_CODE_GRANT_CLIENT_SECRET=(CodeGrant用クライアントシークレット)

OIDC_CODE_GRANT_CLIENT_ID=(OIDC用CodeGrantクライアントID)
OIDC_CODE_GRANT_CLIENT_SECRET=(OIDC用CodeGrantクライアントシークレット)
```

### 11. Webアプリケーションログイン
#### 認可サーバー
```
http://localhost.auth-app.sample.jp/login
```

##### 管理者
```
Email : manager@test.com
Password : password123
```

##### 一般ユーザー
```
Email : staff@test.com
Password : password123
```

#### クライアントアプリケーション
```
http://localhost.client-app.sample.jp/
```

##### 管理者
```
ユーザー名 : manager@test.com
PasswordGrant用パスワード : password123
```

##### 一般ユーザー
```
ユーザー名 : staff@test.com
PasswordGrant用パスワード : password123
```

### 12. 構築時メモ
構築時にインストールした主要パッケージ

#### 認可サーバー
Laravel, breeze, inertia, vue3
```shell
composer create-project laravel/laravel:^12 --prefer-dist .
composer require laravel/breeze:2.3.6
php artisan breeze:install vue
```

laravel/passport最新(少なくともver.13.0.2)はpassport:installでエラーになるため、バージョンを指定してインストールする。
```shell
composer require laravel/passport "12.4.2"
```

#### リソースサーバー
Laravel
```shell
composer create-project laravel/laravel:^12 --prefer-dist .
```

#### クライアントアプリケーション
Laravel
```shell
composer create-project laravel/laravel:^12 --prefer-dist .
```

blade+vue運用ではなくInertia+vue3運用
```shell
composer require inertiajs/inertia-laravel
```
```shell
npm install vue@3 @inertiajs/inertia @inertiajs/vue3 @vitejs/plugin-vue
```

#### LaravelPassport + OIDC の追加実装
LaravelPassport(OAuth2プロセス)にOIDCの認証機能を追加するため、独自に下記を実装。
- 認証サーバー
  - 認可(Code)リクエスト(/oauth/authorize)におけるscope=openidの場合の処理。(openidの許容とトークンへの登録)
  - 認証後にアクセストークン+IDトークンを応答
  - `/userinfo`APIを追加(認証済ユーザーの詳細情報を応答)
- クライアントアプリケーション
  - 認可サーバーへのCodeリクエストにscope=openidを追加
  - 認証後に応答されるIDトークンの検証(JWT署名を確認)
  - `/userinfo` へのアクセストークン付きリクエスト