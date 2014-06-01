# Starter App

## Overview

This starter app is designed to show beginning PHP developers how to:

* Interact with the user via forms

    * URL decoding posted form data submitted by users
    * Form data validation
    * Using built in methods to protect against [HTML injection](https://www.owasp.org/index.php/HTML_Injection) and
     [Cross-site scripting](https://www.owasp.org/index.php/Cross-site_Scripting_\(XSS\)) protection

* Interact with a database to store and retrieve data

    * Prepare statements to protect against SQL inject attacks
    * Create a result cursor and iterate over it to display data on a page

* Handle errors in the application

    * Use try/catch blocks to handle errors gracefully
    * Use nested try/catch blocks and trow statements to manage error reporting and logging in one place
    * Display nondescript but easily trackable errors to the users to protect against displaying critical data in error messages.
    * Log better information for tracking bugs that create errors

* Use the built in PHP web server to test their applications locally without having to install a web server.

## Pre-Requisites

*   PHP 5.4 - Required for the internal web server
*   PHP PDO Extension - Required for database interaction - [Installation instructions](http://us2.php.net/manual/en/pdo.installation.php)

## Getting Started

### Start the Application

Run the built in PHP web server by executing the following command in a shell from the directory containing the example:

    php -S localhost:8080

If the PHP executable cannot be found in the path, the command will need to preceded with the correct path.  A windows example:

    C:\php\php -S localhost:8080

Alternate addresses or ports can be used if necessary.  Refer to
[the PHP documentation](http://www.php.net/manual/en/features.commandline.webserver.php) for more information on
available options.

### Access the Application

Load the application in a browser at (http://localhost:8080). Adjust the URL if you changed the address or port.

The page will be displaying application errors.  This is to be expected as the database has not been created.  The
important thing to note here is that the application did not die or return a 500 error page.  It properly handled the
application error, logged it to the logs and displayed a nondescript error message with a unique ID.  Looking at the
shell in which the PHP web server was started should display the actual error message from the error with the same
error ID.

### Create the Database Tables

In order to use this application, the database must be prepared with a valid schema.
Execute the following command at the command prompt from the directory containing the example:

    php create_db.php

### Access the Working Application

The application should now be working as expected.  Registrations can be added and displayed on the page.

## Look At the Code

Now that the application is working as expected, dig into the code.  All of the code is highly commented to explain
what is happening and why.  Code examples are often the best way to learn how to code.

## Help Improve the App

All code and documentation can be better.  Submit an issue or a pull request to help improve this example application.
