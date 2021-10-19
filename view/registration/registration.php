<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">

    <!-- CSS Independent -->
    <link rel="stylesheet" href="../../public/css/main.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/intlTelInput.min.js" integrity="sha512-QMUqEPmhXq1f3DnAVdXvu40C8nbTgxvBGvNruP6RFacy3zWKbNTmx7rdQVVM2gkd2auCWhlPYtcW2tHwzso4SA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <title>Registration</title>
</head>
<body>
    <?php
        if (isset($_GET['success'])){
            echo '<script>showPopUp()</script>';
        }
    ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8 col-md-6"></div> <!-- Col-8 -->
            <div class="col-lg-4 col-md-6 col-xs-12 p-5 registrasi-side"> <!-- Start Registration Side -->
                <div class="registrasi-side-header mb-2">
                    <p class="h2 text-center mb-3">Create an account</p>
                    <p class="font-italic text-center">Lorem, ipsum dolor sit amet consectetur adipisicing elit.</p>
                </div>

                <div class="registrasi-side-form">
                    <form name="formReg" method="post" action="../../controller/registrationProcess.php">
                        <div class="form-group email-form">
                            <label for="email">Email</label>
                            <input type="email" name="email" class="form-control" id="email" oninput="validate()" placeholder="example : ex@gmail.com">
                            <small id="emailHelpBlock" class="form-text text-danger d-none">
                                Your email is not valid!
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="username">Name</label>
                            <input type="text" name="name" class="form-control name-input" id="username" oninput="allLetter(document.formReg.name)" placeholder="example : Bambang">
                            <small id="nameHelpBlock" class="form-text text-danger d-none">
                                Numbers Not Allowed!
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="phone">No. Hp</label>
                            <input id="phone" name="phoneNum" type="text" class="form-control phone-input" placeholder="example : 081234567890" oninput="allNumber(document.formReg.phoneNum)">
                            <small id="phoneHelpBlock" class="form-text text-danger d-none">
                                Letters Not Allowed!
                            </small>
                        </div>

                        <button disabled class="btn btn-lg btn-registrasi w-100 mt-2" onclick="showPopUp()">Registrasi</button>
                    </form>
                </div>

                <div class="registrasi-side-bottom position-absolute mt-5">
                    <p class="text-center">Already have an account? <a href="../login.php">Sign-In</a></p>
                </div>

            </div>
        </div>
    </div>


    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

    <script src="https://use.fontawesome.com/7a7a4d3981.js"></script>

    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="../../public/js/registrasi.js"></script>
</body>
</html>