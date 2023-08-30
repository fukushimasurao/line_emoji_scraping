<?php

namespace App\Http\Controllers;

use App\Contracts\CrawlerFactory;
use App\Models\Article;
use App\Http\Requests\DownloadUrlRequest;
use Illuminate\Http\Request;
use Symfony\Component\DomCrawler\Crawler;

class ScrapingController extends Controller
{
    protected $crawlerFactory;

    public function __construct(CrawlerFactory $crawlerFactory)
    {
        $this->crawlerFactory = $crawlerFactory;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('index');
    }

    /**
     * URLから対象の画像を取得して、zip化する。
     * もうちょっと行数増えたらfatになるので処理を切り分けたい。
     */
    public function download(DownloadUrlRequest $request, \GuzzleHttp\Client $client, \ZipArchive $zip)
    {
        $target = $request->validated();
        $response = $client->request('GET', $target['target_url']);
        $crawler = $this->crawlerFactory->createFromContent($response->getBody()->getContents());

        $images = $crawler->filter('.mdCMN09Image')->each(function (Crawler $node) {
            $style = $node->attr('style');
            preg_match('/background-image:url\((.*?)\);/', $style, $matches);
            return $matches[1] ?? null;
        });
        $uniqueImages = array_unique($images);

        $zipFileName = 'images.zip';
        $zip->open($zipFileName, $zip::CREATE | $zip::OVERWRITE);

        foreach ($uniqueImages as $uniqueImage) {
            $imageContent = $client->get($uniqueImage)->getBody()->getContents();
            $path = parse_url($uniqueImage, PHP_URL_PATH);
            $imageName = $target['img_name'] . pathinfo($path, PATHINFO_BASENAME);
            $zip->addFromString($imageName, $imageContent);
        }

        $zip->close();
        return response()->download($zipFileName)->deleteFileAfterSend(true);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Article $article)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Article $article)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Article $article)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article)
    {
        //
    }
}
