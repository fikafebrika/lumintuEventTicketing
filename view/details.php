<?php
    include('../config.php');

//    $credential = base64_decode($_GET['m']);
//    echo $credential;
//    $myDataQuery = "SELECT * FROM `customers`";

?>
<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.css" integrity="sha512-UTNP5BXLIptsaj5WdKFrkFov94lDx+eBvbKyoe1YAfjeRPC+gT5kyZ10kOHCfNZqEui1sxmqvodNUx3KbuYI/A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.css" integrity="sha512-OTcub78R3msOCtY3Tc6FzeDJ8N9qvQn1Ph49ou13xgA9VsH9+LRxoFU6EqLhW4+PKRfU+/HReXmSZXHEkpYoOA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">

    <!-- CSS Independent -->
    <link rel="stylesheet" href="../public/css/main.css">

    <link rel="stylesheet" href="sweetalert2.min.css">

    <script defer src="sweetalert2.min.js"></script>

    <title>Hello, world!</title>
</head>
<body>
<div class="container banner-event rounded d-flex align-items-center p-5 mb-4">
    <div class="deskripsi w-50 p-5">
        <p class="h1 nama-event">Dream World Wide in Jogja</p>
        <p class="h4 tanggal mb-4">By Lumintu Logic</p>
        <p class="text-white">Gang, Jalan Candirejo, Jl. Pandega Siwi No.6A, Manggung, Caturtunggal, Depok Sub-District, Sleman Regency, Special Region of Yogyakarta 55281</p>
        <button class="btn btn-lg btn-buy w-50" data-toggle="modal" data-target="#exampleModalScrollable">Buy Ticket</button>
    </div>
</div>

<div class="container detail-container">
    <div class="row">
        <div class="col-6 description">
            <p class="h4 title-event mb-3">Description</p>
            <p class="text-justify"> Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of "de Finibus Bonorum et Malorum" (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, "Lorem ipsum dolor sit amet..", comes from a line in section 1.10.32.</p>
        </div>
        <div class="modal fade" id="exampleModalScrollable" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="header-popup modal-title container">
                            <p class="h3 text-center">Dream World Wide in Jogja</p>
                            <p class="organizer text-center">By Lumintu Logic</p>
                        </div>
                    </div>
                    <div class="modal-body">
                        <form class="body-form" method="post" action="../controller/insertInvitationProcess.php">
                            <div class="body-popup mx-3">
                                <div class="special">
                                    <div class="peserta p-2 rounded" id="peserta1">
                                        <h5>Peserta 1</h5>
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Email</label>
                                            <input type="email" name="peserta1" class="form-control" id="inputPeserta1" aria-describedby="emailHelp">
                                        </div>
                                    </div>
                                    <div class="special d-flex align-items-center mx-2 mb-2 justify-content-between">
                                        <div class="form-group w-50 my-auto">
                                            <input type="text" name="voucher" class="form-control kode-input" id="voucher" placeholder="Voucher Code">
                                        </div>
                                        <div class="toggle-buyMe d-flex flex-row-reverse ">
                                            <label class="switch">
                                                <input type="checkbox" class="switchMe">
                                                <span class="slider round"></span>
                                            </label>
                                            <small class="mr-1">Buy For Me</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right mt-2 mx-4 btn-plus">
                                <button type="button" class="btn btn-default btn-circle btn-lg rounded-circle" onclick="tambahkan()">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                            <div class="modal-footer border-0">
                                <div class="container text-center">
                                    <button class="btn btn-buy w-50">Buy Ticket</button>
                                </div>
                            </div>

                        </form>

                    </div>

                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="header-carousel">
                <div class="owl-carousel owl-theme">
                    <div class="item border-carousel-item active">
                        <p class="h5">Day 1</p>
                        <p class="tanggal-event">17 July 2021</p>
                    </div>
                    <div class="item border-carousel-item">
                        <p class="h5">Day 2</p>
                        <p class="tanggal-event">28 July 2021</p>
                    </div>
                    <div class="item border-carousel-item">
                        <p class="h5">Day 3</p>
                        <p class="tanggal-event">04 Agustus 2021</p>
                    </div>
                    <div class="item border-carousel-item">
                        <p class="h5">Day 4</p>
                        <p class="tanggal-event">05 Agustus 2021</p>
                    </div>
                    <div class="item border-carousel-item">
                        <p class="h5">Day 4</p>
                        <p class="tanggal-event">05 Agustus 2021</p>
                    </div>
                </div>
            </div>

            <div class="body-session">
                <div class="table-responsive">
                    <table class="table">
                        <tr>
                            <td class="date-session">08.00AM - 09.00 AM</td>
                            <td class="title-session  font-weight-bold">Registration</td>
                        </tr>
                        <tr>
                            <td class="date-session">09.00 AM - 10.00 AM</td>
                            <td class="title-session  font-weight-bold">Talk Show <br>
                                <span class="detail-session  font-weight-normal">Digitalization Project in the Palace of Yogyakarta</span></td>
                        </tr>
                        <tr>
                            <td class="date-session">10.00 AM - 10.45 AM
                            </td>
                            <td class="title-session">Registration</td>
                        </tr>
                        <tr>
                            <td class="date-session">10.00 AM - 10.45 AM
                            </td>
                            <td class="title-session font-weight-bold">Registration<br>
                                <span class="detail-session font-weight-normal">Moderated By : Dr. RM. Pramutomo, MA ISI Surakarta</span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>



<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js" integrity="sha512-bPs7Ae6pVvhOSiIcyUClR7/q2OAsRiovw4vAkX+zJbw3ShAeeqezq50RIIcIURq7Oa20rW2n2q+fyXBNcU9lrw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>


<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>

<script src="https://use.fontawesome.com/7a7a4d3981.js"></script>

<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script src="../public/js/details.js"></script>

<script type="text/javascript">

    var cred = " <?php echo(base64_decode($_GET['m'])); ?>";
    console.log(cred);

    $(document).on('change', '.switchMe', function() {
        if(this.checked) {
            $("input#inputPeserta1").val(cred);
            $("input#voucher").css('visibility', 'visible');
        } else{
            $("input#inputPeserta1").val('');
            $("input#voucher").css('visibility', 'hidden');
        }
    });
</script>
</body>
</html>