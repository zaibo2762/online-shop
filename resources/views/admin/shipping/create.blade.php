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
                                                <option value="{{ $country->id }}">{{ $country->name }}</option>
                                            @endforeach
                                            <option value="rest_of_world">Rest Of The World</option>
                                        @endif
                                    </select>

                                    <p></p>
                                </div>

                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <input type="text" name="amount" id="amount" class="form-control"
                                        placeholder="Amount">
                                    <p></p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <button type="submit" class="btn btn-primary">Create</button>
                                </div>
                            </div>


                        </div>
                    </div>

                </div>
        </div>

        </form>
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-stripe">
                            <tr>
                                <th>Id</th>
                                <th>Name</th>
                                <th>Amount</th>
                                <th>Action</th>
                            </tr>

                            @if ($shippingcharges->isNotEmpty())
                                @foreach ($shippingcharges as $shippingcharge)
                                    <tr>
                                        <td>{{ $shippingcharge->id }}</td>
                                        <td>{{ $shippingcharge->country_id == 'rest_of_world' ? 'Rest Of The World' : $shippingcharge->name }}
                                        </td>
                                        <td>${{ $shippingcharge->amount }}</td>
                                        <td>
                                            <a href="{{ route('shipping.edit', $shippingcharge->id) }}"
                                                class="btn btn-primary">Edit</a>
                                            <a href="javascript:void(0);" onclick="deleteRecord({{ $shippingcharge->id }})"
                                                class="btn btn-danger">Delete</a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif

                        </table>
                    </div>
                </div>
            </div>
        </div>
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
                url: '{{ route('shipping.store') }}',
                type: 'post',
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

        function deleteRecord(id) {
            var url = '{{ route('shipping.delete', 'ID') }}';
            var newUrl = url.replace('ID', id)

            if (confirm("Are you sure to Delete")) {
                $.ajax({
                    url: newUrl,
                    type: 'delete',
                    data: {},
                    dataType: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response['status']) {
                            window.location.href = "{{ route('shipping.create') }}";
                        }
                    }
                })
            }


        }
    </script>
@endsection
