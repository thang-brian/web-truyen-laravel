<?php

namespace App\Console\Commands\TruyenChu;

use Illuminate\Console\Command;
use Goutte\Client;
use Symfony\Component\HttpClient\HttpClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\Story;
use App\Models\Chapter;
use App\Models\CategoryStory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CrawlerDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'truyenchu:crawler';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function __construct(){
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->init();
    }

    protected function init()
    {
        // $this->crawlerListChapter(1, 'long-vuong-truyen-thuyet-dau-la-dai-luc-3', 'Long Vương Truyền Thuyết (Đấu La Đại Lục 3)');
        $this->crawlerListStory();
    }

    protected function crawlerListStory()
    {
        // for ($i = 0; $i < 2000 ; $i++) {
        for ($i = 1; $i < 3 ; $i++) {
            sleep(5);
            $html = file_get_html(env('TRUYENCHU_URL') . '/danh-sach/truyen-moi?page=' . $i);
            $this->warn("Crawl list story page: " . $i);
            $listStory = $html->find("div.list.list-truyen.col-xs-12 > div > div.col-xs-9 > div > h3 > a");
            if (count($listStory) == 0) {
                break;
            } else {
                foreach ($listStory as $story) {
                    $this->crawlerStory($story->href);
                }
            }
            dd($listStory);
        }
    }

    protected function crawlerStory($slug)
    {
        try {
            $linkContent = env('TRUYENCHU_URL') . $slug;
            // $linkContent = "https://truyenchu.vn/ta-sinh-con-cho-tong-tai";
            $client = new Client();
            sleep(5);
            $crawler = $client->request('GET', $linkContent);

            // $avatarUrl = env('TRUYENCHU_URL') . $crawler->filter('div.col-xs-12.col-sm-4.col-md-4.info-holder > div > div > img')->eq(0)->attr('src');
            // $contentAvatar = file_get_contents($avatarUrl);
            // $name = substr($avatarUrl, strrpos($avatarUrl, '/') + 1);
            // Storage::disk('public')->put('uploads/' . $name, $contentAvatar);
            // dd($name);

            $typeRaw = $crawler->filter('div.col-xs-12.col-sm-8.col-md-8.desc > div.info > div:nth-child(4) > span')->text();
            switch ($typeRaw) {
                case 'Đang ra':
                    $type = 1;
                    break;
                case 'Hoàn thành':
                    $type = 3;
                    break;
                default:
                    $type = 2;
                    break;
            }
            $title = $crawler->filter('div.col-xs-12.col-info-desc > h1 > a')->text();
            $story = Story::firstOrCreate(
                [
                    'title' => $title
                ],
                [
                    'title' => $title,
                    'avatar' => $crawler->filter('div.col-xs-12.col-sm-4.col-md-4.info-holder > div > div > img')->eq(0)->attr('src') ?? null,
                    'author' => $crawler->filter('div.info > div:nth-child(1) > a > span')->text(),
                    'type' => $type,
                    'content' => $crawler->filter('div.desc-text')->text()
                ]
            );
            $this->warn("Crawl story: " . $title);
            // $story->categories->sync($newCategories);

            // $newCategoriesId = array();
            $crawler->filter('div.info > div:nth-child(3) > a')->each(function ($category) use ($story) {
                $category = $category->text();
                $newCategory = Category::firstOrCreate(
                    [
                        'name' => $category
                    ],
                    [
                        'name' => $category
                    ]
                );
                $this->warn("Crawl category: " . $category);
                // $newCategoriesId[] = $newCategory->id;

                $newCategory->story()->sync($story->id);

                // $categoryStory = CategoryStory::firstOrCreate(
                //     [
                //         'category_id' => $newCategory->id,
                //         'story_id' => $story->id
                //     ],
                //     [
                //         'category_id' => $newCategory->id,
                //         'story_id' => $story->id
                //     ]
                // );
            });
            // $story->categories()->sync($newCategoriesId);
            $this->crawlerListChapter($story->id, $slug, $story->title);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return null;
        }
    }

    protected function crawlerListChapter($storyId, $slug, $title)
    {
        // for ($i = 0; $i < 500 ; $i++) {
        for ($i = 1; $i < 3 ; $i++) {
            $url = env('TRUYENCHU_API_URL') . '/services/list-chapter';
            $data = [
                'type' => 'list_chapter',
                'tascii' => $slug,
                'tname' => $title,
                'page' => $i,
                'totalp' => 0,
            ];
            sleep(5);
            $listChapters = Http::get($url, $data);

            $this->warn("Crawl list chapter of story " . $title . " page " . $i);

            $data = json_decode($listChapters->getBody()->getContents());
            $totalChaps = $data->total;
            $story = Story::where('id', $storyId)->first();
            if (!empty($story)) {
                if ($story->total_chapters == null || $story->total_chapters < $totalChaps) {
                    $story->total_chapters = $totalChaps;
                    $story->save();
                }
            }
            
            $chapList = $data->chap_list;
            if (empty($chapList)) {
                break;
            } else {
                $listChapters = explode("<a href=\"",strip_tags($chapList, '<a>'));
                foreach ($listChapters as $key => $chapter) {
                    if (!empty($chapter)) {
                        $listChapters[$key] = explode("\" ", $chapter);
                        $this->crawlerChapter($storyId, $listChapters[$key][0]);
                    }
                }
            }
        }
    }

    protected function crawlerChapter($storyId, $slugChap)
    {
        $linkContent = env('TRUYENCHU_URL') . $slugChap;
        $client = new Client();
        sleep(5);
        $crawler = $client->request('GET', $linkContent);
        $content = $crawler->filter('#chapter-c')->text();
        $title = $crawler->filter('#chapter-big-container div div h2 a span')->text();
        $chap = Chapter::firstOrCreate(
            [
                'title' => $title,
                'story_id' => $storyId
            ],
            [
                'title' => $title,
                'content' => $content,
                'story_id' => $storyId
            ]
        );
        $this->warn("Crawl chapter: " . $title);
    }
}
