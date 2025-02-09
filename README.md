# Sellasist - Konrad Ptak

## Treść zadania
REST API – Laravel, Napisz aplikację komunikującą się z przykładowym REST API: https://petstore.swagger.io/ umożliw dodawanie, pobieranie, edycję i usuwanie elementów w zasobie /pet. Od strony użytkownika zrób prosty interfejs z formularzami. Obsłuż błędy.

## Rozwiązanie

Dzień dobry, <br>
Realizacja zadania zajęła mi około 4.5 - 5.5h, w tym znajdowało się: <br>
* przygotowanie i postawienie projektu Laravel 11 wraz z Vue.js <br>
* napisanie  rozwiązania i testów jednostkowych <br>
* refaktoryzacja kodu <br>

Do realizacji zadania korzystałem z: <br>
* PHPStorm
* Postman
* dostarczony Swagger
* dokumentacji Laravela
* internetu
* copilota w IDE

Dodatkowym problemem nad którym straciłem trochę czasu było błędne działanie API: <br>
* https://petstore.swagger.io/v2/pet/findByStatus <br>

które mimo przyjmowania kilku statusów zwraca wyniki tylko dla jednego z nich. <br>
Zanim zwróciłem na to uwagę straciłem trochę czasu.<br>

Aby uruchomić projekt konieczne może być otwarciu dwóch konsol. <br>
W jednej z nich należy uruchomić **php artisan serve** w drugiej natomiast **npm run dev**.

