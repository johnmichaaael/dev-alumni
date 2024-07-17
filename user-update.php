<?php
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: user-login.php");
    exit;
}

// Include config file
require_once "./db/config.php";

// Define variables and initialize with empty values
$last_name = $first_name = $middle_name = $email = "";
$last_name_err = $first_name_err = $middle_name_err = $email_err = "";

$form_submitted = false;

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate hidden input value
    $id = $_POST["id"];

    // Validate last name
    $input_last_name = trim($_POST["last_name"]);
    if (empty($input_last_name)) {
        $last_name_err = "Please enter a last name.";
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $input_last_name)) {
        $last_name_err = "Please enter a valid name.";
    } else {
        $last_name = $input_last_name;
    }

    // Validate first name
    $input_first_name = trim($_POST["first_name"]);
    if (empty($input_first_name)) {
        $first_name_err = "Please enter a first name.";
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $input_first_name)) {
        $first_name_err = "Please enter a valid name.";
    } else {
        $first_name = $input_first_name;
    }

    // Validate middle name
    $input_middle_name = trim($_POST["middle_name"]);
    if (empty($input_middle_name)) {
        $middle_name_err = "Please enter a middle name.";
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $input_middle_name)) {
        $middle_name_err = "Please enter a valid name.";
    } else {
        $middle_name = $input_middle_name;
    }

    // Validate email address
    $input_email = trim($_POST["email"]);
    if (empty($input_email)) {
        $email_err = "Please enter an email address.";
    } elseif (!filter_var($input_email, FILTER_VALIDATE_EMAIL)) {
        $email_err = "Please enter a valid email address.";
    } else {
        $email = $input_email;
    }

    // Check input errors before updating the database
    if (empty($last_name_err) && empty($first_name_err) && empty($middle_name_err) && empty($email_err)) {
        // Prepare an update statement
        $sql = "UPDATE alumni SET last_name=:last_name, first_name=:first_name, middle_name=:middle_name, email=:email WHERE id=:id";

        if ($stmt = $pdo->prepare($sql)) {
            // Bind parameters to the statement
            $stmt->bindParam(":last_name", $last_name);
            $stmt->bindParam(":first_name", $first_name);
            $stmt->bindParam(":middle_name", $middle_name);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":id", $id);

            // Attempt to execute the statement
            if ($stmt->execute()) {
                $form_submitted = true;
                // Optionally, you can redirect to a success page or reload the form
                // header("location: success.php");
                // exit();
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        // Close statement
        unset($stmt);
    }
    // Close connection
    unset($pdo);
} else {
    // Validate and fetch data when the page is loaded initially
    if (isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
        // Get URL parameter
        $id = trim($_GET["id"]);

        // Prepare a select statement
        $sql = "SELECT * FROM alumni WHERE id = :id";
        if ($stmt = $pdo->prepare($sql)) {
            // Bind parameters to the prepared statement
            $stmt->bindParam(":id", $param_id);

            // Set parameters
            $param_id = $id;

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                if ($stmt->rowCount() == 1) {
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    // Retrieve individual field values
                    $last_name = $row["last_name"];
                    $first_name = $row["first_name"];
                    $middle_name = $row["middle_name"];
                    $email = $row["email"];
                    // No need to fetch 'password_hash' field for security reasons
                } else {
                    // URL doesn't contain valid id. Redirect to error page
                    header("location: error.php");
                    exit();
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        // Close statement
        unset($stmt);
        // Close connection
        unset($pdo);
    } else {
        // URL doesn't contain id parameter. Redirect to error page
        header("location: error.php");
        exit();
    }
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Record</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        .wrapper{
            width: 600px;
            margin: 0 auto;
        }
        .toast-container {
            position: fixed;
            top: 25%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="mt-5">Update Record</h2>
                    <p>Please edit the input values and submit to update the employee record.</p>
                    <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
                        <div class="form-group">
                            <label>Last name</label>
                            <input type="text" name="last_name" class="form-control <?php echo (!empty($last_name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $last_name; ?>">
                            <span class="invalid-feedback"><?php echo $last_name_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>First name</label>
                            <input type="text" name="first_name" class="form-control <?php echo (!empty($first_name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $first_name; ?>">
                            <span class="invalid-feedback"><?php echo $first_name_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Middle name</label>
                            <input type="text" name="middle_name" class="form-control <?php echo (!empty($middle_name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $middle_name; ?>">
                            <span class="invalid-feedback"><?php echo $middle_name_err;?></span>
                        </div>
                        <div class="form-group">
                        <label for="exampleInputEmail1">Email address</label>
                            <input type="email" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email">
                            <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
                            <span class="invalid-feedback"><?php echo $email_err;?></span>
                         </div>

                        <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="user-dashboard.php" class="btn btn-secondary ml-2">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
    <!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<!-- Toast HTML -->
<div class="toast-container">
    <div id="successToast" class="toast text-bg-success" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <strong class="me-auto">Success</strong>
        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            Changes Saved!
        </div>
    </div>
</div>

<?php if ($form_submitted): ?>
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function () {
            var successToastEl = document.getElementById('successToast');
            var successToast = new bootstrap.Toast(successToastEl);
            successToast.show();
            document.getElementById('facultyForm').reset();
        });
    </script>
<?php endif; ?>

</body>
</html>