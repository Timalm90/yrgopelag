# School Project: Yrgopelag

This is a school project in which I have created an interactive web application for booking hotel rooms or day passes at my fictional hotel Yoshi's Resort on Starlight Island. The application features a Super Mario design with a playful universe filled with familiar characters and themes.

## Project Highlights

- **Interactive calendar to select arrival and departure dates**
  Users can click on a date for arrival and then a date for departure and the selected room is filled in automatically.

- **Toggle bars for dynamic content switching**
  Used in both the room presentation and the booking form.

- **Dynamic total price calculation with real-time discount updates** based on room selection, number of nights and additional features.

- **Playful design with Super Mario characters** for a fun and engaging user experience.

- **Form validation and error handling** for missing or incorrect input.

- **Confirmation modal** displayed after a successful booking.

- **Master code-protected administrative actions**
  A master code is required for creating admin accounts, changing passwords and performing actions that affects financials.

## Technologies / Stack

- PHP for backend and PDO for database access
- SQLite3
- HTML, CSS, JavaScript for frontend
- Composer packages:
- `vlucas/phpdotenv` for environment variables
- `guzzlehttp/guzzle` for API requests

## Installation

Clone the repository:
git clone https://github.com/ESengenbjerg/yrgopelag.git

## Future improvements

- Allow administrators to add rooms and deactivate rooms
- Allow administrators to remove or deactivate admin users
- Allow administrators to change offers and discounts
- Allow administrators to update hotel star ratings
- Display occupancy rate per room



Review from Tim :

1. booking.php:83-86 -
It’s possible to book arrival and departure the same day, resulting in free stay (as long as you book a feature). The date isn't visually booked in the calendar, but the user cannot book this date anymore. I also had the price be -1

"Dear Tim,
Thank you for choosing Yoshi's Resort on Starlight Island. We're looking forward to your visit!
Your visit is registered for 2026-01-04 - 2026-01-04. Check-in: 15:00 Checkout: 11:00
Your room: Luxury
Included features:
* 		Pool
Total price: -1 credits"



2.  Nav.php
I don’t really see the point of the admin link up top. When not logged in it just leads to the login.php page. Possible to just keep the login page and have the admin-page invisible for the visitor?

3. I find it hard to see available rooms, since the input field isn’t beside the calendar.

4. Input radio has no visual link to roomWrapper. Would be nice if the room type updated when selecting one of the options.

5. What’s Day pass? Would be nice to have some info regarding what it is. 

6. No visual calendar for day pass, making it hard to book

7. On bad request:  Forms are reset, making it so that the user have to rewrite everything into forms. Would be nice with cashed data on error.
 
