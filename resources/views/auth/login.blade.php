@extends('layouts::auth')

@section('page:content')
  @container(['attr' => 'fill-height'])
    @layout(['attr' => 'justify-center align-center'])
      @flex(['attr' => 'md6 xs12'])
        @brand
        @form(['action' => url('login')])
          @card(['attr' => 'flat', 'class' => 'transparent'])
            @slot('title')
              {{ sprintf(__('Sign in with your %s account'), settings('app:title')) }}
            @endslot
            @slot('body')
              @field('text', [
                'label' => 'Email or Username',
                'name' => 'username',
              ])
              @field('text', [
                'label' => 'Password',
                'name' => 'password',
                'type' => 'password',
                'min' => '6',
              ])
              @field('checkbox', [
                'label' => 'Remember',
                'name' => 'remember',
              ])
              @submit('Login', ['class' => 'primary'])
            @endslot
          @endcard
        @endform
      @endflex

      @flex(['attr' => 'md5 xs12  offset-md1'])
        @illustration('login', ['width' => '100%', 'height' => '100%'])
        {{-- @animation('treeswing.gif', [
          'width'=> '500px',
          'height' => '500px'
        ]) --}}
      @endflex
    @endlayout
  @endcontainer
@endsection
