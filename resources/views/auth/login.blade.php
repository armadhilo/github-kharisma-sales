<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Login &mdash; SALES KHARISMA</title>
  <link rel="shortcut icon" type="image/x-icon" href="{{asset('assets/public_page/images/favicon.ico')}}">

    <!-- General CSS Files -->
    <link rel="stylesheet" href="{{ asset('assets/css/login/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/modules/fontawesome/css/all.min.css') }}">

    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('assets/modules/bootstrap-social/bootstrap-social.css') }}">

    <!-- Template CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/components.css') }}">
    <style>
      @media (max-width: 767px){
         #login_image{
            display: none!important;
         }
         #login_form{
            padding: 0 10% 0 10%;
         }
      }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
</head>

<body>
  <div id="app">
    <section class="section">
       <div class="d-flex flex-wrap align-items-stretch">
          <div class="col-lg-4 col-md-6 col-12 order-lg-1 min-vh-100 order-2 bg-white" id="login_form">
             <div class="p-4 m-3">
                <div class="d-flex align-items-center" style="gap: 20px;">
                  <img src="{{ asset('assets/img/kharisma_logo.png') }}" alt="logo" width="80" class="mb-4 mt-2">
                  <div>
                     <h4 class="text-dark font-weight-normal">Welcome to <span class="font-weight-bold">SALES KHARISMA</span></h4>
                     <p class="text-muted">Before you get started, you must login.</p>
                  </div>
               </div>
                <form id="form">
                   <div class="form-group">
                      <label>Email</label>
                      <input id="text" type="text" class="form-control" name="email" tabindex="1" required autofocus>
                      <div class="invalid-feedback">
                         Mohon Isi email anda
                      </div>
                   </div>
                   <div class="form-group">
                      <div class="d-block">
                         <label for="password" class="control-label">Password</label>
                      </div>
                      <input id="password" type="password" class="form-control" name="password" tabindex="2" required>
                      <div class="invalid-feedback">
                         Mohon isi Password anda
                      </div>
                   </div>
                   <div class="form-group text-right">
                      <button type="submit" class="btn btn-success btn-lg btn-icon icon-right" tabindex="3">
                      Login
                      </button>
                   </div>

                   {{-- <a href="/auth/google"><button type="button" class="btn btn-block btn-danger" style="margin-top: 15%;"> <i class="fab fa-google mr-2"></i> Login with Google</button></a>
                   <a href="/auth/facebook"><button type="button" class="btn btn-block btn-info mt-2"> <i class="fab fa-facebook mr-2"></i> Login with Facebook</button></a> --}}
                </form>

                <div class="text-center text-small" style="margin-top: 46%;">
                   Copyright &copy; KHARISMA
                </div>
             </div>
          </div>

          <div class="col-lg-8 col-md-6 col-12 order-lg-2 order-1 min-vh-100 background-walk-y position-relative overlay-gradient-bottom" style="background-size: cover" id="login_image" data-background="{{ asset('assets/img/login.jpg') }}">
             <div class="absolute-bottom-left index-2">
                <div class="text-light p-5 pb-0">
                   <div class="mb-0 pb-0">
                      {{-- <h1 class="mb-2 display-4 font-weight-bold" style="font-size: 3rem;">ADMIN PANEL</h1> --}}
                      {{-- <h5 class="font-weight-normal text-muted-transparent">lorem ipsum dolor sit amet consectetur adipiscing elit </h5> --}}
                   </div>
               </div>
             </div>
          </div>
          <!-- Modal Load-->
          <div class="modal fade" role="dialog" id="modal_loading" data-keyboard="false" data-backdrop="static">
            <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body pt-0" style="background-color: #FAFAF8; border-radius: 6px;">
                    <div class="text-center">
                        <img style="border-radius: 4px; height: 140px;" src="{{ asset('assets/img/project/icon/loader.gif') }}" alt="Loading">
                        <h6 style="position: absolute; bottom: 10%; left: 38%;" class="modal-title pb-2">Mohon Tunggu..</h6>
                    </div>
                </div>
            </div>
        </div>
       </div>
    </section>
 </div>

    <!-- General JS Scripts -->
    <script src="{{ asset('assets/modules/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/modules/popper.js') }}"></script>
    <script src="{{ asset('assets/modules/bootstrap/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/modules/nicescroll/jquery.nicescroll.min.js') }}"></script>
    <script src="{{ asset('assets/modules/moment.min.js') }}"></script>
    <script src="{{ asset('assets/js/stisla.js') }}"></script>

    <!-- JS Libraies -->
    <script src="{{ asset('assets/modules/sweetalert/sweetalert.min.js') }}"></script>

    <!-- Template JS File -->
    <script src="{{ asset('assets/js/scripts.js') }}"></script>
    <script src="{{ asset('assets/js/custom.js') }}"></script>

    <!-- Page Specific JS File -->
    <script type="text/javascript">

      $('#form').submit(function(e){
         e.preventDefault();

         $("#modal_loading").modal('show');
         $.ajax({
            url : "/login",
            type: "POST",
            data: $('#form').serialize(),
            dataType: "JSON",
            success: function(response){
               setTimeout(function () {  $('#modal_loading').modal('hide'); }, 500);
               if(response.status === 201){
                  swal(response.message, {  icon: 'success', });
                  window.location.href = response.link;
               }else{
                  swal(response.message, {  icon: 'error', });
               }
            },
            error: function (jqXHR, textStatus, errorThrown){
               setTimeout(function () {  $('#modal_loading').modal('hide'); }, 500);
               swal("Oops! Terjadi kesalahan segera hubungi tim IT (" + errorThrown + ")", {  icon: 'error', });
            }
         });
      })

      $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });
  </script>
</body>
</html>
