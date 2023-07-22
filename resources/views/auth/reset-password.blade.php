<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
  <title>Reset Password &mdash; SALES</title>
  <!-- Favicon -->
  <link rel="icon" type="image/png" href="{{ asset('assets/img/favicon-32x32.png') }}" sizes="32x32" />
  <link rel="icon" type="image/png" href="{{ asset('assets/img/favicon-16x16.png') }}" sizes="16x16" />

  <!-- General CSS Files -->
  <link rel="stylesheet" href="{{ asset('assets/modules/bootstrap/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/modules/fontawesome/css/all.min.css') }}">

  <!-- CSS Libraries -->

  <!-- Template CSS -->
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/components.css') }}">
<!-- Start GA -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-94034622-3"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-94034622-3');
</script>
<!-- /END GA --></head>

<body>
  <div id="app">
    <section class="section">
      <div class="container mt-5">
        <div class="row">
          <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3">
            <div class="card card-primary">
              <div class="card-header"><h4>Set Password</h4></div>
              <div class="card-body">
                <form method="POST" id="form_submit" action="/reset-password">
                  <input type="hidden" name="token" value="{{ $token }}">
                  <div class="form-group">
                    <label for="password">New Password</label>
                    <input id="password" type="password" class="form-control pwstrength" data-indicator="pwindicator" name="password" tabindex="2" required>
                  </div>
                  <div class="form-group">
                    <label for="password-confirm">Confirm Password</label>
                    <input id="password-confirm" type="password" class="form-control" name="confirm-password" tabindex="2" required>
                  </div>

                  <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                      Reset Password
                    </button>
                  </div>
                </form>
              </div>
            </div>
            <div class="simple-footer">
              Copyright &copy; Kharisma Stationary 2023
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>

  <!-- General JS Scripts -->
  <script src="{{ asset("assets/modules/jquery.min.js")}}"></script>
  <script src="{{ asset("assets/modules/popper.js")}}"></script>
  <script src="{{ asset("assets/modules/tooltip.js")}}"></script>
  <script src="{{ asset("assets/modules/bootstrap/js/bootstrap.min.js")}}"></script>
  <script src="{{ asset("assets/modules/nicescroll/jquery.nicescroll.min.js")}}"></script>
  <script src="{{ asset("assets/modules/moment.min.js")}}"></script>
  <script src="{{ asset('assets/modules/sweetalert/sweetalert.min.js') }}"></script>
  <script src="{{ asset("assets/js/stisla.js")}}"></script>
  
  <!-- JS Libraies -->

  <!-- Page Specific JS File -->
  
  <!-- Template JS File -->
  <script src="{{ asset("assets/js/scripts.js")}}"></script>
  <script src="{{ asset("assets/js/custom.js")}}"></script>

  @include('scriptjs')
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                // 'Authorization': '{{session()->get('token_jwt')}}',
            }
        });
    </script>
</body>
</html>