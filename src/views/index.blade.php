@extends("cms::layouts.default")

@section("content")
	<div class="container">
		@include('cms::components.alert')
		<div class="card card-default">
			<div class="card-header">
				<div class="col"></div>
				<div class="col-1">
					<a href="{{route('cms:plugins:contentImporters.download')}}"
					   target="_blank">download</a>
				</div>
			</div>
			<div class="card-body">
				<form action="{{route('cms:plugins:contentImporters.action')}}"
				      method="POST"
				      enctype="multipart/form-data"
				>
			
			{{csrf_field()}}
					
					<div class="form-group">
				{{Form::label('file','File',['class'=>'form-label'])}}
						{{Form::file('file',['class'=>$errors->has('file')?"form-control is-invalid":"form-control","required"])}}
						@if ($errors->has('file'))
							<span class="invalid-feedback">
			          <strong>{{ $errors->first('file') }}</strong>
			      </span>
						@endif
			</div>
			
			
			<div class="form-group">
				<button class="btn btn-success">Submit</button>
			</div>
		</form>
			</div>
		</div>
		
	</div>
@endsection
