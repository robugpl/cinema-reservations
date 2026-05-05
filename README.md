# Cinema Reservations System

This is a Domain-Driven Design (DDD) implementation of a Cinema Reservation system built with PHP 8.4, Symfony 7, and Doctrine ORM. 
The architecture clearly separates the domain logic (Reservations and Sales modules) from the framework infrastructure.

## Features

* **Rich Domain Model**: Implements Aggregates (`Screening`, `Reservation`, `Order`), Value Objects (`SeatId`, `MovieDuration`, `Money`), and custom exceptions.
* **Application Layer**: Business use cases are encapsulated in Application Services / Command Handlers.
* **Persistence**: Custom Doctrine DBAL Types to accurately persist Domain Value Objects without leaking database structure into the domain layer. 
* **Interactive CLI**: Interactive Symfony Console commands to make reservations and purchase tickets.
* **Testing**: Comprehensive Unit and Integration test coverage using PHPUnit.

## Prerequisites

* PHP >= 8.4
* Composer
* SQLite (configured as default in `doctrine.yaml`)

## Setup Instructions

1. **Install Dependencies**
   Run the following command to install required PHP packages:
   ```bash
   composer install
   ```

2. **Prepare the Database**
   Run the following command to update your database schema and load the initial fixtures (seeding Movies, Rooms, and Screenings):
   ```bash
   bin/console doctrine:schema:update --force
   bin/console doctrine:fixtures:load -n
   ```

## How to Run the Program

The application relies on an interactive CLI to demonstrate the flow.

### 1. Make a Reservation

To reserve one or multiple seats for a movie screening, run:
```bash
bin/console app:reservations:make
```
Follow the interactive prompts:
* Select a movie by typing its corresponding number.
* Select a screening date by typing its number.
* Select your seats by typing the numbers corresponding to the `[Row,Seat]` coordinates (e.g. `0, 1` to reserve the first two seats).
* Enter your e-mail address.

The console will output your **Reservation ID** upon success.

### 2. Pick Up a Reservation

To finalize your reservation, convert it to an Order, and generate Tickets, run:
```bash
bin/console app:sales:pickup
```
* Enter the **Reservation ID** generated from the previous step.
* The system will update the reservation status to `REDEEMED`, create `Ticket` entities for each seat, and compute the total `Order` price. 
* Your newly generated Ticket IDs will be displayed on the screen.

## Running Tests

To run the unit and integration tests:
```bash
vendor/bin/phpunit
```

All integration tests automatically create and clean up a dedicated test SQLite database (`var/test.db`).
