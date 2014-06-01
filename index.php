<?php
/**
 * Copyright (c) 2014 Adam L. Englander
 *
 * Permission is hereby granted, free of charge, to any person obtaining a
 * copy of this software and associated documentation files (the "Software"),
 * to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense,
 * and/or sell copies of the Software, and to permit persons to whom the Software
 * is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
 * INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A
 * PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF
 * CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE
 * OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

// Mostly for the windows folks.  Load the pdo and pdo_sqlite extensions if they are not loaded
foreach (array('pdo', 'pdo_sqlite') as $extension) {
    if (!extension_loaded ($extension)) dl($extension);
}

// The default timezone should always be set when using dates and times
date_default_timezone_set('America/Los_Angeles');

// Turn off error reporting to hide it from the users.  Hiding errors from users prevents exposing the internal
// workings of your application and the possibility of displaying secret or private data in the error.
error_reporting(0);

// Initialize our variables used in the template
$errors = array();
$first = null;
$last = null;
$result = null;

// Add a generic try catch around the entire PHP code section to ensure we catch any errors
try {
    // Connect to database with PDO
    $pdo = new PDO('sqlite:' . __DIR__ . '/registry.sq3');

    // Set the
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_POST) {

        if (empty($_POST['first'])) {
            // If the required field is empty add an error
            $errors[] = 'First is required to register';
        } else {
            // Use urldecode() as POST data is URL encoded @see http://en.wikipedia.org/wiki/POST_(HTTP)#Use_for_submitting_web_forms
            $first = urldecode($_POST['first']);
        }

        if (empty($_POST['last'])) {
            // If the required field is empty add an error
            $errors[] = 'Last is required to register';
        } else {
            // Use urldecode() as POST data is URL encoded @see http://en.wikipedia.org/wiki/POST_(HTTP)#Use_for_submitting_web_forms
            $last = urldecode($_POST['last']);
        }
        $timestamp = time();

        // If the form post had no errors then we will store the data in the database
        if (!$errors) {

            // Use try/catch to catch any errors in saving the registration
            try {
                // Use a prepared statement to protect against SQL injection
                $stmt = $pdo->prepare('INSERT INTO registrants (first, last, registered_timestamp) VALUES (:first, :last, :timestamp)');

                // Binding the data to the statement will escape the data before sending to the database in order to
                // prevent SQL injection
                $stmt->bindValue('first', $first);
                $stmt->bindValue('last', $last);
                $stmt->bindValue('timestamp', $timestamp, PDO::PARAM_INT);
                $result = $stmt->execute();

                // With a successful registration, clear the form values to allow for a new registration
                $first = null;
                $last = null;
            } catch (PDOException $e) {
                $message = sprintf(
                    'Unable to save registration: %s: Registrant info: [first: %s, last: %s, registered_timestamp: %s]',
                    $e->getMessage(),
                    $first,
                    $last,
                    $timestamp
                );

                // Throw new Exception with descriptive error message so the default handler will properly log an display the error
                throw new Exception($message, 0, $e); // Set the caught exception as the previous
            }
        }
    }

    // Get the registrants
    try {
        $query = 'SELECT * FROM registrants ORDER BY registered_timestamp DESC';
        $result = $pdo->query($query);
    } catch (PDOException $e) {
        $message = sprintf(
            'Unable to retrieve registrants: %s',
            $e->getMessage()
        );
        // Throw new Exception with descriptive error message so the default handler will properly log an display the error
        throw new Exception(
            $message,
            0,
            $e // Set the caught exception as the previous
        );
    }
} catch (Exception $e) {
    // The default/master exception handler will log the error and display to the user

    // Generate a Unique ID to identify this error
    $errorId = uniqid('ERROR-');

    // Add a nondescript error to the errors to show the user and include the error ID for reference
    $errors[] = sprintf('An application error occurred [%s]', $errorId);
    error_log(sprintf('%s: %s', $errorId, $e->getMessage()));
}

// All logic was handled above in the PHP section.  This separates the logic from the view and makes the HTML
// section a bit more readable.  It also allows for separating the controller from the view at a later time.
?>
<!DOCTYPE html>
<html>
<body>
<div>
    <h1>Registration Application</h1>

    <p>This is a basic registration application</p>
</div>
<div>
    <h2>Register</h2>
    <?php if ($errors): // If there are errors, display them to the user ?>
        <div class="errors">
            <h3 class="error-heading">Errors were encountered wth your registration</h3>
            <ul>
                <?php foreach ($errors as $error): // Loop through the errors array ?>
                    <li><?php echo $error; // Display an individual error ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <form method="POST">
        <div>
            <label for="first">First: </label>
            <input id="first" name="first" value="<?php echo htmlentities($first); // Always protect against HTML injection with htmlentities() ?>">

            <label for="last">Last: </label>
            <input id="last" name="last" value="<?php echo htmlentities($last); // Always protect against HTML injection with htmlentities() ?>">

            <input type="submit" value="Register">
        </div>
    </form>
</div>
<div class="registration-list">
    <h2>Registrations</h2>
    <table>
        <thead>
        <tr>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Date</th>
            <th>Time</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($result): // Check to make shre you have a valid result object before iterating over it ?>
            <?php while ($registrant = $result->fetch()): // Fetch the next record of the result ?>
                <tr>
                    <td><?php echo htmlentities($registrant['first']); // Always protect against HTML injection with htmlentities() ?>
                    <td><?php echo htmlentities($registrant['last']); // Always protect against HTML injection with htmlentities() ?>
                    <td><?php echo date('M j, Y', $registrant['registered_timestamp']); // Format the timestamp as a date ?>
                    <td><?php echo date('g:i:s A', $registrant['registered_timestamp']); // Format the timestamp as a time ?>
                </tr>
            <?php endwhile; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
