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
                    <div class="account-verification-message__icon">
                        <i class="fas fa-check-circle"></i>
                    </div>

                    <div class="account-verification-message__title">
                        everything is done sir. have fun!
                    </div>
                </div>
            </div>

            <div class="dialog-form__row dialog-form__row--client-verification-completed-buttons">
                <a href="{{ route('home') }}" class="dialog-form__button">
                    home
                </a>

                <button
                    class="dialog-form__extra-link dialog-form__extra-link--small"
                    href="{{ route('logout') }}"
                >
                    logout
                </button>
            </div>
        </div>
    </div>
@endsection
