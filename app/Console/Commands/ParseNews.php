<?php

namespace App\Console\Commands;

use App\Models\ContentNews;
use App\Models\News;
use Illuminate\Console\Command;


class ParseNews extends Command
{

    protected $signature = 'parse-news:start';
    protected $description = 'start jobs news parse';

    private $countGetNews = 10;

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        foreach (News::$languages as $language){
            $url = $this->prepareLink(News::$urlMask, $language);
            $contentXml = $this->getXmlContent($url);

            if(!$contentXml){
                continue;
            }

            if ($contentXml instanceof \SimpleXMLElement)
            {
                for ($i = 0; $i < $this->countGetNews; $i++){
                    $newsId = $this->getNewsIdByUrl($contentXml->channel->item[$i]->link);

                    if($this->newsNotIsset($newsId)){
                        $image = (string) $contentXml->channel->item[$i]->enclosure['url'];
                        $this->createNews($newsId, $image);
                    }

                    $this->saveContentNews($contentXml->channel->item[$i], $newsId, $language);
                }
            }
        }

        exit();
    }

    /***
     * @param string $url
     * @return int
     */
    private function getNewsIdByUrl(string $url) :int
    {
        $arr = explode('/', parse_url($url,PHP_URL_PATH));
        $id_news = 0;

        foreach ($arr as $segment){
            $segment = (int) $segment;
            if($segment){
                $id_news = $segment;
            }
        }

        return $id_news;
    }

    /***
     * @param $imagePath
     * @return mixed|string|void
     */
    private function copyImage($imagePath){

        $infoImage = pathinfo($imagePath);

        if(is_file(base_path('storage/app/public/news_image/' . $infoImage['basename']))){
            return;
        }

        if (!is_dir(News::$image_path)) {
            mkdir(News::$image_path, 0755);
        }

        if(\File::copy($imagePath, base_path(News::$image_path . "/" . $infoImage['basename']))){
            return $infoImage['basename'];
        }

        return;
    }

    /**
     * @param int $id
     * @return bool
     */
    private function newsNotIsset(int $id) :bool
    {
        return (new News)->where('news_id', $id)->first() ? false : true;
    }

    /**
     * @param int $id
     * @param $imagePath
     * @return bool
     */
    private function createNews(int $id, $imagePath)
    {
        $imageLocalName =  $this->copyImage($imagePath);

        $news = new News();
        $news->news_id = $id;
        $news->image_local_name = $imageLocalName;
        $news->image_origin_path = $imagePath;
        return  $news->save();
    }

    /**
     * @param $news
     * @param int $newsId
     * @param string $language
     * @throws \Exception
     */
    private function saveContentNews($news, int $newsId, string $language)
    {
        $objDateNews = new \DateTime((string) $news->pubDate);
        $modelName = "\\App\\Models\\" . ContentNews::$modelsByLanguage[$language];
        $newsContent = new $modelName();

        if((new $newsContent)->where('news_id', $newsId)->first()){
            return;
        }

        $newsContent->news_id = $newsId;
        $newsContent->title = (string) $news->title;
        $newsContent->url = (string) $news->link;
        $newsContent->description = $this->stripTags($news->description);
        $newsContent->date_public = $objDateNews->format("Y-m-d H:i:s");

        return  $newsContent->save();
    }

    /**
     * @param string $string
     * @return string|string[]|null
     */
    private function stripTags(string $string)
    {
        $pattern = '/<img.*?>/ism';
        return preg_replace($pattern, "", $string);
    }

    /**
     * @param $url
     * @return false|\SimpleXMLElement
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function getXmlContent($url)
    {
        $client = new \GuzzleHttp\Client([
            'timeout' => 15,
        ]);

        try {
            $res = $client->request('GET', $url, ['verify' => false,  'headers' => ['Accept' => 'application/xml']]);
        } catch (\Exception $e) {
            return false;
        }

        if($res->getStatusCode() != 200){
            return false;
        }

        return simplexml_load_string($res->getBody()->getContents());
    }

    /**
     * @param $url
     * @param $dateStr
     * @return string
     */
    private function prepareLink($url, $dateStr) :string
    {
        $urlReturn = '';

        if (preg_match('/\{%(.*?)%\}/i', $url, $matches)) {

            list($placeholder, $format) = $matches;
            if (empty($placeholder) || empty($format)) {
                throw new \InvalidArgumentException('Invalid link or date placeholders:' . $url);
            }
            $urlReturn = str_replace($placeholder, $dateStr, $url);
        }

        return $urlReturn;
    }
}
