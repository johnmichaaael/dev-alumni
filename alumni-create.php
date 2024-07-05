<?php
// Include config file
require_once "./db/config.php";

// Flag to check if the form was submitted successfully
$form_submitted = false;
$duplicate_record = false;

// Define variables and initialize with empty values
$last_name = $first_name = $middle_name = $email = $password = "";
$last_name_err = $first_name_err = $middle_name_err = $email_err = $password_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate last name
    $input_last_name = trim($_POST["last_name"]);
    if(empty($input_last_name)){
        $name_err = "Please enter a last name.";
    } elseif(!filter_var($input_last_name, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $last_name_err = "Please enter a valid name.";
    } else{
        $last_name = $input_last_name;
    }

    // Validate first name
    $input_first_name = trim($_POST["first_name"]);
    if(empty($input_first_name)){
        $first_name_err = "Please enter a first name.";
    } elseif(!filter_var($input_first_name, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $first_name_err = "Please enter a valid name.";

    } else{
        $first_name = $input_first_name;
    }

    // Validate middle name
    $input_middle_name = trim($_POST["middle_name"]);
    if(empty($input_middle_name)){
        $middle_name_err = "Please enter a middle name.";
    } elseif(!filter_var($input_middle_name, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $middle_name_err = "Please enter a valid name.";
    } else{
        $middle_name = $input_middle_name;
    }

    // Validate email address
    $input_email = trim($_POST["email"]); // Corrected here
    if(empty($input_email)){
        $email_err = "Please enter an email address.";
    } elseif(!filter_var($input_email, FILTER_VALIDATE_EMAIL)){
        $email_err = "Please enter a valid email address.";
    } else{
        $email = $input_email;
    }

    // Validate password
    $input_password = trim($_POST["password"]); // Corrected here
    if(empty($input_password)){
        $password_err = "Please enter a password.";
    } elseif(strlen($input_password) < 6){
        $password_err = "Password must have at least 6 characters.";
    } else{
        $password = $input_password;
    }

    // Check input errors before inserting in database
    if(empty($last_name_err) && empty($first_name_err) && empty($middle_name_err) && empty($email_err) && empty($password_err)){
        // Prepare an insert statement
        $sql = "INSERT INTO alumni (last_name, first_name, middle_name, email, password) VALUES (:last_name, :first_name, :middle_name, :email, :password)";
        
        // Check if the record already exists
        $sql = "SELECT COUNT(*) FROM alumni WHERE last_name =:last_name AND first_name =:first_name AND middle_name =:middle_name AND email=:email";

        if($stmt = $pdo->prepare($sql)){

            // Set parameters
            $param_last_name = $last_name;
            $param_first_name = $first_name;
            $param_middle_name = $middle_name;
            $param_email = $email;

             // Bind variables to the prepared statement as parameters
             $stmt->bindParam(":last_name", $param_last_name);
             $stmt->bindParam(":first_name", $param_first_name);
             $stmt->bindParam(":middle_name", $param_middle_name);
             $stmt->bindParam(":email", $param_email);

            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Records created successfully. Redirect to landing page
                //header("location: alumni-list.php");
                //exit();

                if ($stmt->fetchColumn() > 0) {
                    // Duplicate record found
                    $duplicate_record = true;
                } else {

                    $sql = "INSERT INTO alumni (last_name, first_name, middle_name, email, password) VALUES (:last_name, :first_name, :middle_name, :email, :password)";

                    if ($stmt = $pdo->prepare($sql)) {
                        // Set parameters
                        $param_last_name = $last_name;
                        $param_first_name = $first_name;
                        $param_middle_name = $middle_name;
                        $param_email = $email;
                        $param_password = $password;

                        // Bind variables to the prepared statement as parameters
                        $stmt->bindParam(":last_name", $param_last_name);
                        $stmt->bindParam(":first_name", $param_first_name);
                        $stmt->bindParam(":middle_name", $param_middle_name);
                        $stmt->bindParam(":email", $param_email);
                        $stmt->bindParam(":password", $param_password);

                        // Attempt to execute the prepared statement
                        if ($stmt->execute()) {
                            // Set the form submission flag to true
                            $form_submitted = true;
                            $last_name = $first_name = $middle_name = $email = $password = "";
                        } else {
                            echo "Oops! Something went wrong. Please try again later.";
                        }
                    }    
                }

            } else{
                echo "Oops Something went wrong. Please try again later.";
            }
        }
         
        // Close statement
        unset($stmt);
    }
    
    // Close connection
    unset($pdo);
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Record</title>
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
                    <h2 class="mt-5">Create Record</h2>
                    <p>Please fill this form and submit to add employee record to the database.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="form-group">
                            <label>Last name</label>
                            <input type="text" name="last_name" class="form-control <?php echo (!empty($first_name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $last_name; ?>" placeholder="Enter last name">
                            <span class="invalid-feedback"><?php echo $last_name_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>First name</label>
                            <input type="text" name="first_name" class="form-control <?php echo (!empty($first_name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $first_name; ?>" placeholder="Enter first name">
                            <span class="invalid-feedback"><?php echo $first_name_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Middle name</label>
                            <input type="text" name="middle_name" class="form-control <?php echo (!empty($middle_name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $middle_name; ?>" placeholder="Enter middle name">
                            <span class="invalid-feedback"><?php echo $middle_name_err;?></span>
                        </div>
                        <div class="form-group">
                        <label for="exampleInputEmail1">Email address</label>
                            <input type="email" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email">
                            <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
                            <span class="invalid-feedback"><?php echo $email_err;?></span>
                         </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1">Password</label>
                            <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>" id="exampleInputPassword1" placeholder=" Enter password">
                            <span class="invalid-feedback"><?php echo $password_err;?></span>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1">Confirm Password</label>
                            <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>" id="exampleInputPassword1" placeholder="Enter password again">
                            <span class="invalid-feedback"><?php echo $password_err;?></span>
                        </div>

                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="alumni-list.php" class="btn btn-secondary ml-2">Cancel</a>
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
       New record added successfully!
       </div>
   </div>

   <div id="duplicateToast" class="toast text-bg-danger" role="alert" aria-live="assertive" aria-atomic="true">
       <div class="toast-header">
           <strong class="me-auto">Duplicate</strong>
           <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
       </div>
       <div class="toast-body">
           Duplicate record found. No new record added.
       </div>
   </div>
</div>

<!-- Trigger Toast JS -->
<?php if ($form_submitted): ?>
   <script type="text/javascript">
       var successToastEl = document.getElementById('successToast');
       var successToast = new bootstrap.Toast(successToastEl);
       successToast.show();
       document.getElementById('facultyForm').reset();
   </script>
<?php elseif ($duplicate_record): ?>
   <script type="text/javascript">
       var duplicateToastEl = document.getElementById('duplicateToast');
       var duplicateToast = new bootstrap.Toast(duplicateToastEl);
       duplicateToast.show();
   </script>
<?php endif; ?>
</body>
</html>