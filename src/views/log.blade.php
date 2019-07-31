@extends("cms::layouts.default")

@section("content")
	<div class="container">
		<pre>{{file_get_contents($path)}}</pre>
	</div>
@endsection
