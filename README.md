## 商品管理システム

### 環境構築手順

* Gitクローン
* .env.example をコピーして .env を作成
* MySQLのデータベース作成（名前：item_management）
* Macの場合 .env の DB_PASSWORD を root に修正（Windowsは修正不要）

    ```INI
    DB_PASSWORD=root
    ```

* APP_KEY生成

    ```console
    php artisan key:generate
    ```

* Composerインストール

    ```console
    composer install
    ```

* フロント環境構築

    ```console
    npm ci
    npm run build
    ```

* マイグレーション

    ```console
    php artisan migrate
    ```

* 起動

    ```console
    php artisan serve
    ```
