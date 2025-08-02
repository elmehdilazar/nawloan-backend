<?php

use App\Http\Controllers\API\BaseController;
use App\Libraries\GoogleFirebase;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $database;
    protected $repository;
    protected $auth;
    protected $node;

    public function __construct(string $node = null)
    {
        $firebaseService = new GoogleFirebase();
        $this->database = $firebaseService->database;
        $this->auth = $firebaseService->auth;
        $this->node = $node;
        $this->repository = $this->database->getReference($node);
    }
    public function setExtendReference($extend)
    {
        $this->repository = $this->database->getReference($this->node.'/'.$extend);
    }
}

