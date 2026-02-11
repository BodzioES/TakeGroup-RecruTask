# API Movie & Series Manager
A short recruitment project implementing a system for synchronizing and displaying movies and TV series from an external TMDB API. The system supports full multilingual support (PL, EN, DE) and dynamic view switching.

## Environment and Technology
* **Backend:** PHP 8.2 + Laravel 11
* **Frontend:** Livewire 3
* **Database:** MySQL 8.0
* **Containerization:** Docker + Docker Compose
* **Stylization:** Tailwind CSS
* **Data:** The Movie Database (TMDB) API

---
### I recommend the Postman application to test endpoints, it makes testing much easier
## Project launch

1. **Environment configuration:**
    - Copy file `.env.example` to `.env`.
    - In the file `.env` complete the key `TMDB_API_KEY="your_key_here"`.
    - Make sure that `DB_HOST=db` and `DB_PASSWORD=` (I left it blank to make it easier to check)

2. **Construction and launch of containers:**
   ```bash
   docker-compose build
   docker-compose up -d

3. **Installation and migrations:**
    ```bash
    docker-compose exec app composer install
    docker-compose exec app php artisan migrate
   
4. **Data import from API:**
    ```bash
    docker-compose exec app php artisan tmdb:import

### After starting the containers, the frontend is available at:
### http://localhost:8080

5. **API endpoints:**

    All endpoints accept the Accept-Language: pl|en|de header. If the header is missing, the default language returned is English (en).

    | Method | Endpoint | Description |
    | :--- | :--- | :--- |
    | `GET` | `/api/movies` | Returns a paginated list of 50 movies with translations and genres. |
    | `GET` | `/api/series` | Returns a paginated list of 10 series with translations and genres. |
    | `GET` | `/api/genres` | Returns the full list of available genres in the selected language. |
