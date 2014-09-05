@extends('maintenance::layouts.main')

@section('header')
	<h1>{{ $title }}</h1>
@stop

@section('content')


    {{ Form::open(array('url'=>route('maintenance.assets.images.store', array($asset->id)))) }}
<div id="current-container" class="form-group">
    {{ Form::button('Choose Files...', array('class'=>'btn btn-primary', 'id'=>'current-browse-button')) }}
    {{ Form::button('Upload Added Files', array('class'=>'btn btn-success', 'id'=>'current-start-upload')) }}
</div>

<div class="form-group">
    {{ Form::label('Files Added') }}
    <table id="added-table" class="table table-condensed table-bordered table-striped">
        <thead>
            <tr>
                <th>File Name</th>
                <th>File Size</th>
                <th>Remove</th>
            </tr>
        </thead>
        <tbody id="added-list"></tbody>
    </table>
    
    {{ Form::label('Files Uploaded') }}
    <table id="uploaded-table" class="table table-condensed table-bordered table-striped">
        <thead>
            <tr>
                <th>File Name</th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody id="uploaded-list">
        
        </tbody>
    </table>
    
</div>

<div class="form-group"><hr /></div>

<div class="form-group">
    {{ Form::submit('Save', array('class'=>'btn btn-success')) }}
</div>
    
    {{ HTML::script('packages/jildertmiedema/laravel-plupload/assets/js/plupload.full.min.js') }}
    <script type="text/javascript">
        var current_uploader = new plupload.Uploader({
                filters: {
                    mime_types : [
                      { title : "Image files", extensions : "jpg,gif,png" }
                    ],
                    max_file_size: "200mb",
                    prevent_duplicates: true
                },
                runtimes:"html5, html4",
                browse_button:"current-browse-button",
                container:"current-container",
                url:"{{ route('maintenance.assets.images.uploads.store') }}",
                headers:{"Accept":"application\/json"},
                chunk_size:"500kb",
				
                init: {
                        PostInit: function(){
                                $('#current-start-upload').on('click', function(){
                                        current_uploader.start();
                                        return false;
                                });
                        },
                        FilesAdded: function(up, files){
                                plupload.each(files, function(file) {
                                        $('#added-list').append(
                                                '<tr id="'+file.id+'">'+
                                                        '<td>'+file.name+'</td>'+
                                                        '<td>'+plupload.formatSize(file.size)+'</td>'+
                                                        '<td><a href="#" class="remove btn btn-danger btn-xs">Remove</a></td>'+
                                                '</tr>'
                                        );

                                        $('#' + file.id + ' a.remove').first().on('click', function() {
                                                current_uploader.removeFile(file);
                                                $('#' + file.id).hide();
                                                return false;
                                        });
                                });
                        }
                }
            });
        current_uploader.init();
        current_uploader.bind('FileUploaded', function(up, file, object) {
            var myData;
            try {
                myData = eval(object.response);
            } catch(err) {
                myData = eval('(' + object.response + ')');
            }
            $('#uploaded-list').append(myData.result.html);
			$('#'+file.id).remove();
        });

    $(document).on('click', '.delete-file-uploaded', function(){
        var tr = $(this).parents().eq(1);
        var file_path = $(this).data('file-path');
		var file_folder = $(this).data('file-folder');
		
        $.ajax({
            "type": "POST",
            "url": "{{ route('maintenance.assets.images.uploads.destroy') }}",
            "data": { file_path: file_path, file_folder: file_folder },
            "dataType": "json"
        }).done(function(result)
        {
            if(typeof result.message !== 'undefined')
            {
                showStatusMessage(result.message, result.messageType);

                if(result.messageType === 'success')
                {
                    tr.remove();
                    ajaxContent($(this).attr('href'), ".ajax-content", false);
                }
            }
            else if(typeof result.errorMessages !== 'undefined')
            {
                showRegisterFormAjaxErrors(result.errorMessages);
            }
        });
        return false;
    });
    </script>
</div>
@stop