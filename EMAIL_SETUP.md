# Email notifications

Order and order-status emails connect directly to Gmail SMTP with PHP's built-in socket and OpenSSL functions. No third-party mailer package is used. PHP's standard `mail()` function is not used because its Windows SMTP configuration cannot authenticate to Gmail or negotiate Gmail's required STARTTLS connection.

In Windows, add these under **System Properties → Advanced → Environment Variables** (system variables are preferred), then restart Apache from the XAMPP Control Panel:

- `SMTP_HOST=smtp.gmail.com`
- `SMTP_PORT=587`
- `SMTP_USERNAME=codewithsheru@gmail.com` (optional; defaults to the admin notification address)
- `SMTP_APP_PASSWORD=your-16-character-google-app-password`
- `SMTP_FROM_EMAIL=your-sending-gmail-address@gmail.com` (optional; defaults to `SMTP_USERNAME`)
- `SMTP_FROM_NAME=Pure Oats with Nuts` (optional)

Create a Google App Password for the sending Gmail account with 2-Step Verification enabled. Do not use the normal Gmail password or put the App Password in source code. `codewithsheru@gmail.com` is the admin notification recipient and default sender; if this is not the Gmail account you control, set `SMTP_USERNAME` and `SMTP_FROM_EMAIL` to an account you can send from.

The PHP `openssl` extension must be enabled. For a fresh database, apply `database/migrations/20261007_add_customer_email.sql`; on the existing XAMPP database, run `php database\migrate_customer_email.php` to add the column/index only when missing.

After a customer enters their email at checkout:

- A new order notification is sent to the admin recipient.
- Every actual order status change sends the new status to the customer.
- Admin status-update toasts confirm whether the customer email was sent. A mail failure does not undo a saved order/status; details are written to the PHP error log.
