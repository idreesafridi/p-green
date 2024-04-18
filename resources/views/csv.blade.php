<form action="{{ route('csvUpload') }}" method="post">
    @csrf
    <input type="file" name="csv" id="csv">
    <input type="submit" value="Submit">
</form>
