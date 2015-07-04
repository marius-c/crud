@extends('themes.semantic.layout')

@section('content')
    <div class="main container" style="width:50%; margin:0 auto;">
        <form method="post" class="ui form">

            <div class="field required">
                <label for="connection">Connection:</label>
                <select name="connection">
                    @foreach($connections as $conn)
                        <option>{{$conn}}</option>
                    @endforeach
                </select>
            </div>

            <div class="field required">
                <label for="table">Table:</label>
                <input type="text" id="table" name="table" required placeholder="Table" />
            </div>

            <button class="ui blue button" style="float:right">Generate</button>
            <input name="_token" type="hidden" value="{{csrf_token()}}" />
        </form>
        <br/><br/>
        <div class="clerafix"></div>
        <hr/>

        <div class="ui form">
            <textarea id="generated" rows="10" placeholder="Generated..." onclick="this.select()"></textarea>
        </div>
    </div>

@stop

@section('js')
    <script>
        $('form').submit(function(){
            $('#generated').val('Loading...');

            $.ajax({
                url: '{{Request::url()}}',
                data: $(this).serialize()+'&generate=1'
            })
            .success(function(data){
                $('#generated').val(data);
            })
            .fail(function(error){
                alert('ERROR! Check console.');
            });

            return false;
        });
    </script>
@stop
