@extends('admin.layouts.app')
@section('content')
    <section class="content-header">
        <div class="container-fluid my-2">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Shipping Management</h1>
                </div>
            </div>
        </div>
        <!-- /.container-fluid -->
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="container-fluid">
            @include('admin.message')
            <form action="" method="post" id="shippingForm" name="shippingForm">

                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <select name="country" id="country" class="form-control">

                                        <option value="">Select A Country</option>
                                        @if ($countries->isNotEmpty())
                                            @foreach ($countries as $country)
                                                <option
                                                    {{ $shippingcharge->country_id == $country->id ? 'selected' : '' }}
                                                    value="{{ $country->id }}">{{ $country->name }}</option>
                                            @endforeach
                                            <option {{ $shippingcharge->country_id == 'rest_of_world' ? 'selected' : '' }}
                                                value="rest_of_world">Rest Of The World</option>
                                        @endif
                                    </select>

                                    <p></p>
                                </div>

                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <input value="{{ $shippingcharge->amount }}" type="text" name="amount"
                                        id="amount" class="form-control" placeholder="Amount">
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </div>
                            </div>


                        </div>
                    </div>

                </div>
        </div>

        </form>

        <!-- /.card -->
    </section>
    <!-- /.content -->

@endsection

@section('customJs')
    <script>
        $('#shippingForm').submit(function(event) {
            event.preventDefault();
            var form = $(this); // Get the form
            var formData = form.serialize();
            $("button[type=submit]").prop('disabled', true)
            $.ajax({
                url: '{{ route('shipping.update', $shippingcharge->id) }}',
                type: 'put',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    if (response.status == true) {
                        $("button[type=submit]").prop('disabled', false)
                        window.location.href = "{{ route('shipping.create') }}";



                    } else {
                        var errors = response['errors'];
                        if (errors['country']) {
                            $('#country').addClass('is-invalid').siblings('p').addClass(
                                'invalid-feedback').html(errors['country']);
                        } else {
                            $('#country').removeClass('is-invalid').siblings('p').removeClass(
                                'invalid-feedback').html('');
                        }
                        if (errors['amount']) {
                            $('#amount').addClass('is-invalid').siblings('p').addClass(
                                'invalid-feedback').html(errors['amount']);
                        } else {
                            $('#amount').removeClass('is-invalid').siblings('p').removeClass(
                                'invalid-feedback').html('');
                        }
                    }

                },
                error: function(jqXHR, exception) {
                    console.log('Something went wrong');
                }
            })
        });
    </script>
@endsection
