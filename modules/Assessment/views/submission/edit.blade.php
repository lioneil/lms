@extends('layouts::admin')

@section('head:title') @lang('Edit Submission') @endsection
@section('page:title') @lang('Edit Submission') @endsection

@section('page:content')
  @form(['action' => route('submissions.update', $resource->id)])
    @method('put')

    <div>Author: {{ $resource->user->displayname }}</div>
    {{ $resource->results }}
    @field('text', ['name' => 'results', 'label' => 'Results', 'value' => $resource->results])
    {{ $resource->remarks }}
    @field('text', ['name' => 'remarks', 'label' => 'Remarks', 'value' => $resource->remarks])

    @submit('Update Submission')
    button: Update Submission
  @endform
@endsection
