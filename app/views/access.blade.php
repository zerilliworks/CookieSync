@extends('layout')

<?php View::share('_page_title', 'Sign Up') ?>

@section('css')
<style type="text/css">
    body {
        padding-top: 40px;
        padding-bottom: 0px;
        background-color: #eee;
    }

    h1,
    h2,
    h3,
    h4 {
        font-family: 'Kavoon', 'Georgia', serif;
    }

    p.lead {
        font-family: 'Kavoon', 'Georgia', 'Helvetica', sans-serif;
    }

    .forms-wrapper {
        max-width: 355px;
        padding: 0;
        margin: 0 auto;
        height: 260px;
        overflow: visible;
    }

    .form-container {
        width: 355px;
        overflow: visible;
        padding: 30px 0;
        margin: 0;
    }

    .form-signup {
        overflow: visible;
        position: absolute;
        width: 355px;
        /*padding: 15px;*/
        overflow: visible;
        -webkit-transition: all 400ms cubic-bezier(0.785, 0.135, 0.150, 0.860);
        -moz-transition: all 400ms cubic-bezier(0.785, 0.135, 0.150, 0.860);
        -o-transition: all 400ms cubic-bezier(0.785, 0.135, 0.150, 0.860);
        transition: all 400ms cubic-bezier(0.785, 0.135, 0.150, 0.860); /* easeInOutCirc */

    }

    .animated {
        -webkit-transition: all 700ms cubic-bezier(0.445, 0.050, 0.550, 0.950);
        -moz-transition: all 700ms cubic-bezier(0.445, 0.050, 0.550, 0.950);
        -o-transition: all 700ms cubic-bezier(0.445, 0.050, 0.550, 0.950);
        transition: all 700ms cubic-bezier(0.445, 0.050, 0.550, 0.950); /* easeInOutSine */
    }

    .form-signup .form-signup-heading,
    .form-signup .checkbox {
        margin-bottom: 10px;
    }

    .form-signup .checkbox {
        font-weight: normal;
    }

    .form-signup .form-control {
        position: relative;
        font-size: 16px;
        height: auto;
        padding: 10px;
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
    }

    .form-signup .form-control:focus {
        z-index: 2;
    }

    .form-signup input[type="text"] {
        margin-bottom: -1px;
        border-bottom-left-radius: 0;
        border-bottom-right-radius: 0;
    }

    .form-signup input[type="email"] {
        margin-bottom: -1px;
        border-radius: 0;
    }

    .form-signup input[type="password"] {
        margin-bottom: 10px;
        border-top-left-radius: 0;
        border-top-right-radius: 0;
    }

    .form-group {
        margin: 0;
    }

    .form-register {
        position: relative;
        height: 0;
        padding: 0;
        overflow: hidden;

        background-color: #eee;
        z-index: 4;
    }

    .available {
        background-color: #89db2c;
        color: white;
    }

    .unavailable {
        background-color: #fa4;
        color: white;
    }

    .btn-link {
        text-align: center;
    }
</style>
@stop

@section('body')
<div class="row" style="">
    <div class="col-md-4 col-md-offset-4 col-sm-12">
        <h1 style="text-align: center">CookieSync</h1>
        <h5 style="text-align: center" class="text-muted">For dopes that Cookie Click everywhere.</h5>

        @if( $errors->any() )

        <div class="alert alert-danger">
            <h4>Better double-check that.</h4>

            <p>Whatever you put in there, it made the database groan uncomfortably.</p>
            <ul>
                @foreach( $errors->toArray() as $error )
                <li>{{ $error[0] }}</li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>
</div>


<div class="forms-wrapper animated">


    <!-- Login form -->
    <form id="login" class="form-signup" method="POST" action="{{ action('AuthController@postLoginCredentials') }}">
        <div class="form-container">
            <h4 style="text-align: center" class="text-muted">Log In to your account</h4>
            <a id="switch-to-register" class="btn btn-small btn-link btn-block" href="#">Or Create an Account</a>
            {{ Form::token() }}
            <div id="username-field" class="form-group">
                <input id="username-input" type="text" class="form-control" placeholder="User name" name="username"
                       autocapitalize="off" autocorrect="off" spellcheck="false" autofocus>
            </div>
            <input type="password" class="form-control" placeholder="Passkey" name="password">
            <button class="btn btn-lg btn-primary btn-block" type="submit">Let Me In!</button>

        </div>


    </form>
    <!-- end login form -->

    <!-- registration form -->
    <form id="registration" class="form-signup form-register animated" style="height: 0px" method="POST"
          action="{{ action('AuthController@postRegistrationInfo') }}"
          onsubmit="return ValidateAsirra();">
        <div class="form-container">
            <h4 style="text-align: center" class="text-muted">Register a new account</h4>
            <a id="switch-to-login" class="btn btn-small btn-link btn-block" href="#">Or Log In to an Account</a>
            {{ Form::token() }}
            <div id="username-field" class="form-group">
                <input id="username-input" type="text" class="form-control" placeholder="User name" name="username"
                       autocapitalize="off" autocorrect="off" spellcheck="false" autofocus>
            </div>
            <input type="password" class="form-control" placeholder="Passkey" name="password" style="margin-bottom: -1px; border-radius: 0;">
            <input type="password" class="form-control" placeholder="Confirm Passkey" name="password_confirmation">

            <div id="asirra-wrapper"
                 style="margin-top: 30px; margin-bottom: 20px; position: relative; overflow: visible;">
                {{ Form::asirra() }}
            </div>

            <button class="btn btn-lg btn-warning btn-block" type="submit">Sign Me Up!</button>
        </div>

    </form>

    <!-- end registration form -->
