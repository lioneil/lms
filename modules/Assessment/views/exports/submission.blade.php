<table>
  <thead>
    <tr><th cell="A1"></th></tr>
    <tr><th cell="A2">{{ settings('app:title') }}</th></tr>
    <tr>
      <th cell="A3">@lang('Results')</th>
      <th cell="A3">@lang('Remarks')</th>
      <th cell="B3">@lang('Author')</th>
    </tr>
  </thead>
  <tbody>
    @foreach($submissions as $submission)
      <tr>
        <td>{{ $submission->results }}</td>
        <td>{{ $submission->remarks }}</td>
        <td>{{ $submission->author }}</td>
        <td></td>
      </tr>
    @endforeach
  </tbody>
</table>
