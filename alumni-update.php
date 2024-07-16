<?php
// Include config file
require_once "./db/config.php";
 
// Define variables and initialize with empty values
$last_name = $first_name = $middle_name = $email = $password = $confirm_password = "";
$last_name_err = $first_name_err = $middle_name_err = $email_err = $password_err = $confirm_password_err = "";

$form_submitted = false;
 
// Processing form data when form is submitted
if(isset($_POST["id"]) && !empty($_POST["id"])){
    // Get hidden input value
    $id = $_POST["id"];
    
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
   
   // Validate confirm password
   if(empty(trim($_POST["confirm_password"]))){
       $confirm_password_err = "Please confirm password.";     
   } else{
       $confirm_password = trim($_POST["confirm_password"]);
       if(empty($password_err) && ($password != $confirm_password)){
           $confirm_password_err = "Password did not match.";
       }
   }

    // Check input errors before inserting in database
    if(empty($last_name_err) && empty($first_name_err) && empty($param_middle_name_name_err) && empty($email_err) && empty($password_err) && empty($confirm_password_err)){
        // Prepare an update statement
        $sql = "UPDATE alumni SET last_name=:last_name, first_name=:first_name, middle_name=:middle_name, email=:email WHERE id=:id";
 
        if($stmt = $pdo->prepare($sql)){
            
            // Set parameters
            $param_last_name = $last_name;
            $param_first_name = $first_name;
            $param_middle_name = $middle_name;
            $param_email = $email;
            $param_id = $id;
            
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":last_name", $param_last_name);
            $stmt->bindParam(":first_name", $param_first_name);
            $stmt->bindParam(":middle_name", $param_middle_name);
            $stmt->bindParam(":email", $param_email);
            $stmt->bindParam(":id", $param_id);

            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Records updated successfully. Redirect to landing page
                //header("location: alumni-list.php");
                //exit();

                $form_submitted = true;

                // Reload data to forms
                if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
                    // Get URL parameter
                    $id =  trim($_GET["id"]);

                    // Prepare a select statement
                    $sql = "SELECT * FROM alumni WHERE id = :id";
                    if($stmt = $pdo->prepare($sql)){

                         // Set parameters
                         $param_id = $id;

                        // Bind variables to the prepared statement as parameters
                        $stmt->bindParam(":id", $param_id);

                        // Attempt to execute the prepared statement
                        if($stmt->execute()){
                            if($stmt->rowCount() == 1){

                                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                                // Retrieve individual field value
                                $last_name = $row["last_name"];
                                $first_name = $row["first_name"];
                                $middle_name = $row["middle_name"];
                                $email = $row["email"];
                                $password = $row["password"];

                            } else{
                                // URL doesn't contain valid id. Redirect to error page
                                header("location: error.php");
                                exit();
                            }

                        } else{
                            echo "Oops! Something went wrong. Please try again later.";
                        }
                    }

                    // Close statement
                    unset($stmt);

                    // Close connection
                    unset($pdo);
                }  else{
                    // URL doesn't contain id parameter. Redirect to error page
                    header("location: error.php");
                    exit();
                }


            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
         
        // Close statement
        unset($stmt);
    }
    
    // Close connection
    unset($pdo);
} else{
    // Check existence of id parameter before processing further
    if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
        // Get URL parameter
        $id =  trim($_GET["id"]);
        
        // Prepare a select statement
        $sql = "SELECT * FROM alumni WHERE id = :id";
        if($stmt = $pdo->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(":id", $param_id);
            
            // Set parameters
            $param_id = $id;
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                if($stmt->rowCount() == 1){
                    /* Fetch result row as an associative array. Since the result set
                    contains only one row, we don't need to use while loop */
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                
                    // Retrieve individual field value
                    $last_name = $row["last_name"];
                    $first_name = $row["first_name"];
                    $middle_name = $row["middle_name"];
                    $email = $row["email"];
                    $password = $row["password"];
                } else{
                    // URL doesn't contain valid id. Redirect to error page
                    header("location: error.php");
                    exit();
                }
                
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        
        // Close statement
        unset($stmt);
        
        // Close connection
        unset($pdo);
    }  else{
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
                        <div class="form-group">
                            <label for="exampleInputPassword1">Password</label>
                            <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" id="exampleInputPassword1" placeholder="Password">
                            <span class="invalid-feedback"><?php echo $password_err;?></span>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1">Confirm Password</label>
                            <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password; ?>" id="exampleInputPassword1" placeholder="Enter password again">
                            <span class="invalid-feedback"><?php echo $confirm_password_err;?></span>
                        </div>

                        <input type="hidden" name="id" value="<?php echo $id; ?>"/>
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