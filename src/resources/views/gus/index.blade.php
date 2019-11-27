<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Search Gus</title>
</head>
<body>
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<h1>GUS</h1>
{{Form::open(['route' => 'gus.search', 'method' => 'post'])}}
{{Form::label('number', 'Podaj NIP lub REGON')}}
{{Form::text('number',null,['id'=>'number'])}}
<br><br><br>
{{Form::token()}}
{{Form::submit('START',['id' => 'submit'])}}
{{Form::close()}}
</body>
</html>

