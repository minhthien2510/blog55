@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Register</div>

                <div class="panel-body">
                    <form class="form-horizontal" method="POST" action="{{ route('register') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">Name</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus>

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">E-Mail Address</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">Password</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password" required>

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password-confirm" class="col-md-4 control-label">Confirm Password</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <div id="g-recaptcha"></div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="reset" class="btn btn-primary">
                                    Reset
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    Register
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('style')
    <style>
    </style>
@stop

@section('script')
    <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit&hl=en" async defer></script>

    <script type="text/javascript">
        var verifyCallback = function(response) {
            console.log(response);
        };

        var onRecaptchaExpired = function() {
            console.log("Your Recaptcha has expired, please verify it again !");
        };

        var widgetId;

        var onloadCallback = function() {
            widgetId = grecaptcha.render('g-recaptcha', {
                'sitekey': '6LcFtiQTAAAAAA6ys3czokyOo34igAiQNjugrwRO',
                'callback': verifyCallback,
                'theme': 'light',
                'size': 'normal',
                'expired-callback': onRecaptchaExpired,
                // 'error-callback': onRecaptchaError
            });

            if ((scale = $('#g-recaptcha').width() / 304) < 1) {
                $('#g-recaptcha').css({
                    'height': 78 * scale,
                    'transform':  'scale(' + scale + ')',
                    '-webkit-transform': 'scale(' + scale + ')',
                    'transform-origin': '0 0',
                    '-webkit-transform-origin': '0 0'
                });
            }
        };

        $('form').bind('reset', function () {
            grecaptcha.reset(widgetId);
        });
    </script>
@stop
