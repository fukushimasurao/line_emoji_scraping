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

        $bodyContents = $this->fetchBodyContents($target_url, $client);

        $uniqueImages = $this->fetchImageUrls($bodyContents);
        $zipFileName = $this->createZipFile($zip, $uniqueImages, $prefix, $client);
        return response()->download($zipFileName)->deleteFileAfterSend(true);
    }

    /**
     * 対象のURL先のHTML等情報を取得する。
     * @param array $target
     * @param \GuzzleHttp\Client $client
     * @return Crawlerssss
     */
    private function fetchBodyContents($target, $client)
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

            $style = $node->filter('.mdCMN09Image')->attr('style');
            if (preg_match('/url\((.*?)\)/', $style, $matches)) {
                // URLが見つかった場合は、それを返す
                return trim($matches[1], '"');
            }
        });
        return array_filter($images);
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
        $zipFileName = self::getZipFileName();

        $zip->open($zipFileName, $zip::CREATE | $zip::OVERWRITE);

        foreach ($uniqueImages as $index => $uniqueImage) {
            $imageContent = $client->get($uniqueImage)->getBody()->getContents();
            $path = parse_url($uniqueImage, PHP_URL_PATH);

            $baseName = pathinfo($path, PATHINFO_BASENAME);

            // $indexを3桁の整数にする。意味ないけどかっこいいから。
            $index = sprintf('%03d', $index);

            // スタンプの場合は、stickerが命名されている。
            if ($baseName === 'sticker.png') {
                $imageName = $prefix . '_' . $index . '_' . $baseName;
            } else {
                $imageName = $prefix . '_' . $baseName;
            }

            $zip->addFromString($imageName, $imageContent);
        }

        $zip->close();
        return $zipFileName;
    }

    /**
     * ZIPファイル名を取得(現在の日付と時間)
     *
     * @return string
     */
    private static function getZipFileName(): string
    {
        return 'images_' . now()->format('Ymd_His') . '.zip';
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
