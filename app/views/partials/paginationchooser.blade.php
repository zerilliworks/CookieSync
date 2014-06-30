{{ Form::open(['action' => 'OptionsController@postSetListLength', 'method' => 'post', 'style' => 'float: right; padding: 20px 0;']); }}
<div class="input-group">
    <span class="input-group-addon">Show</span>
    <select class="form-control" id="list-length-selector" name="list-length" >
        <option {{ $paginationLength == 10 ? 'selected="selected"' : null }}value="10"> 10 Saves</option>
        <option {{ $paginationLength == 20 ? 'selected="selected"' : null }}value="20"> 20 Saves</option>
        <option {{ $paginationLength == 30 ? 'selected="selected"' : null }}value="30"> 30 Saves</option>
        <option {{ $paginationLength == 50 ? 'selected="selected"' : null }}value="50"> 50 Saves</option>
        <option {{ $paginationLength == 100 ? 'selected="selected"' : null }}value="100"> 100 Saves</option>
    </select>
            <span class="input-group-btn">
                <button id="sample-button" class="btn btn-success" type="submit" data-loading-text="Loading...">Go!</button>
            </span>
</div>
{{ Form::close() }}
