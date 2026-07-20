<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{gs('site_name')}}</title>
</head>

<body>
<form action="{{$data->url}}" method="{{$data->method}}" id="auto_submit">
    @foreach($data->val as $k=> $v)
        <input type="hidden" name="{{$k}}" value="{{$v}}"/>
    @endforeach
</form>
<script>
	"use strict";
    // Small delay to ensure form is ready before submit
    setTimeout(function() {
        document.getElementById("auto_submit").submit();
    }, 100);
</script>
</body>

</html>

