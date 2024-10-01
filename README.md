# Coinsbuy API Integration Demo

This project demonstrates integration with the Coinsbuy API for creating deposits and payouts.

## Setup

1. Clone this repository.
2. Update `config.php` with your API credentials.
3. Ensure PHP is installed on your system.

## Usage

### Create a Deposit

Run:

```
php create_deposit.php
```

### Create a Payout

Run:

```
php create_payout.php
```

### Handling Callbacks

Place `callback.php` in a publicly accessible location and update the callback URL in your Coinsbuy account settings.

## Note

This is a demo project. In a production environment, implement proper error handling, logging, and security measures.
