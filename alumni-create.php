<?php
// Include config file
require_once "./db/config.php";

// Define variables and initialize with empty values
$last_name = $first_name = $middle_name = $email = $password = "";
$last_name_err = $first_name_err = $middle_name_err = $email_err = $password_err = "";

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate last name
    $input_last_name = trim($_POST["last_name"]);
    if(empty($input_last_name)){
        $last_name_err = "Please enter a last name.";
    } elseif(!preg_match("/^[a-zA-Z\s]+$/", $input_last_name)){
        $last_name_err = "Please enter a valid name.";
    } else{
        $last_name = $input_last_name;
    }

    // Validate first name
    $input_first_name = trim($_POST["first_name"]);
    if(empty($input_first_name)){
        $first_name_err = "Please enter a first name.";
    } elseif(!preg_match("/^[a-zA-Z\s]+$/", $input_first_name)){
        $first_name_err = "Please enter a valid name.";
    } else{
        $first_name = $input_first_name;
    }

    // Validate middle name (optional)
    $input_middle_name = trim($_POST["middle_name"]); // Corrected here
    if(!empty($input_middle_name) &&!preg_match("/^[a-zA-Z\s]+$/", $input_middle_name)){
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

        if($stmt = $pdo->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":last_name", $param_last_name);
            $stmt->bindParam(":first_name", $param_first_name);
            $stmt->bindParam(":middle_name", $param_middle_name);
            $stmt->bindParam(":email", $param_email);
            $stmt->bindParam(":password", $param_password);

            // Set parameters
            $param_last_name = $last_name;
            $param_first_name = $first_name;
            $param_middle_name = $middle_name;
            $param_email = $email;
            $param_password = $password;

            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Records created successfully. Redirect to landing page
                header("location: alumni-list.php");
                exit();
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
    <style>
        .wrapper{
            width: 600px;
            margin: 0 auto;
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
                            <input type="text" name="last_name" class="form-control <?php echo (!empty($first_name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $last_name; ?>">
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
                            <input type="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>" id="exampleInputEmail1" aria-describedby="emailHelp" placeholder="Enter email">
                            <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>
                            <span class="invalid-feedback"><?php echo $email_err;?></span>
                         </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1">Password</label>
                            <input type="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>" id="exampleInputPassword1" placeholder="Password">
                            <span class="invalid-feedback"><?php echo $password_err;?></span>
                        </div>

                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="alumni-list.php" class="btn btn-secondary ml-2">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>