@extends("cms::layouts.default")

@section("content")
	<div class="container">
		@include('cms::components.alert')
		<div class="card card-default mt-3">
			<div class="card-header">
				<div class="col d-flex justify-content-between">
					<h3 class="m-0">Content Importer</h3>
					<a href="{{route('cms:plugins:contentImporters.download')}}"
					   class="btn btn-sm btn-primary "
					   target="_blank">Download template file</a>
				</div>
			</div>
			<div class="card-body">
				{{Form::open([
				'url'=>route('cms:plugins:contentImporters.action'),
				'method'=>'POST',
				'files'=>true,
				'class'=>'form'
				])}}
				<div class="form-group">
					{{Form::label('file','File: ',['class'=>'form-label'])}}
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
				{{Form::close()}}
				
			</div>
		</div>
		
	</div>
@endsection
