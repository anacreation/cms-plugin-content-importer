@extends("cms::layouts.default")

@section("content")
	<div class="container">
		@include('cms::components.alert')
		<div class="card card-default mt-3">
			<div class="card-header">
				<div class="col d-flex justify-content-between">
					<h3 class="m-0">Content Importer</h3>
					<div>
						<a href="{{route('cms:plugins:contentImporters.download')}}"
						   class="btn btn-sm btn-primary "
						   target="_blank">Download template file</a>
					<a href="{{route('cms:plugins:contentImporters.latest_log')}}"
					   class="btn btn-sm btn-info"
					   target="_blank">Show Latest Log</a>
					</div>
					
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
					<small class="helper"><em>.csv file only</em></small>
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
