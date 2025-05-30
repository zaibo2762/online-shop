@extends('admin.layouts.app')
@section('content')
<section class="content-header">					
    <div class="container-fluid my-2">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Edit Page</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="{{ route('pages.index') }}" class="btn btn-primary">Back</a>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
</section>
<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="container-fluid">
        <form action="" method="post" id="pageForm" name="pageForm">
            
        <div class="card">
            <div class="card-body">								
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="name">Name</label>
                            <input value="{{ $page->name }}" type="text" name="name" id="name" class="form-control" placeholder="Name">
                            <p></p>	
                        </div>
                        
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="slug">Slug</label>
                            <input type="text" value="{{ $page->slug }}" readonly name="slug" id="slug" class="form-control" placeholder="Slug">	
                            <p></p>
                        </div>
                       
                    </div>	
                   	 <div class="col-md-12">
                        <div class="mb-3">
                            <label for="content">Content</label>
                            <textarea name="content" id="content" class="summernote" cols="30" rows="10">{!! $page->content !!}</textarea>
                        </div>								
                    </div>     		
                   						
                </div>
            </div>							
        </div>
   
        <div class="pb-5 pt-3">
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="{{ route('pages.index') }}" class="btn btn-outline-dark ml-3">Cancel</a>
        </div>
    </div>
</form>
    <!-- /.card -->
</section>
<!-- /.content -->

@endsection

@section('customJs')
    <script>
        $('#pageForm').submit(function(event){
            event.preventDefault();
            var form = $(this); // Get the form
            var formData = form.serialize();
            $("button[type=submit]").prop('disabled',true)
            $.ajax({
                url:'{{ route('pages.update',$page->id) }}',
                type:'put',
                data: formData,
                dataType:'json',
                success:function(response){
                    if(response.status == true){
                        $("button[type=submit]").prop('disabled',false)
                        window.location.href = "{{ route('pages.index') }}";
                        $('#name').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');
                        $('#slug').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');
                        

                    }else{
                        var errors = response['errors'];
                    if(errors['name']){
                        $('#name').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors['name']);
                    }else{
                        $('#name').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');
                    }
                    if(errors['slug']){
                        $('#slug').addClass('is-invalid').siblings('p').addClass('invalid-feedback').html(errors['slug']);
                    }else{
                        $('#slug').removeClass('is-invalid').siblings('p').removeClass('invalid-feedback').html('');
                    }
                    }
                   
                },
                error:function(jqXHR, exception){
                    console.log('Something went wrong');
                }
            })
        });

        $('#name').change(function(){
            $("button[type=submit]").prop('disabled',true)
            element = $(this);
            $.ajax({
                url:'{{ route('getSlug') }}',
                type:'get',
                data: {title:element.val()},
                dataType:'json',
                success:function(response){
                    if(response['status'] == true){
                        $("button[type=submit]").prop('disabled',false)
                        $('#slug').val(response["slug"])
                    }

                }
        });

        })


     


        
    </script>
@endsection