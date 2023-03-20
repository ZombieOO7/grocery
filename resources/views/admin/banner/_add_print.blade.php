<div class="input-group err_msg">
    @if (@$banner->image != null)
        <img id="blah" src="{{ @$banner->attachment->image_path }}" alt="" max-width="200" width="200" height="200"
            style="{{ isset($banner->image) ? 'display:block;' : 'display:none;' }}" />
    @else
        <img id="blah" src="{{ url('storage/app/public/uploads/' . @$banner->image) }}" alt="" width="200"
            max-width="200" height="200" style="{{ isset($banner->image) ? 'display:block;' : 'display:none;' }}" />
    @endif
</div>
