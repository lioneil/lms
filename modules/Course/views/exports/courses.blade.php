<table>
  <thead>
    <tr><th cell="A1"></th></tr>
    <tr><th cell="A2">{{ settings('app:title') }}</th></tr>
    <tr>
      <th cell="A3">@lang('Title')</th>
      <th cell="A3">@lang('Subtitle')</th>
      <th cell="B3">@lang('Author')</th>
    </tr>
  </thead>
  <tbody>
    @foreach($courses as $course)
      <tr>
        <td></td>
        <td>{{ $course->title }}</td>
        <td>{{ $course->title }}</td>
        <td>{{ $course->subtitle }}</td>
        <td>{{ $course->author }}</td>
        <td></td>
      </tr>
    @endforeach
  </tbody>
</table>
