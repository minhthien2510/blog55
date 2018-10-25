@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Search</div>

                <div class="panel-body">
                    <form class="form-inline" method="POST" action="{{ route('search') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="search" class="col-md-4 control-label">Search</label>

                            <div class="col-md-6">
                                <input id="search" type="search" class="form-control" name="search" value="{{ old('search') }}" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Search
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://twitter.github.io/typeahead.js/js/handlebars.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"></script>
<script src="https://twitter.github.io/typeahead.js/releases/latest/typeahead.bundle.js"></script>
<script>
    jQuery(document).ready(function($) {
        var cateTeams  = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('team'),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {
                url: 'categories/search?q=%QUERY%',
                wildcard: '%QUERY%'
            }
        });

        var postTeams  = new Bloodhound({
            datumTokenizer: Bloodhound.tokenizers.obj.whitespace('team'),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {
                url: 'posts/search?q=%QUERY%',
                wildcard: '%QUERY%'
            }
        });

        // cateTeams.initialize();

        $("#search").typeahead({
            hint: true,
            highlight: true,
            minLength: 1
        }, {
            name: 'categories',
            display: 'name',
            source: cateTeams,
            templates: {
                // empty: [
                //     '<div class="empty-message">',
                //     'unable to find any Best Picture winners that match the current query',
                //     '</div>'
                // ].join('\n'),
                header: [
                    '<h3 class="league-name">Categories'
                ],
                suggestion: function (data) {
                    console.log(data);
                    return '<div class="list-group-item tt-suggestion tt-selectable" style="cursor: pointer">' + data.name + '</div>'
                },
                // footer: Handlebars.compile('</div>')
            }
        }, {
            name: 'posts',
            display: 'name',
            source: postTeams,
            templates: {
                // empty: [
                //     '<div class="empty-message">',
                //     'unable to find any Best Picture winners that match the current query',
                //     '</div>'
                // ].join('\n'),
                header: [
                    '<h3 class="league-name">Posts'
                ],
                suggestion: function (data) {
                    console.log(data);
                    return '<div class="list-group-item tt-suggestion tt-selectable" style="cursor: pointer">' + data.name + '</div>'
                }
            }
        });
    });
</script>
@stop
