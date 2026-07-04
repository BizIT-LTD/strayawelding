# Straya Mobile Welding Website

Static HTML/CSS/JS website for Straya Mobile Welding, with a PHP contact form using PHPMailer and Gmail SMTP.

## Pages

- `index.html`
- `about.html`
- `services.html`
- `portfolio.html`
- `contact.html`

## Editing

- Main styles are in `css/styles.css`.
- Mobile menu and contact status messages are in `js/main.js`.
- Public images are stored in `assets/images/`.
- Original logo files are stored in `assets/images/logo/`.
- Portfolio project photos are stored in `assets/images/portfolio/`.
- Collected source notes are in `source-content/content-summary.md`.

## Contact Form Setup on Hostinger

1. Install PHPMailer on Hostinger, preferably with Composer:
   ```bash
   composer require phpmailer/phpmailer
   ```
2. Copy `config.example.php` to `config.php` on Hostinger.
3. Edit `config.php` and add the real Gmail App Password.
4. Do not commit `config.php`. It is ignored by `.gitignore`.

Gmail requirements:
- Enable 2-Step Verification on the Gmail account.
- Create a Gmail App Password for the website.
- Use `straya.welding@gmail.com` as the SMTP username.
- SMTP host: `smtp.gmail.com`
- SMTP port: `587`
- SMTP security: STARTTLS

The form sends from `straya.welding@gmail.com` to `info@strayawelding.com.au`, with Reply-To set to the visitor email.

## Deployment

Push these files to GitHub and connect the repository to Hostinger. Upload or create `config.php` only on Hostinger after deployment. If Composer is not available on the hosting plan, upload the PHPMailer source folder as `PHPMailer/` beside `contact.php`.

## Before Going Live

- Add the real Gmail App Password to Hostinger `config.php`.
- Confirm PHPMailer is installed through Composer or uploaded as `PHPMailer/`.
- Submit a test enquiry and confirm delivery to `info@strayawelding.com.au`.
