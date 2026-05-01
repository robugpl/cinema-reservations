# Ubiquitous Language (Cinema Reservations Domain)

This document defines the core business terms (Ubiquitous Language) used across the project, serving as the single source of truth for both domain experts and developers.

## Shared Concepts
* **Money**: Represents a monetary value. It is strictly stored in the minor unit (e.g., cents or grosze) to prevent floating-point precision errors during financial calculations.
* **Email**: A value object representing a valid electronic mail address, used to identify a guest customer and for communication purposes (like sending tickets or reservation confirmations).

## Reservations Context (`App\Reservations\Domain`)
* **Movie**: A cinematic production characterized by its title, director, and total duration.
* **Movie Duration**: The precise length of a movie, encapsulating the conversion between different time units (seconds, minutes).
* **Screening Room**: A physical hall where movies are shown, containing a specific layout of rows and seats.
* **Row**: A physical line of adjacent seats within a screening room.
* **Seat**: A specific, uniquely identified sitting place within a screening room, usually denoted by a label (e.g., "A1").
* **Screening**: A scheduled event where a specific `Movie` is shown in a designated `Screening Room` at a precise date and time. It acts as the aggregate root responsible for managing reservations and enforcing business rules (like preventing double-booking of seats).
* **Reservation**: A temporary hold on a specific `Seat` for a particular `Screening` made by a customer (identified by an `Email`). Every reservation has an explicit expiration date by which it must be paid, otherwise it is automatically canceled.
* **Reservation Status**: The current state of a reservation lifecycle:
    * **Pending**: The reservation is created but awaiting payment.
    * **Redeemed**: The reservation has been paid for and successfully converted into a ticket.
    * **Expired**: The reservation was not paid within the required timeframe and has been released back to the pool.
    * **Canceled**: The reservation was explicitly aborted by the user or the system.

## Sales Context (`App\Sales\Domain`)
* **Ticket**: A proof of purchase granting the customer the right to occupy a specific `Seat` during a particular `Screening`. It holds its own monetary value (`Price`).
* **Order**: A commercial transaction grouping one or multiple `Tickets` purchased by a customer (`Email`). It calculates and manages the `Total` amount due.
