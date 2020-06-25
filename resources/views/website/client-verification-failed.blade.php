@extends('website.master')

@section('content')
    <div class="dialog-form">
        <div class="dialog-form__dialog">
            <div class="dialog-form__row dialog-form__row--header"></div>

            <div class="dialog-form__row dialog-form__row--title">
                <div class="dialog-form__logo"></div>
                <h1 class="dialog-form__title">client verification</h1>
            </div>

            <div class="dialog-form__row dialog-form__row--client-verification-completed">
                <div class="account-verification-message">
                    <div class="account-verification-message__icon" style="color: #ba000d;">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>

                    <div class="account-verification-message__title">
                        an error has occured!
                        <div class="account-verification-message__text">
                          @if(isset($message))
                            {{ $message }}
                          @endif
                        </div>
                    </div>
                </div>

                <div class="dialog-form__row dialog-form__row--client-verification-completed-buttons">
                    
                </div>
            </div>
        </div>
    </div>
@endsection
