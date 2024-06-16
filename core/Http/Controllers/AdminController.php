<?php

namespace Core\Http\Controllers;

use Core\Application\Repository\WithRepository;
use Core\Application\Service\WithService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

abstract class AdminController extends Controller
{
    use WithRepository, WithService, AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
