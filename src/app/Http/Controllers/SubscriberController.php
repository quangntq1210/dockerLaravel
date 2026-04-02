<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Interfaces\SubscriberRepositoryInterface;
use App\Http\Controllers\ApiController;

class SubscriberController extends ApiController
{
    protected $subscriberRepo;

    /**
     * Constructor
     * @param SubscriberRepositoryInterface $subscriberRepo
     */
    public function __construct(SubscriberRepositoryInterface $subscriberRepo)
    {
        $this->subscriberRepo = $subscriberRepo;
    }

    /**
     * Search subscribers by name or email (AJAX endpoint)
     * GET /admin/subscribers/search?q=...
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $query = trim($request->input('q', ''));
        if (strlen($query) < 2) {
            return response()->json([]);
        }
        $subscribers = $this->subscriberRepo->search($query);
        // Return plain array to be used by JS blade directly
        return response()->json($subscribers);
    }
}
