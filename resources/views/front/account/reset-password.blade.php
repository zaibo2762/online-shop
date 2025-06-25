@extends('front.layouts.app')
@section('content')
    <section class="section-5 pt-3 pb-3 mb-3 bg-white">
        <div class="container">
            <div class="light-font">
                <ol class="breadcrumb primary-color mb-0">
                    <li class="breadcrumb-item"><a class="white-text" href="{{ route('front.home') }}">Home</a></li>
                    <li class="breadcrumb-item">Reset Password</li>
                </ol>
            </div>
        </div>
    </section>
    @if (Session::has('success'))
        <div class="alert alert-success">
            {{ session::get('success') }}
        </div>
    @endif
    @if (Session::has('error'))
        <div class="alert alert-danger">
            {{ session::get('error') }}
        </div>
    @endif
    <section class=" section-10">
        <div class="container">
            <div class="login-form">
                <form action="{{ route('front.processResetPassword') }}" method="post">
                    @csrf
                    <h4 class="modal-title">Reset Password</h4>
                    <input type="hidden" name="token" value="{{ $token }}">
                    <div class="form-group">
                        <input type="password" class="form-control @error('new_password') is-invalid  @enderror"
                            placeholder="New Password" name="new_password" value="">
                        @error('new_password')
                            <p class="invalid-feedback"> {{ $message }} </p>
                        @enderror
                    </div>
                    <div class="form-group">
                        <input type="password" class="form-control @error('confirm_password') is-invalid  @enderror"
                            placeholder="Confirm Password" name="confirm_password" value="">
                        @error('confirm_password')
                            <p class="invalid-feedback"> {{ $message }} </p>
                        @enderror
                    </div>


                    <input type="submit" class="btn btn-dark btn-block btn-lg" value="Update">
                </form>
                <div class="text-center small"><a href="{{ route('account.login') }}">Click Here To Log In</a></div>
            </div>
        </div>
    </section>
@endsection
