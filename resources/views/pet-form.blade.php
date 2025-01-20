<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sellasist Konrad Ptak Laravel + Vue</title>
    @vite(['resources/js/pet-form.js'])
</head>
<body>
<div id="pet-form"
     data-id="{{ $id ?? '' }}">
</div>
</body>
</html>