</div>


<div class="row">
    <div class="col-md-6 col-md-offset-3 col-xs-12">
        <blockquote>
            <h2>It's stupid how easy this is.</h2>
            <h4 class="text-muted">&mdash; Grandma</h4>
        </blockquote>
    </div>
</div>
<div class="row">
    <div class="col-md-6 col-xs-12">
        <p>This thing stores and retrieves <a href="http://orteil.dashnet.org/cookieclicker">Cookie Clicker</a> saves.
            It's really easy. Create an account
            get a bookmarklet to stick in your browser. While you're Cookie Clickin', just mash that bookmark to
            save a game. That's pretty much it. You can keep track of each separate Cookie Clicker game and follow its
            progress. Marvel as the system shows you how many cookies you've baked over your entire career!
        </p>

        <p>Yes, CookieSync is more than a convenience tool; it's a Cookie Clicker stat aggregator. As the user base grows,
            so does our knowledge of how people play Cookie Clicker and (ultimately) the measure of all the cookies collected
            by everyone ever. Interesting, no?
        </p>

        <p>
            Log in anywhere to get your saves back. Don't worry, nothing is too tricky. You can add and delete saves at
            any time. You can also completely erase your account at any time. It's on the options page. Minimal personal
            information is collected; only an anonymous analytics cookie (no ads or spam).
        </p>
    </div>
    <div class="col-md-6 col-xs-12">
        <p>Still alpha-level, CookieSync evolves fairly quickly. Right now, some things are unimplemented.
            In its current state, CookieSync represents significant time spent working.</p>

        <h4>Features in the works:</h4>
        <ol>
            <li>Complete stat tracking and "career" progress</li>
            <li>Graphing of cookie counts and other stats</li>
            <li>Better details of buildings</li>
            <li>Email-based password resets</li>
            <li>Social media logins (Maybe. If I did add them, they'd be totally optional, nothing but an auth token stored)</li>
            <li>Improved Cookie Clicker integration</li>
            <li>Automatic saving</li>
        </ol>
    </div>
    <small class="text-muted">{{ App::environment() }}</small>
</div>
@stop

@section('footer-js')
<!--<script type="text/javascript">-->
<!--    var availability = '';-->
<!---->
<!--    $("#username-field").tooltip({ placement: 'top', trigger: 'manual', title: 'Nope. Try another one.'});-->
<!---->
<!--    $("#username-input").blur(function (e) {-->
<!--        $("#username-input").removeClass('available');-->
<!--        $("#username-input").removeClass('unavailable');-->
<!---->
<!--        if (!$(e.target).val()) {-->
<!--            $("#username-field").tooltip('hide');-->
<!--            return;-->
<!--        }-->
<!--        $.get('jax/nametaken', { name: $(e.target).val() })-->
<!--            .done(function (data) {-->
<!--                availability = data;-->
<!--                if (data == 'available') {-->
<!--                    $("#username-input").addClass('available');-->
<!--                    $("#username-field").tooltip('hide');-->
<!--                }-->
<!--                else {-->
<!--                    $("#username-input").addClass('unavailable');-->
<!--                    $("#username-field").tooltip('show');-->
<!--                }-->
<!--            });-->
<!--    });-->
<!--</script>-->

<script type="text/javascript">
    $('#asirra-wrapper').tooltip({
        'placement' : 'top',
        'title' : 'Please correctly identify the cats.',
        'trigger' : 'manual'
    });

    $('#asirra-wrapper').click(function()
    {
        $('#asirra-wrapper').tooltip('hide');
    });

    var passThroughFormSubmit = false;
    function ValidateAsirra() {
        if (passThroughFormSubmit) {
            return true;
        }
        // Do site-specific form validation here, then...
        Asirra_CheckIfHuman(HumanCheckComplete);
        return false;
    }
    function HumanCheckComplete(isHuman) {
        if (!isHuman) {
//            alert("Please correctly identify the cats.");
            $('#asirra-wrapper').tooltip('show');

        }
        else {
            passThroughFormSubmit = true;
            formElt = document.getElementById("registration");
            formElt.submit();
        }
    }

    // You can control where the big version of the photos appear by
    // changing this to top, bottom, left, or right
    asirraState.SetEnlargedPosition("top");

    // You can control the aspect ratio of the box by changing this constant
    asirraState.SetCellsPerRow(6);

    $('#switch-to-register').click(function () {
        $('#registration').css('height', '500px');
        $('.forms-wrapper').css('height', '490px');
    });

    $('#switch-to-login').click(function () {
        $('#registration').css('height', '0px');
        $('.forms-wrapper').css('height', '260px');

    });
</script>
@stop
