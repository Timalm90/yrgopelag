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
