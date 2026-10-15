# Home Haven Property Listings

A beginner-friendly property listing project built with PHP, MySQL, HTML, and CSS for XAMPP. People can create an account, post a property with one photo, browse listings, and try a simulated purchase. No real payment is collected.

## Run with XAMPP on macOS

1. Start **Apache** and **MySQL** in the XAMPP Manager.
2. Open `http://localhost/phpmyadmin` and import `database.sql`.
3. Copy the project into XAMPP's `htdocs` folder. Apache may not be able to follow a symbolic link into your private `Documents` folder, so copy the files instead. If you cloned it into `~/Documents/phpcapstone`, run this in Terminal:

   ```sh
   if [ -L /Applications/XAMPP/xamppfiles/htdocs/phpcapstone1 ]; then
     rm /Applications/XAMPP/xamppfiles/htdocs/phpcapstone1
   fi
   mkdir -p /Applications/XAMPP/xamppfiles/htdocs/phpcapstone1
   rsync -a --exclude='.git/' "$HOME/Documents/phpcapstone/" /Applications/XAMPP/xamppfiles/htdocs/phpcapstone1/
   chmod 777 /Applications/XAMPP/xamppfiles/htdocs/phpcapstone1/uploads
   ```

   Run the `rsync` command again after changing the project files so the XAMPP copy is updated. The `chmod` line lets XAMPP save uploaded photos during local development.
4. Visit `http://localhost/phpcapstone1`.

The default database settings in `includes/db.php` match this computer's XAMPP setup: host `127.0.0.1`, port `3307`, user `root`, and an empty password. If your settings are different, set the `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, or `DB_PASSWORD` environment values before starting Apache. The upload limit is 2 MB to match the XAMPP PHP setting.

## Included pages

- `index.php`: browse available properties
- `register.php`, `login.php`, `logout.php`: create and manage a session
- `add-property.php`: post a listing with one image
- `property.php`: see listing details
- `checkout.php`: confirm a simulated purchase
- `my-listings.php`: view your own available and sold listings

Uploaded property photos are stored in `uploads/` and are not included in Git.
