# Store setup

## Order email delivery

Contact form notifications, order confirmations, and admin status updates use PHPMailer over SMTP. PHPMailer is installed from the official [PHPMailer GitHub repository](https://github.com/PHPMailer/PHPMailer) using Composer. Run `composer install` after cloning or copying the project.

Configure these environment variables for the PHP/Apache process, or use the ignored `config.local.php` file for local development. Never commit SMTP credentials:

- `MAIL_HOST`: SMTP server hostname
- `MAIL_PORT`: usually `587` for STARTTLS or `465` for implicit TLS
- `MAIL_ENCRYPTION`: `tls` (STARTTLS), `ssl` (implicit TLS), or `none` (only for a trusted local relay without credentials)
- `MAIL_USERNAME` and `MAIL_PASSWORD`: set both for authenticated SMTP
- `MAIL_FROM_ADDRESS`: verified sender address
- `MAIL_FROM_NAME`: optional sender label (defaults to `VEBRA`)
- `MAIL_CONTACT_TO`: address that receives contact form submissions
- `APP_URL`: public website base URL, used for the order-lookup link in emails

For local XAMPP, you can instead put these settings in a root-level `config.local.php` file that returns an array with the same keys. This file is ignored by Git. Set `MAIL_FROM_ADDRESS` to the authenticated SMTP account or another sender verified by your provider; `MAIL_CONTACT_TO` is the inbox that receives contact messages.

After changing Apache environment settings, restart Apache. Contact messages and orders are saved even when email delivery is unavailable; the pages report notification failures, which are also written to the PHP error log. The submitter's address is set as Reply-To on contact notifications. Never use a visitor-provided address as the sender.

## Store policies and artwork

The policy copy at `store-policy.php` is a neutral draft for owner review, not legal advice. Confirm business practices, contact details, consumer rights, and terms for each sales region before launch. The catalog artwork should also be replaced with images that correctly depict each product.
