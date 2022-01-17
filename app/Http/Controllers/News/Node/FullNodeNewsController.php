<?php

namespace App\Http\Controllers\News\Node;

use App\Http\Requests\News\Node\FullNodeNewsCreateRequest;
use App\Models\News\Node\FullNodeArticle;
use App\Repositories\News\Node\FullNodeNewsRepository;
use App\Traits\VerifyRecaptchaTrait;

class FullNodeNewsController extends BaseController
{
    /**
     * Трейт с функцией bool checkRecaptcha($token, $ip)
     */
    use VerifyRecaptchaTrait;

    /**
     * @var FullNodeNewsRepository
     */
    private $fullNodeNewsRepository;

    /**
     * FullNodeNewsController constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->fullNodeNewsRepository = app(FullNodeNewsRepository::class);
    }

    /**
     * Get specific full news data (by it's hash).
     *
     * @param $hash
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($hash)
    {
        $fullNewsArticle = $this->fullNodeNewsRepository->getByHash($hash);

        if (!isset($fullNewsArticle)) {
            abort('404');
        }

        $content_json = $fullNewsArticle->content_json;

        return response()->json($content_json, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param FullNodeNewsCreateRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(FullNodeNewsCreateRequest $request)
    {
        $userID = $request->user_id;
        if ($userID != auth()->guard('api')->user()->id) {
            abort(401);
        }

        $data = [
            'hash' => $request->hash,
            'content_json' => $request->content_json
        ];

        $encodedNewsPost = FullNodeArticle::create($data);

        if ($encodedNewsPost) {
            return response()->json(['message' => 'Your full news has been successfully added to NewNews node!'], 200);
        }
    }
}
