<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

## How to run Project

1. **Install Dependencies**: 
    ```bash
    composer update
    ```

2. **Install Dev Container**: (Select only mysql)
    ```bash
    php artisan sail:install --devcontainer
    ```

3. **Install Extentions**: Install the [Remote - Containers](https://marketplace.visualstudio.com/items?itemName=ms-vscode-remote.remote-containers) and [Docker](https://marketplace.visualstudio.com/items?itemName=ms-azuretools.vscode-docker) and [Dev Container](https://marketplace.visualstudio.com/items?itemName=ms-vscode-remote.remote-containers) extensions in VS Code.

4. **Open VScode**: Open Source Code in the project folder.
    ```bash
    code .
    ```

5. **Reopen in Container**: Press `F1`, type `Reopen in Container`, and select it. VS Code will build the container and open your project inside it.

6. **Install Dependencies**: Once inside the container, install the project dependencies.
    ```bash
    composer install
    ```

7. **Generate Key**: Generate the Laravel application key.
    ```bash
    php artisan key:generate
    ```

8. **Run database.sql**: Run the database.sql file to mysql.
    ```bash
    mysql -h <mysql-host> -u root -p -D <database> < database.sql
    ```

9. **Run Laravel Server**: Once inside the container, run the Laravel development server.
    ```bash
    php artisan serve --host=0.0.0.0 --port=8000
    ```

10. **Access the Project**: Open your browser and navigate to `http://localhost:8000` to see your Laravel project running.

That's it! You now have your Laravel project running inside a development container.