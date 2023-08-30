<?php

namespace App\Http\Controllers;

use App\Contracts\CrawlerFactory;
use App\Models\Article;
use App\Http\Requests\DownloadUrlRequest;
use Illuminate\Http\Request;
use Symfony\Component\DomCrawler\Crawler;

class ScrapingController extends Controller
{
    public const ZIP_FILE_NAME = 'images.zip';



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
     * URLから対象の画像を取得して、zip化してダウンロードする。
     * @param DownloadUrlRequest $request
     * @param \GuzzleHttp\Client $client
     * @param \ZipArchive $zip
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
    */
    public function download(DownloadUrlRequest $request, \GuzzleHttp\Client $client, \ZipArchive $zip)
    {
        $target = $request->validated();
        $target_url = $target['target_url'];
        $prefix = $target['target_prefix'];

        $fetch_url = $this->fetchHtml($target_url, $client);

        $uniqueImages = $this->fetchImageUrls($fetch_url);
        $zipFileName = $this->createZipFile($zip, $uniqueImages, $prefix, $client);
        return response()->download($zipFileName)->deleteFileAfterSend(true);
    }

    /**
     * 対象のURL先のHTML等情報を取得する。
     * @param array $target
     * @param \GuzzleHttp\Client $client
     * @return Crawler
     */
    private function fetchHtml($target, $client)
    {
        $response = $client->request('GET', $target);
        return $this->crawlerFactory->createFromContent($response->getBody()->getContents());
    }

    /**
     * 対象のURL先の画像URLを取得する。
     * @param Crawler $fetch_url
     * @return array
     */
    private function fetchImageUrls($fetch_url): array
    {
        $images = $fetch_url->filter('.FnStickerPreviewItem')->each(function (Crawler $node) {
            $data_preview = $node->attr('data-preview');
            if (preg_match('/"https?:\/\/[^"]*_animation\.png[^"]*"/', $data_preview, $matches)) {
                return trim($matches[0], '"') ?? null;
            }

            $style = $node->attr('style');
            preg_match('/background-image:url\((.*?)\);/', $style, $matches);
            return $matches[1] ?? null;
        });
        return  array_unique($images);
    }

    private function fetchImageUrls2($fetch_url): array
    {
        $images = $fetch_url->filter('.mdCMN09Image')->each(function (Crawler $node) {
            $style = $node->attr('style');
            preg_match('/background-image:url\((.*?)\);/', $style, $matches);
            return $matches[1] ?? null;
        });
        return  array_unique($images);
    }

    /**
     * 対象の画像をzip化する。
     *
     * @param \ZipArchive $zip
     * @param array $uniqueImages
     * @param array $target
     * @param \GuzzleHttp\Client $client
     * @return void
     */
    private function createZipFile(\ZipArchive $zip, $uniqueImages, $prefix, $client)
    {
        $zipFileName = self::ZIP_FILE_NAME;

        $zip->open($zipFileName, $zip::CREATE | $zip::OVERWRITE);

        foreach ($uniqueImages as $uniqueImage) {
            $imageContent = $client->get($uniqueImage)->getBody()->getContents();
            $path = parse_url($uniqueImage, PHP_URL_PATH);
            $imageName = $prefix . pathinfo($path, PATHINFO_BASENAME);
            $zip->addFromString($imageName, $imageContent);
        }

        $zip->close();
        return $zipFileName;
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
