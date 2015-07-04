@extends('themes.bootstrap.layout')
@section('content')

    <div class="ui dividing header" style="font-size:20px;">
        @if($row->id)
            <?php $title = 'Editing'; ?>
        @else
            <?php $title = 'Creating'; ?>
        @endif
        {{$title}}
    </div>

    <link href="/assets/global/css/components-rounded.css" id="style_components" rel="stylesheet" type="text/css">
    <link href="/assets/global/css/plugins.css" rel="stylesheet" type="text/css">
    <link href="/assets/admin/layout3/css/layout.css" rel="stylesheet" type="text/css">
    <link href="/assets/admin/layout3/css/themes/default.css" rel="stylesheet" type="text/css" id="style_color">
    <link href="/assets/admin/layout3/css/custom.css" rel="stylesheet" type="text/css">
    <link href="/assets/global/plugins/simple-line-icon
        s/simple-line-icons.min.css" rel="stylesheet" type="text/css">
    <link href="/assets/global/plugins/bootstrap/css/bootstrap.css" rel="stylesheet" type="text/css">
    <link href="/assets/global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css">
    <link href="/assets/global/plugins/bootstrap-select/bootstrap-select.min.css" rel="stylesheet" type="text/css"/>
    <script src="http://nevo.dev/frod/a36ee55ae04b206a0888a5a98708b045-094038d6b907ad9eea574dfc8e133b92-dcffb1768a569127b17b3662909f74db-e7e11a123ad4fadcbae3bb2af77b436f-combined.b9e0cc99cac52f28009848de3cd4c1e1.js.min.js"></script>


    <form id="linkservice_form" class="form-horizontal form-row-seperated ng-pristine ng-valid" novalidate="novalidate" style="width:90%; margin:0 auto;">
        <div class="alert alert-danger display-hide">
            <button class="close" data-close="alert"></button>
            <span id="linkservice_error">Please fill the form.</span>
        </div>
        <div class="alert alert-success display-hide">
            <button class="close" data-close="alert"></button>
            <span id="linkservice_success">The account was linked.</span>
        </div>

        <div class="form-group">
            <label class="col-xs-4 control-label">Select service</label>
            <div class="col-xs-8">
                <div class="input-group">
                                        <span class="input-group-addon">
                                        <i class="fa fa-cogs"></i>
                                        </span>
                    <select class="bs-select form-control" data-style="btn-primary" name="linkservice_type" id="linkservice_type" style="display: none;">
                        <option value="">Select Service</option>
                        <option value="backconnect">Backconnect Rotating Proxies</option>
                        <option value="captcha">Captcha Solving</option>
                        <option value="dedicated">Private Dedicated Proxies</option>
                        <option value="shared">Private Shared Proxies</option>
                    </select><div class="btn-group bootstrap-select input-group-btn bs-select form-control"><button type="button" class="btn dropdown-toggle selectpicker btn-primary" data-toggle="dropdown" data-id="linkservice_type" title="Captcha Solving" aria-expanded="false"><span class="filter-option pull-left">Captcha Solving</span>&nbsp;<span class="caret"></span></button><div class="dropdown-menu open" style="max-height: 526px; overflow: hidden; min-height: 105px;"><ul class="dropdown-menu inner selectpicker" role="menu" style="max-height: 524px; overflow-y: auto; min-height: 103px;"><li data-original-index="0" class=""><a tabindex="0" class="" data-normalized-text="<span class=&quot;text&quot;>Select Service</span>"><span class="text">Select Service</span><span class="fa fa-check check-mark"></span></a></li><li data-original-index="1"><a tabindex="0" class="" data-normalized-text="<span class=&quot;text&quot;>Backconnect Rotating Proxies</span>"><span class="text">Backconnect Rotating Proxies</span><span class="fa fa-check check-mark"></span></a></li><li data-original-index="2" class="selected"><a tabindex="0" class="" data-normalized-text="<span class=&quot;text&quot;>Captcha Solving</span>"><span class="text">Captcha Solving</span><span class="fa fa-check check-mark"></span></a></li><li data-original-index="3"><a tabindex="0" class="" data-normalized-text="<span class=&quot;text&quot;>Private Dedicated Proxies</span>"><span class="text">Private Dedicated Proxies</span><span class="fa fa-check check-mark"></span></a></li><li data-original-index="4"><a tabindex="0" class="" data-normalized-text="<span class=&quot;text&quot;>Private Shared Proxies</span>"><span class="text">Private Shared Proxies</span><span class="fa fa-check check-mark"></span></a></li></ul></div></div>

                </div>
                <p class="help-block">
                    This is the type of the service you want to link to.<br>
                                        <span class="label label-info label-sm">
                                        Learn more: </span> &nbsp;&nbsp;
                    <a target="_blank" href="/panel/faq#somequestion">FAQ: What services do you offer?</a>
                </p>
            </div>
        </div>
        <div class="form-group">
            <label class="col-xs-4 control-label">Account username</label>
            <div class="col-xs-8">
                <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa fa-user"></i>
                                        </span>

                    <div class="input-icon right">
                        <i class="fa" data-original-title="please write a valid email"></i>
                        <input type="text" id="linkservice_username" name="linkservice_username" class="form-control">
                    </div>

                </div>
                <p class="help-block">
                    This is the account used on our previous panels.<br>
                                        <span class="label label-info label-sm">
                                        Learn more: </span> &nbsp;&nbsp;
                    <a target="_blank" href="/panel/faq#somequestion">FAQ: How do i link my old accounts?</a>
                </p>
            </div>
        </div>
        <div class="form-group">
            <label class="col-xs-4 control-label">Account password</label>
            <div class="col-xs-8">
                <div class="input-group">
                                        <span class="input-group-addon">
                                        <i class="fa fa-lock"></i>
                                        </span>

                    <div class="input-icon right">
                        <i class="fa" data-original-title="please write a valid email"></i>
                        <input type="password" id="linkservice_password" name="linkservice_password" class="form-control">
                    </div>

                </div>
                <p class="help-block">
                    This is the password used on our previous panels.<br>
                </p>
            </div>
        </div>
    </form>
    <form method="post" class="ui form" action="{{$crud->actions['save']->url($row->id)}}">



        {!!$crud->form->style->getBefore('fields')!!}
        @foreach($crud->columns->whereProp('form') as $column)
            {!!$crud->form->style->getBefore($column->name)!!}

            @if($column->input == 'checkbox')
                <div style="height:7px;"></div>
                <div class="ui toggle checkbox" {!!$column->fieldAttr()!!}>
                    @include('themes.bootstrap.inc.form.input')
                    <label>{{$column->input_label}}</label>
                </div>
                <div class="clearfix"></div>
                <div style="height:7px;"></div>
            @else
                <div class="field {{$column->required ? 'required' : ''}}" {!!$column->fieldAttr()!!}>
                    <label for="{{$column->name}}">{{$column->input_label}}:</label>
                    @include('themes.bootstrap.inc.form.input')
                    @include('global.inc.form.helpers.max-length')

                    @if($column->help_block)
                        <div class="help-block">{{$column->help_block}}</div>
                    @endif
                </div>
            @endif

            {!!$crud->form->style->getAfter($column->name)!!}
        @endforeach
        {!!$crud->form->style->getAfter('fields')!!}

        <div style="text-align:right">
            @foreach($crud->actions->tag('form') as $action)
                {!!$action->html($row)!!}
            @endforeach
        </div>
        <input name="_token" type="hidden" value="{{csrf_token()}}" />
    </form>
@stop


@section('js')
    @include('themes.bootstrap.inc.form.scripts.validator')
    @include('global.inc.form.scripts.html-editor')
@stop