<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css"
        integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">

    <!-- intlTelInput CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/css/intlTelInput.css"
        integrity="sha512-gxWow8Mo6q6pLa1XH/CcH8JyiSDEtiwJV78E+D+QP0EVasFs8wKXq16G8CLD4CJ2SnonHr4Lm/yY2fSI2+cbmw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Independent CSS-->
    <link rel="stylesheet" href="../../public/css/registration.css">

    <title>Registration | Lumintu Event</title>

    <!-- Jquery CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"
        integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

    <!-- Bootstrap CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns"
        crossorigin="anonymous"></script>

    <!-- SweetAlert2 CDN -->
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Deklarasi Function Show Pop Up Sukses
        const showPopUp = () => {
            Swal.fire({
                icon: 'success',
                title: 'Registration Success!',
                showConfirmButton: true,
                confirmButtonColor: '#3085d6',
                text: "Make sure to check your email to verify your account.",
            })
        }

        // Deklarasi Function Show Pop Up Email Exist
        const showMailExist = () => {
            Swal.fire({
                icon: 'error',
                title: 'Registration Failed!',
                showConfirmButton: true,
                confirmButtonColor: '#3085d6',
                text: `Your Email already exists!"`,
            })
        }
    </script>

</head>

<body>
    <?php
        if (isset($_GET['scs'])){
            echo '<script type="text/javascript">
                    showPopUp();
                </script>';
        }elseif(isset($_GET['mailExist'])){
            echo '<script type="text/javascript">showMailExist();</script>';
        }
    ?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-8 col-lg-7 col-md-6 col-sm-12 col-xs-12"></div> 
            <div class="col-xl-4 col-lg-5 col-md-6 col-sm-12 col-xs-12 p-5 registrasi-side">
                <div class="registrasi-side-header mb-4">
                    <p class="h2 text-center mb-3 text-white">Create an account</p>
                    <blockquote class="blockquote text-center mb-3">
                        <p class="mb-0 font-italic gold h6">
                            Get to know about something first, before you can love it better
                        </p>
                        <footer class="blockquote-footer">
                            Someone famous in <cite title="Source Title">Indonesia</cite>
                        </footer>
                    </blockquote>
                </div>

                <div class="registrasi-side-form">
                    <form name="formReg" method="post" action="../../controller/registrationProcess.php">
                        <div class="form-group email-form">
                            <label for="email" class="text-white">Email</label>
                            <input type="email" name="email" class="form-control" id="email" oninput="validate()"
                                placeholder="example : ex@gmail.com">
                            <small id="emailHelpBlock" class="form-text text-danger d-none">
                                Your email is not valid!
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="username" class="text-white">Name</label>
                            <input type="text" name="name" class="form-control name-input" id="username"
                                oninput="allLetter(document.formReg.name)" placeholder="example : Bambang">
                            <small id="nameHelpBlock" class="form-text text-danger d-none">
                                Numbers Not Allowed!
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="phone" class="text-white">No. Hp</label>
                            <input id="phone" name="phoneNum" type="tel" class="form-control phone-input"
                                oninput="allNumber(document.formReg.phoneNum)">
                            <small id="phoneHelpBlock" class="form-text text-danger d-none">
                                Numbers Not Allowed!
                            </small>
                        </div>

                        <button disabled class="btn btn-registrasi w-100 mt-2 p-2">
                            Registrasi
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <!-- Font Awesome CDN -->
    <script src="https://use.fontawesome.com/7a7a4d3981.js"></script>

    <!-- intl-tel-input -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/intlTelInput.min.js"
        integrity="sha512-QMUqEPmhXq1f3DnAVdXvu40C8nbTgxvBGvNruP6RFacy3zWKbNTmx7rdQVVM2gkd2auCWhlPYtcW2tHwzso4SA=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <!-- Independent Javascript -->
    <script src="../../public/js/registration.js"></script>

</body>

</html>