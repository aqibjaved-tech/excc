<?php


namespace App\Http\Controllers;
use App\Repositories\NewsRepositoryInterface;


class NewsController extends Controller
{
    protected $repository;

    public function __construct(NewsRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function store()
    {
        $input = 'imapact2';
        $news = $this->repository->all($input);
    }
}
